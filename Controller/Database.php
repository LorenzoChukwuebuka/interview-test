<?php

 
class Database
{
    private $host = "localhost";
    private $database_name = "interview";
    private $username = "root";
    private $password = "";

    public $db;

    public function __construct()
    {
        $this->db = null;
        // Create connection
        $this->db = new \mysqli($this->host, $this->username, $this->password, $this->database_name);

        // Check connection
        if (!$this->db) {
            die("Connection failed: " . mysqli_connect_error());
        }

        return true;
    }

    // Sanitize Inputs
    public function sanitize($data)
    {
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        $data = stripslashes($data);
        $data = trim($data);
        return $data;
    }

    public function message($content)
    {
        return json_encode(['message' => $content]);
    }

    public function out($msg){
        return json_encode($msg);
    }
}
