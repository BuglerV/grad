<?php

namespace App;

class Store extends Patterns\Singleton
{
    protected static $instance;
    
    /**
     * Load data from file or return NULL.
     *
     * Param 'name' - name of file to read.
     *
     * Return array of data.
     */
    public function __get($name)
    {
        $fileName = STORE . '/cache/' . $name . '.php';
        return is_file($fileName) ? include($fileName) : null;
    }
    
    /**
     * Save data in file.
     *
     * Param 'fileName' - name of new file.
     * Param 'fileData' - values to save.
     *
     * Return void.
     */
    public function __set($fileName,$fileData)
    {
        $fileName = STORE . '/cache/' . $fileName . '.php';
        $fileData = "<?php\n\nreturn ".var_export($fileData,true).";\n\n//Stored " . date('Y-m-d H:i:s',time());
        
        file_put_contents($fileName,$fileData);
    }
    
    /**
     *
     */
    public function __isset($fileName)
    {
        $fileName = STORE . '/cache/' . $fileName . '.php';
        return is_file($fileName);
    }

    /**
     * Delete file from storage.
     *
     * Param 'fileName' - name of deleted file.
     *
     * Return void.
     */
    public function __unset($fileName)
    {
        $fileName = STORE . '/cache/' . $fileName . '.php';
        
        if(is_file($fileName))
            unlink($fileName);
    }
}