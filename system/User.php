<?php

namespace App;

class User extends Patterns\Singleton
{
    protected static $instance;
    
    public function getUserFromDb($name)
    {
        $query = 'SELECT * FROM '. DB_PREFIX .'users WHERE name = ?;';
        
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute([$name]);
        
        return $stmt->rowCount() ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
    }
    
    public function setErrorCount($id,$count)
    {
        $stmt = \App\Db::i()->prepare('UPDATE '. DB_PREFIX .'users SET `error_count` = ? WHERE `id` = ?;');
        return $stmt->execute([$count,$id]);
    }
    
    protected function __construct(){
        if(!$user = \App\Cookie::i()->username)
            return;
        
        $user = $this->getUserFromDb($user);
        
        if($this->checkPassword($user['password'],(string)\App\Cookie::i()->password)){
            $this->data = $user;
        }
    }
    
    public function checkPassword($need,$pass){
        return $need === $this->createPassword($pass);
    }
    
    public function createPassword($password){
        return md5($password);
    }
    
    public function createCsrfToken(){
        if(!$this->isLogged()) return '';

        $this->csrf = $this->getNewCsrf();
        $this->saveCsrf();
        
        \App\Cookie::i()->ckCsrfToken = $this->csrf;
        
        return $this->csrf;
    }
    
    public function getCsrf(){
        if(!$this->isLogged()) return '';
        
        return $this->csrf ?: $this->createCsrfToken();
    }
    
    public function checkCsrf($csrf = null){
        $csrf = $csrf ?? \App\Cookie::i()->ckCsrfToken ?? null;
        
        return $this->isLogged() AND $csrf AND $csrf === $this->csrf;
    }
    
    public function getNewCsrf(){
        return '====' . md5( ($this->name ?? self::getRandomString()) . time() ) . '====';
    }
    
    public static function getRandomString($len=32)
    {
        $string = '';
        for($i=0; $i<$len; $i++){
            $string .= chr( rand(0,25) + 97 );
        }
        return $string;
    }

    public function isLogged(){
        return !is_null($this->id);
    }
    
    private function saveCsrf(){
        if($this->isLogged()){
            $query = 'UPDATE '. DB_PREFIX .'users SET `csrf`=? WHERE id=?;';
            $stmt = \App\Db::i()->prepare($query);
            $stmt->execute([$this->csrf,$this->id]);
        }
    }
}