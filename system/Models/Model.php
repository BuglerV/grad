<?php

namespace App\Models;

abstract class Model
{
    protected $mainRow = 'id';
    protected $loadedRows = null;
    
    protected $table = null;
    
    protected $data = [];
    
    public function isLoad(){
        return isset($this->data[$this->mainRow]) AND $this->data[$this->mainRow];
    }
    
    public function __get($key){
        $getter = 'getter_' . $key;
        if(method_exists($this,$getter))
            return $this->$getter();
        else
            return $this->data[$key] ?? null;
    }
    
    public function __set($key,$value){
        $setter = 'setter_' . $key;
        if(method_exists($this,$setter))
            $this->$setter($value);
        else
            $this->data[$key] = $value;
    }
    
    public function save()
    {
        $isLoad = $this->isLoad();
        
        $query = $isLoad ? 'UPDATE' : 'INSERT INTO';
        $query .= " {$this->table} SET ";
        
        $colsArray = $binds = [];
        foreach($this->loadedRows as $row){
            $colsArray[] = "`$row` = ? ";
            $binds[] = $this->$row;
        }
        $query .= implode(', ',$colsArray);
        
        if($isLoad){
            $query .= 'WHERE '. $this->mainRow .' = ?';
            $binds[] = $this->id;
        }
        
        $query .= ';';
        
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute($binds);
        
        return !+$stmt->errorCode();
    }
    
    public function delete()
    { 
        if(!$id = $this->data['id']) return;
        
        $query = "DELETE FROM {$this->table} WHERE {$this->mainRow} = ?;";
        $stmt = \App\Db::i()->prepare($query);

        $stmt->execute([$id]);
        
        return !+$stmt->errorCode();
    }
    
    public function getValues()
    {
        return $this->data;
    }
    
    public function setValues($values)
    {
        if(!is_array($values))
            return;

        foreach($this->loadedRows as $key){
            if(isset($values[$key]))
                $this->$key = $values[$key];
            else
                $this->$key = '';
        }
    }
    
    public function __construct($row = null)
    {
        if(!$row) return;
        
        // Удалить из списка столбцов название главного по сортировке.
        
        $query = 'SELECT '. $this->mainRow .', ' . implode(', ',$this->loadedRows) . " FROM {$this->table} WHERE {$this->mainRow} = ?;";
        $stmt = \App\Db::i()->prepare($query);
        $stmt->execute([$row]);
        
        $this->data = $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}