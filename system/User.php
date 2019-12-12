<?php

namespace App;

class User extends Patterns\Singleton
{
    protected static $instance;
    
    protected function __construct(){
        $query = 'SELECT * FROM users WHERE name = ?;';
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute([\App\Cookie::i()->username]);
        
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
        $token = $this->getNewCsrf();
        $this->csrf = $token;
        $this->saveCsrf();
        return $token;
    }
    
    public function checkCsrf($csrf = null){
        $csrf = $csrf ?? \App\Request::i()->get('csrf');
        return $csrf == $this->csrf;
    }
    
    private function getNewCsrf(){
        return md5( $this->name . time() );
    }
    
    public function isLogged(){
        return !is_null(\App\User::i()->id);
    }
    
    private function saveCsrf(){
        $query = 'UPDATE users SET `csrf`=? WHERE id=?;';
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute([$this->csrf,$this->id]);
    }
}