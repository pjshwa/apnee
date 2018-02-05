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
    public function newHTMLText($content) {
        // Step 1. Prepare the SQL query 
        $query = "insert into poll_20170424(response) values (?)";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $content);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }
    public function newProjectItem($content) {
        // Step 1. Prepare the SQL query 
        $query = "insert into project(content) values (?)";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $content);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }
    public function getProjectItems() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT content, reg_date
        from project
        order by reg_date desc";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }    
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($content, $reg_date);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'content'=>$content,
                            'reg_date'=>$reg_date,
                            );
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
        return $items;
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
        $query = "insert into pika_score(nickname, success, remain_time, hits_score) values (?, ?, ?, ?)";
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
    public function newFish($user_id, $title, $content) {
        // Step 1. Prepare the SQL query 
        $query = "insert into fish(user_id, title, content) values (?, ?, ?)";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('sss', $user_id, $title,  $content);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        // Step 5. Close the connection
        $stmt->close();
    }
    public function getRandomPhi($i) { // for p_real.php
        // Step 1. Prepare the SQL query 
        $query = "SELECT PI.img_src, PI.desc
        from phi_info PI
        where PI.id = ?";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $i);
        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($img_src, $description);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'img_src'=>$img_src,
                            'description'=>$description,
                            );
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $items;
    }
    public function insertPhiChat($c) {
        // Step 1. Prepare the SQL query 
        $query = "insert into phi_chats(phi_id, content) values (?, ?)";
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
    public function initCoffee() {
        $query = "SELECT count(*) from coffee where date(reg_date) = current_date";         
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
        if ((int)$item >= 1) {
            $query = "update coffee set iced_americano = 0 where date(reg_date) = current_date"; 
        }
        else {
            $query = "insert into coffee (iced_americano) values ('0')"; 
        }
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }    
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->close();
    }
    public function insertVisitorLog($t, $c) {
        // Step 1. Prepare the SQL query 
        $query = "insert into visitor_log (title, content) values (?, ?)";


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
    public function addCoffee() {
        // Step 1. Prepare the SQL query 
        $query = "update coffee set iced_americano = iced_americano + 1 where date(reg_date) = current_date";
        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
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
    public function coffeeMax() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT count(*) from coffee";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }     
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
        return $item;
    
    }
    public function fishMax() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT count(*) from fish";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }     
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
        return $item;
    
    }
    public function getLogs($start) {
        // Step 1. Prepare the SQL query 
        $query = "SELECT id,title, content,user_id, date(reg_date) from fish order by reg_date desc limit ?,10";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $start);

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($id, $title, $content, $user_id, $d);
        $items = array();
        $months = array("01"=> "Jan", "02"=> "Feb", "03"=> "Mar", "04"=> "Apr", "05"=> "May", "06"=> "Jun", "07"=> "Jul", "08"=> "Aug", "09"=> "Sep", "10"=> "Oct", "11"=> "Nov", "12"=> "Dec");
        while($stmt->fetch()) {
            list($year, $month, $day) = preg_split("/-/", $d);
            $items[] = array('article_id'=>$id,
                            'title'=>$title, 
                            'content'=>$content, 
                            'user_id'=>$user_id,
                            'day'=>$day,
                            'month'=>$months[$month],
                            'year'=>$year,
                            'date'=>$d);
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $items;
    
    }
    public function getCoffee($start) {
        // Step 1. Prepare the SQL query 
        $query = "SELECT iced_americano, date(reg_date) from coffee order by reg_date desc limit ?, 20";


        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $start);

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }


        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($iced_americano, $d);
        $items = array();
        while($stmt->fetch()) {
           $items[] = array('iced_americano'=>$iced_americano, 
                            'date'=>$d);
        }

        // Step 5. Close the connection
        $stmt->close();

        // Step 6. Return the selected $items to the function caller
        return $items;
    
    }
    public function getChicken() {
        // Step 1. Prepare the SQL query 
        $query = "SELECT sum(chicken), year(created_at), week(created_at) from chicken_counts group by week(created_at) desc";

        // Step 2. Prepare the mysqli_stmt object (stmt)           
          if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        // Step 3. Execute the statement      
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }


        // Step 4. Retrieve the result and put them in the $items array

        $stmt->bind_result($chicken_sum_of_week, $year_of_week, $week);
        $items = array();
        while($stmt->fetch()) {
           $items[] = array('chicken_sum_of_week'=>$chicken_sum_of_week, 
                            'year_of_week'=>$year_of_week,
                            'week'=>$week);
        }
        // Step 5. Close the connection
        $stmt->close();
        // Step 6. Return the selected $items to the function caller
        return $items;
    
    }
    public function totalCoffee() {
        $query = "SELECT sum(iced_americano) from coffee";


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
    public function totalChicken() {
        $query = "SELECT sum(chicken) from chicken_counts";


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
    public function avgCoffee() {
        $query = "SELECT avg(iced_americano) from coffee";


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
       return $item;
    
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
    
    /* Check login information */
    public function checkLogin($password) {
        if(!($stmt = $this->mysqli->prepare('select user_id, nickname, password_h from fishbowl_users'))) {
           throw new Exception('DB Error: '.$this->mysqli->error);
        }

        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_result($i, $nick, $ph);
        while($stmt->fetch()) {
           if(password_verify($password, $ph)) return array('user_id'=>$i, 'nickname'=>$nick);
        }
        return false;
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);

?>