<?php

namespace Modules\core\windows;

use App\Window\AbstractWindow;

class Chat extends AbstractWindow
{
    protected $ip;
    protected $isBanned;
    protected $lastChat;
    
    protected $interval;
    
    protected $default_period = 10;
    protected $default_count = 25;
    
    protected $count;
    
    protected $time;
    
    public $isOpen = true;
    
    public function __construct($params = null)
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        
        $stmt = \App\Db::i()->prepare("SELECT count(*) AS c FROM banned_chat_ip WHERE `ip` = ? UNION (SELECT datetime FROM chat WHERE `ip` = ? AND datetime > ? ORDER BY datetime DESC LIMIT 1);");
        
        $period = \App\Settings::i()->chat_period_minutes ?? $this->default_period;
        $this->interval = new \DateInterval("PT{$period}M");
        $time = (new \DateTime())->sub($this->interval)->format('Y-m-d H:i:s');
        
        $stmt->execute([$this->ip,$this->ip,$time]);
        
        $this->isBanned = (boolean) $stmt->fetch(\PDO::FETCH_ASSOC)['c'];
        $this->lastChat = $stmt->fetch(\PDO::FETCH_ASSOC)['c'];
        
        $this->time = date('Y-m-d H:i:s',time());
        
        $this->count = \App\Settings::i()->chat_message_count ?? $this->default_count;
    }
    
    protected function saveChatToken($token,$ip=null)
    {
        $ip = $ip ?? $this->ip;
        
        $stmt = \App\Db::i()->prepare('DELETE FROM chat_tokens WHERE `ip` = ?;');
        $stmt->execute([$ip]);
        
        $stmt = \App\Db::i()->prepare('INSERT INTO chat_tokens SET `ip` = ?, `token` = ?;');
        $stmt->execute([$ip,$token]);
    }
    
    protected function getChatToken($ip=null)
    {
        $ip = $ip ?? $this->ip;
        $stmt = \App\Db::i()->prepare('SELECT token FROM chat_tokens WHERE `ip` = ?;');
        $stmt->execute([$ip]);
        
        return $stmt->rowCount() ? $stmt->fetch(\PDO::FETCH_ASSOC)['token'] : '';
    }
    
    protected function submitMessage()
    {
        if(!isset($_POST['chat_message']) OR !$message = $this->strip(trim($_POST['chat_message'])))
            return 'Не введено сообщение';
        
        if(!isset($_POST['csrf']) OR !$_POST['csrf'] OR $_POST['csrf'] !== $this->getChatToken())
            return 'Ошибка';
        
        $name = $_POST['chat_author'] ? $this->strip($_POST['chat_author']) : \App\I18n::i()->translate('chat_anonim',['domain'=>'chat']);
        
        $type = \App\User::i()->role == 'admin' ? 'Admin' : 'guest';
        
        $stmt = \App\Db::i()->prepare('INSERT INTO chat SET `name` = ?, `text` = ?, `ip` = ?, `datetime` = ?, `type` = ?;');
        $stmt->execute([$name,$message,$this->ip,$this->time,$type]);
        
        return false;
    }
    
    protected function strip($string)
    {
        return $string;
        //return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    
    protected function getNavMode()
    {
        if($this->isBanned) return 'banned';
        if(!$this->lastChat OR \App\User::i()->role == 'admin'){
            if(isset($_POST['submit'])){
                $error = $this->submitMessage();
                if(!$error){
                    $message = \App\I18n::i()->translate('chat_success',['domain'=>'chat']);
                    return '<div class="mt05">' . $message . '</div>';
                }
            }
            $csrf = \App\User::i()->getNewCsrf();
            $this->saveChatToken($csrf);
            
            return \App\Twig::i()->render('chat_form.twig',[
                'error' => $error ?? '',
                'csrf' => $csrf,
                'name' => \App\Cookie::i()->chat_name ?? \App\User::i()->name ?? ''
            ]);
        }
        $time = new \DateTime($this->lastChat);
        $time->add($this->interval);
        $r = $time->diff(new \DateTime(),false);
        $minutes = $r->i + ($r->h*60) + ($r->d *60*24);
        
        $message = \App\I18n::i()->translate('chat_no_time',['domain'=>'chat']);
        return '<div class="mt05">' . sprintf($message,$minutes) . '</div>';
    }
    
    protected function deleteMode()
    {
        if(!\App\User::i()->isLogged() OR !\App\User::i()->checkCsrf())
            return '';
        
        if(!isset($_POST['id']) OR !$id = $_POST['id'])
            return '';
        
        if(isset($_POST['del']) AND $_POST['del'] == 'true'){
            $stmt = \App\Db::i()->prepare('SELECT ip FROM chat WHERE id = ?;');
            $stmt->execute([$id]);
            if($stmt->rowCount()){
                $ip = $stmt->fetch(\PDO::FETCH_ASSOC)['ip'];
                $stmt->preapare('INSERT INTO banned_chat_ip SET `ip` = ?,`datetime` = ?;');
                $stmt->execute([$ip,$this->time]);
            }
        }
        
        $stmt = \App\Db::i()->prepare('DELETE FROM chat WHERE `id` = ?;');
        $stmt->execute([$id]);
        
        $stmt = \App\Db::i()->prepare('INSERT INTO chat SET `type` = "delete", `name` = ?, `datetime` = ?;');
        $stmt->execute([$id,$this->time]);
        
        return 'delete';
    }
    
    protected function getNewMode()
    {
        if(!isset($_POST['id']) OR !$id = $_POST['id']) return '';
        
        $stmt = \App\Db::i()->prepare('SELECT * FROM chat WHERE `id` > ? ORDER BY `datetime` DESC LIMIT 0,'.$this->count.';');
        $stmt->execute([$id]);
        
        $new = [];
        $del = [];
        $last = null;
        
        while($one = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $last = $last ?? $one['id'];
            if($one['type'] == 'delete'){
                $del[] = $one['name'];
            }
            elseif(count($new) < $this->count){
                $new[] = \App\Twig::i()->render('chat_row.twig',[
                    'message' => $one
                ]);
            }
        }
        
        if(!$last) return '';
        
        return json_encode([
            'del'=>$del,
            'new'=>$new,
            'last'=>$last
        ]);
    }
    
    public function manage()
    {
        if(isset($_GET['mode']) AND method_exists($this,$_GET['mode'].'Mode')){
            $method = $_GET['mode'].'Mode';
            return $this->$method();
        }
        
        $stmt = \App\Db::i()->query('SELECT * FROM chat WHERE `type` <> "delete" ORDER BY `datetime` DESC LIMIT 0,'.$this->count.';');
        $chat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $last = $chat[0]['id'] ?? -1;
        $chat = array_reverse($chat);
        
        return \App\Twig::i()->render('core_chat.twig',[
            'ip' => $this->ip,
            'banned' => $this->isBanned,
            'chat' => $chat,
            'last' => $last
        ]);
    }
}

// INSERT INTO windows SET `module_id` = 1, `name` = 'Chat', `enabled` = 1;

// CREATE TABLE chat (id INT(10) auto_increment PRIMARY KEY, name VARCHAR(100) NULL, text TEXT NULL, ip VARCHAR(15) NULL, type VARCHAR(50) NOT NULL DEFAULT 'guest', datetime DATETIME);

// CREATE TABLE `chat_tokens` ( `id` int(11) NOT NULL AUTO_INCREMENT, `ip` varchar(15) DEFAULT NULL, `token` varchar(40) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

// CREATE TABLE `banned_chat_ip` ( `id` int(10) NOT NULL AUTO_INCREMENT, `ip` varchar(15) DEFAULT NULL, `datetime` datetime DEFAULT NULL, `reason` text, PRIMARY KEY (`id`), UNIQUE KEY `ip` (`ip`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8