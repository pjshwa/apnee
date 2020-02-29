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
        $query = "SELECT id, title, content, created_at from eggs where YEAR(created_at) = ? AND MONTH(created_at) = ? order by created_at desc";
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
        $idsOfArticles = array_map(function($elements){
            return $elements['id'];
        }, $items);
        if (count($idsOfArticles) == 0){
            $inClause = '0';
        }
        else {
            $inClause = join(',', $idsOfArticles);
        }
        $query2 = "SELECT id, egg_id, author, comment, created_at from egg_comments where egg_id in (".$inClause.") order by created_at desc";
        if(!($stmt2 = $this->mysqli->prepare($query2))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        if(!$stmt2->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt2->bind_result($id, $egg_id, $author, $comment, $commdate);
        $allCommentsById = array();
        foreach($idsOfArticles as $egg_id){
            $allCommentsById[$egg_id] = array();
        }
        $finish_time = new DateTime('now');
        while($stmt2->fetch()){
            $start_time = new DateTime($commdate);
            if ($start_time->diff($finish_time)->days < 2){
                $commnew = true;
            }
            else {
                $commnew = false;
            }
            $allCommentsById[$egg_id][] = array(
                'commid'=>$id,
                'commauthor'=>$author,
                'comment'=>$comment,
                'commdate'=>$commdate,
                'commnew'=>$commnew,
            );
        }
        foreach($items as &$item){ // mutable $item
            $item['comments'] = $allCommentsById[$item['id']];
        }
        $stmt2->close();
        return $items;
    }

    public function postComment($article_id, $author, $comment, $password) {
        $query = "INSERT INTO `egg_comments`(`egg_id`, `comment`, `author`, `h_password`) VALUES (?, ?, ?, ?)";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $h_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('ssss', $article_id, $comment, $author, $h_password);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->close();
    }

    public function updateComment($comment_id, $author, $comment, $password) {
        $ph = hPassOfComment($comment_id);
        if(password_verify($password, $ph)){
            $query = "UPDATE `egg_comments` SET `comment`=?,`author`=? WHERE id=?";
            if(!($stmt2 = $this->mysqli->prepare($query))) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
            $stmt2->bind_param('sss', $comment, $author, $article_id);
            if(!$stmt->execute()) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
            $stmt2->close();
            return true;
        }
        return false;
    }

    public function deleteComment($comment_id, $password) {
        $ph = hPassOfComment($comment_id);
        if(password_verify($password, $ph)){
            $query = "DELETE FROM `egg_comments` WHERE id=?";
            if(!($stmt2 = $this->mysqli->prepare($query))) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
            $stmt2->bind_param('s', $comment_id);
            if(!$stmt->execute()) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
            $stmt2->close();
            return true;
        }
        return false;
    }

    private function hPassOfComment($comment_id) {
        $querypre = "SELECT `h_password` FROM `egg_comments` WHERE id=?";
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $comment_id);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($ph);
        $item = '';
        while($stmt->fetch()) {
            $item = $ph;
        }
        $stmt->close();
        return $item;
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);

?>