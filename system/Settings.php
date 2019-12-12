<?php

namespace App;

class Settings extends Patterns\Singleton
{
    /**
     * Object.
     */
    protected static $instance;
    
    /**
     * Changed settings.
     */
    protected $change = [];
    
    /**
     * Constructor load settings form file or DB.
     */
    protected function __construct()
    {
        $file = Store::i()->settings;
        if($file){
            $this->data = $file;
        }
        else{
            Logger::i()->log(sprintf('Отсутствует файл настроек модулей "%s".','settings.php'),'warning',['Settings']);
            
            $query = 'SELECT `key`,`value` FROM settings;';
            foreach(Db::i()->query($query,\PDO::FETCH_ASSOC) as $row){
                $this->data[$row['key']] = $row['value'];
            }
            
            Store::i()->settings = $this->data;
        }
        
        Kernel::i()->addListener(Kernel::FINISH,'\\App\\Settings','saveChangedSettings');
    }
    
    /**
     * Set new value to setting.
     *
     * Param 'name' - name of setting.
     * Param 'value' - new value for setting.
     *
     * Return void.
     */
    public function __set($name,$value)
    {
        if(isset($this->data[$name]) AND $this->data[$name] !== $value){
            $this->data[$name] = $value;
            $this->change[$name] = $value;
        }
    }
    
    /**
     * Save changed settings to file and DB.
     *
     * Return void.
     */
    public function saveChangedSettings($settings = null)
    {
        if(!$settings = $settings ?? $this->change)
            return;
        
        $query = 'UPDATE settings SET `value` = ? where `key` = ?';
        $stm = Db::i()->prepare($query);

        foreach($settings as $key => $value){
            $stm->execute([$value,$key]);
        }
        
        Store::i()->settings = $this->data;
    }
}