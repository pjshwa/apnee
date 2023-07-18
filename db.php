<?php
include('credentials.php');

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

    // public function titlesOfEvents() {
    //     $query = "SELECT id, title, created_at from events order by created_at desc limit 5";
    //     if(!($stmt = $this->mysqli->prepare($query))) {
    //         throw new Exception('DB Error: '.$this->mysqli->error);
    //     }
    //     if(!$stmt->execute()) {
    //         throw new Exception('DB Error: '.$this->mysqli->error);
    //     }
    //     $stmt->bind_result($id, $title, $date);
    //     $items = array();
    //     while($stmt->fetch()) {
    //         $items[] = array(
    //                         'id'=>$id,
    //                         'title'=>$title,
    //                         'date'=>$date,
    //                         );
    //     }
    //     $stmt->close();
    //     return $items;
    // }

    public function getEventById($event_id) {
        $query = "SELECT title, content, created_at from events where id = ?";
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $event_id);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($title, $content, $date);
        while($stmt->fetch()) {
            $item = array(
                'title'=>$title,
                'content'=>$content,
                'date'=>$date,
            );
        }
        $stmt->close();
        return $item;
    }

    public function getPika() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT nickname, success, remain_time, hits_score, date(reg_date)
        from pika_score
        order by success desc, remain_time desc, hits_score desc, reg_date desc";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }    
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($nickname, $success, $remain_time, $hits_score, $d);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'nickname'=>$nickname,
                            'success'=>$success,
                            'remain_time'=>$remain_time,
                            'hits_score'=>$hits_score,
                            'date'=>$d,
                            );
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
        return $items;
    }

    public function newPika($nickname, $success, $remain_time, $hits_score) {
        // Step 1. Prepare the SQL query 
        $query = "insert into pika_score(nickname, success, remain_time, hits_score, reg_date) values (?, ?, ?, ?, CURRENT_TIMESTAMP)";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('ssss', $nickname, $success,  $remain_time, $hits_score);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }

    public function insertPhiChat($c) {
        // Step 1. Prepare the SQL query 
        $query = "insert into phi_chats(phi_id, content, reg_date) values (?, ?, CURRENT_TIMESTAMP)";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $i = mt_rand(1,13);
        $stmt->bind_param('ss', $i, $c);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }

    public function getPhiChat() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT PC.content, PC.src, PI.img_src, PI.desc
        from phi_chats PC, phi_info PI
        where PC.phi_id = PI.id
        order by PC.reg_date desc";

        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($content, $src, $img_src, $description);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array('content'=>$content,
                            'src'=>$src,
                            'img_src'=>$img_src,
                            'description'=>$description,
                            );
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $items;
    }

    public function insertVisitorLog($t, $c) {
        // Step 1. Prepare the SQL query 
        $query = "insert into visitor_log (title, content, reg_date) values (?, ?, CURRENT_TIMESTAMP)";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('ss', $t, $c);


        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }

    public function getVisitorLogs() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT title, content, reg_date from visitor_log order by reg_date desc";

        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        // Step 4. Retrieve the result and put them in the $items array
        $stmt->bind_result($title, $content, $d);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array('title'=>$title, 
                            'content'=>$content,
                            'date'=>$d);
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
        return $items;
    }


    public function getDailyLeetCode($date) {
        $query = "SELECT url from daily_problems where reg_date = ?";
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $date);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_result($url);
        $items = array();
        while($stmt->fetch()) {
            $items[] = $url;
        }
        $stmt->close();
        return $items[0];
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);
?>
