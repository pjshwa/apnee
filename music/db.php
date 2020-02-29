<?php
include(dirname(__FILE__).'/../credentials.php');

class DB {
    private $mysqli;
    /* Constructor: setup connection */
    public function __construct($host, $user, $pass, $database) {
	    $this->mysqli = new mysqli($host, $user, $pass, $database);
        $this->mysqli->set_charset("utf8");
        if($this->mysqli->connect_errno) {
            throw new Exception('Connect Error: '.$this->mysqli->connect_errno);
        }
    }

    public function articlesOfMonth($year, $month) {
        // Step 1. Prepare the SQL query
        $query = "SELECT id, title, content, created_at from music_logs where YEAR(created_at) = ? AND MONTH(created_at) = ? order by created_at desc";
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('ss', $year, $month);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($id, $title, $content, $date);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
            'id'=>$id,
            'title'=>$title,
            'content'=>$content,
            'date'=>$date,
            );
        }
        $stmt->close();
        return $items;
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);

?>