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

    public function limitAllMemosByCategory($limit){
        $rank_query = 'SELECT id, category_id, title, created_at,
                       @rank := IF(@current_category_id = category_id, @rank + 1, 1) AS rank,
                       @current_category_id := category_id 
                       FROM gongboo ORDER BY category_id, created_at DESC';
        $query = 'SELECT id, category_id, title, created_at FROM ('.$rank_query.') gongboo_with_rank, (SELECT @rank := 1) r, (SELECT @current_category_id := 0) c WHERE rank <= ? ORDER BY created_at DESC;';
        $category_set = $this->titlesOfCategory();
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception($query.'DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $limit);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($id, $category_id, $title, $date);
        $items = array();
        while($stmt->fetch()) {
            $category_set[$category_id]['articles'][] = array(
                                                        'id'=>$id,
                                                        'title'=>$title,
                                                        'date'=>$date,
                                                        );
        }
        $stmt->close();
        return $category_set;
    }

    public function titlesOfMemo($category_id) {
        // check if category title exists, if it does $category_title represents title
        // if not it is set to false
        $category_title = $this->titleOfCategory($category_id);
        if ($category_title == false){
            $category_id = 0;
        }
        if ($category_id != 0){
            $query = "SELECT id, title, created_at from gongboo where category_id = ? order by created_at desc";
            if(!($stmt = $this->mysqli->prepare($query))) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
            $stmt->bind_param('s', $category_id);
        }
        else {
            $category_title = '모든 카테고리';
            $query = "SELECT id, title, created_at from gongboo order by created_at desc";
            if(!($stmt = $this->mysqli->prepare($query))) {
                throw new Exception('DB Error: '.$this->mysqli->error);
            }
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
        return [$category_title, $items];
    }

    public function memoOfArticle($article_id) {
        $query = "SELECT title, content, include_highlighter, include_markdown, created_at from gongboo where id = ?";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $article_id);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($title, $content, $include_highlighter, $include_markdown, $date);
        $results_present = false;
        while($stmt->fetch()) {
            $results_present = true;
            $item = array(
                            'title'=>$title,
                            'content'=>$content,
                            'include_highlighter'=>$include_highlighter,
                            'include_markdown'=>$include_markdown,
                            'date'=>$date,
                            );
        }
        $stmt->close();
        if (!$results_present) return false;
        return $item;
    }

    private function titlesOfCategory(){
        $query = 'SELECT id, title FROM gongboo_categories ORDER BY created_at';
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($id, $title);
        $items = array();
        while($stmt->fetch()) {
            $items[$id] = array(
                            'id'=>$id,
                            'title'=>$title,
                            'articles'=>array()
                            );
        }
        $stmt->close();
        return $items;
    }

    private function titleOfCategory($category_id){
        $query = 'SELECT title FROM gongboo_categories WHERE id = ? ORDER BY created_at';
        if(!($stmt = $this->mysqli->prepare($query))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_param('s', $category_id);
        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->bind_result($title);
        $item = false;
        while($stmt->fetch()) {
            $item = $title;
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