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

    public function titlesOfMemo() {
        // Step 1. Prepare the SQL query
        $query = "SELECT id, title, created_at from crema_memo order by created_at desc";
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($id, $title, $date);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'id'=>$id,
                            'title'=>$title,
                            'date'=>$date,
                            );
        }
        $stmt->close();
        return $items;
    }

    public function memoOfArticle($article_id) {
        $query = "SELECT title, content, created_at from crema_memo where id = ?";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $article_id);
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

    public function getContent($widget_device_type) {
        $query = "SELECT content, new_content, id from crema_widget_contents where widget_device_type=?";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $widget_device_type);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($content, $new_content, $content_id);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                'content'=>$content,
                'new_content'=>$new_content,
                'content_id'=>$content_id,
                );
        }
        $stmt->close();
        return $items;
    }

    public function updateContent($content_id, $content) {
        $query = "UPDATE `crema_widget_contents` SET `new_content`=? WHERE id=?";
        if(!($stmt2 = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt2->bind_param('ss', $content, $content_id);
        if(!$stmt2->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt2->close();
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