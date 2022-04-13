<?php
class dbConfig
{
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
    public function dbConfig()
    {
        $this -> serverName = '';
        $this -> userName = '';
        $this -> password = '';
        $this -> dbName = '';
    }
}
