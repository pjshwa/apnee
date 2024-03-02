<?php
require(dirname(__FILE__).'/../credentials.php');

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
    public function catCount() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT count(*) from cats";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($ans);
        $item = '';
        while($stmt->fetch()) {
            $item = $ans;
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $item;
    }
    public function getCats($l) {
        // Step 1. Prepare the SQL query 
        $query = "SELECT title, img_src, date(reg_date)
        from cats
        order by reg_date desc limit ?, 10";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $l);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($title, $img_src, $d);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'title'=>$title,
                            'img_src'=>$img_src,
                            'date'=>$d,
                            );
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $items;
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);

?>