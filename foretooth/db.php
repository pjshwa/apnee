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
    
    /* Get items from database */
    public function getItems($page='1') {
		// Step 1. Prepare the SQL query 
        $page = (int)$page; if ($page < 1) $page = 1;
        $pg_idx = ((int)$page - 1) * 5;
        $query = "SELECT question_key, question, tries, corrects from questions limit ?,5";


		// Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $pg_idx);



		// Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }


		// Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($id, $q, $t, $c);
        $items = array();
        while($stmt->fetch()) {
           $items[] = array('id'=>$id, 
                            'question'=>$q,
                            'tries'=>$t,
                            'corrects'=>$c);
        }

		// Step 5. Close the connection
        $stmt->close();

		// Step 6. Return the selected $items to the function caller
	   return $items;
	
    }
    public function updateTries($k, $correct) {
        // Step 1. Prepare the SQL query 
        if ($correct) $query = "update questions set tries = tries+1,corrects = corrects+1 where question_key=?";
        else $query = "update questions set tries = tries + 1 where question_key=?";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $k);

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
    }
    public function updateTempQ($q, $a) {
        // Step 1. Prepare the SQL query 
        $query = "insert into temp_questions (question, answer) values (?, ?)";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('ss', $q, $a);

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
    }

    public function getTotalPage() {
        $query = "SELECT count(*) from questions";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }



        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }


        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($ans);
        $item = '';
        while($stmt->fetch()) {
           $item = $ans;
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return (int)$item;
    
    }

    public function getanswer($k) {
        // Step 1. Prepare the SQL query 
        $query = "SELECT answer from questions where question_key=?";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $k);

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }


        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($a);
        $item = '';
        while($stmt->fetch()) {
           $item = $a;
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $item;
    
    }
    
 //    /* Check login information */
 //    public function checkLogin($login, $password) {
	// if(!($stmt = $this->mysqli->prepare('SELECT user_id, user_pw '.
 //                                    'FROM users '.
 //                                    'WHERE user_id=? AND user_pw=?'))) {
 //           throw new Exception('DB Error: '.$this->mysqli->error);
 //        }

 //        $stmt->bind_param('ss', $login, $password);
 //        if(!$stmt->execute()) {
 //            throw new Exception('DB Error: '.$this->mysqli->error);
 //        }

 //        $stmt->bind_result($id, $name);
 //        if($stmt->fetch()) {
 //            $stmt->close();
 //           return true;
 //        } else {
 //            $stmt->close();
 //           return false;
 //        }

	
 //    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);

?>