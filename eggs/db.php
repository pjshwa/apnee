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

    public function getArticlesOfMonth($year, $month) {
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
        if (count($items) == 0) return $items;

        $inClause = join(',', array_map(function($el) {return $el['id'];}, $items));
        $query2 = "SELECT id, egg_id, author, comment, created_at from egg_comments where egg_id in (".$inClause.") order by created_at";
        if(!($stmt2 = $this->mysqli->prepare($query2))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        if(!$stmt2->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt2->bind_result($id, $egg_id, $author, $comment, $commdate);
        $allCommentsById = array();
        $finish_time = new DateTime('now');
        while($stmt2->fetch()){
            $start_time = new DateTime($commdate);
            $commnew = $start_time->diff($finish_time)->days < 2;
            $allCommentsById[$egg_id][] = array(
                'commid'=>$id,
                'commauthor'=>$author,
                'comment'=>$comment,
                'commdate'=>$commdate,
                'commnew'=>$commnew,
            );
        }

        // $item is mutable
        foreach($items as &$item) $item['comments'] = $allCommentsById[$item['id']] ?? array();

        $stmt2->close();
        return $items;
    }

    public function newComment($article_id, $author, $comment) {
        $query = "INSERT INTO `egg_comments` (`egg_id`, `comment`, `author`, `created_at`, `updated_at`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('sss', $article_id, $comment, $author);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->close();
    }
};

// Create a DB object $db. We will use $db to connect to database in catalog.php
$db = new DB($credentials['host'], 
             $credentials['user'], 
			 $credentials['pass'], 
			 $credentials['database']);
?>
