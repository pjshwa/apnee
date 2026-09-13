<?php
require_once __DIR__ . '/../lib/database.php';

class EggRepository extends DatabaseRepository {
    public function getArticlesOfMonth($year, $month) {
        // Step 1. Prepare the SQL query
        $query = "SELECT id, title, content, created_at from eggs where YEAR(created_at) = ? AND MONTH(created_at) = ? order by created_at desc";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ss', $year, $month);
        $stmt->execute();
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
        $query2 = "SELECT id, egg_id, author, comment, created_at from egg_comments where comment_id IS NULL AND egg_id in (".$inClause.") order by created_at";
        $stmt2 = $this->mysqli->prepare($query2);
        $stmt2->execute();
        $stmt2->bind_result($id, $egg_id, $author, $message, $commdate);
        $comments = array();
        $finish_time = new DateTime('now');
        while($stmt2->fetch()){
            $start_time = new DateTime($commdate);
            $commnew = $start_time->diff($finish_time)->days < 2;
            $comments[] = array(
                'egg_id'=>$egg_id,
                'commid'=>$id,
                'commauthor'=>$author,
                'message'=>$message,
                'commdate'=>$commdate,
                'commnew'=>$commnew,
            );
        }
        $stmt2->close();

        if (count($comments) > 0) {

            $inClause = join(',', array_map(function($el) {return $el['commid'];}, $comments));
            $query3 = "SELECT id, comment_id, author, comment, created_at from egg_comments where comment_id in (".$inClause.") order by created_at";
            $stmt3 = $this->mysqli->prepare($query3);
            $stmt3->execute();
            $stmt3->bind_result($id, $comment_id, $author, $message, $commdate);
            while($stmt3->fetch()) {
                foreach ($comments as &$comment) {
                    if ($comment['commid'] == $comment_id) {
                        $start_time = new DateTime($commdate);
                        $commnew = $start_time->diff($finish_time)->days < 2;
                        $comment['subcomments'][] = array(
                            'commid'=>$id,
                            'commauthor'=>$author,
                            'message'=>$message,
                            'commdate'=>$commdate,
                            'commnew'=>$commnew,
                        );
                    }
                }
            }
            $stmt3->close();

        }

        unset($comment);


        // $item is mutable
        foreach($items as &$item) {
            if (!isset($item['comments'])) {
                $item['comments'] = array();
            }
            foreach($comments as $comment) {
                if ($item['id'] == $comment['egg_id']) {
                    $item['comments'][] = $comment;
                }
            }
        }

        return $items;
    }

    public function newComment($article_id, $author, $comment, $comment_id) {
        $query = "INSERT INTO `egg_comments` (`egg_id`, `comment_id`, `comment`, `author`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ssss', $article_id, $comment_id, $comment, $author);
        $stmt->execute();
        $nid = $this->mysqli->insert_id;
        $stmt->close();
        return $nid;
    }
}
