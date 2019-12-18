<?php

namespace App;

class User extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct(){
        if(!$user = \App\Cookie::i()->username)
            return;
        
        $query = 'SELECT * FROM users WHERE name = ?;';
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute([$user]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if($this->checkPassword($user['password'],\App\Cookie::i()->password)){
            $this->data = $user;
        }
    }
    
    protected function checkPassword($need,$pass){
        return $pass == $this->createPassword($need);
    }
    
    public function createPassword($password){
        return md5($password);
    }
    
    public function createCsrfToken(){
        if(!$this->csrf){
            $this->csrf = $this->getNewCsrf();
            $this->saveCsrf();
        }
        return $this->csrf;
    }
    
    public function checkCsrf($csrf = null){
        $csrf = $csrf ?? \App\Request::i()->get('csrf');
        return $this->isLogged() AND $csrf == $this->csrf;
    }
    
    private function getNewCsrf(){
        return md5( $this->name . time() );
    }

    public function isLogged(){
        return !is_null($this->id);
    }
    
    private function saveCsrf(){
        if($this->isLogged()){
            $query = 'UPDATE users SET `csrf`=? WHERE id=?;';
            $stmt = \App\Db::i()->prepare($query);
            $stmt->execute([$this->csrf,$this->id]);
        }
    }
}