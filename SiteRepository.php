<?php
require_once __DIR__ . '/lib/database.php';

class SiteRepository extends DatabaseRepository {
    public function getEventById($event_id) {
        $query = "SELECT title, content, created_at from events where id = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $event_id);
        $stmt->execute();
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
        $stmt = $this->mysqli->prepare($query);
        $stmt->execute();
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
          $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ssss', $nickname, $success,  $remain_time, $hits_score);
        // Step 3. Execute the statement
        $stmt->execute();
        // Step 5. Close the connection
        $stmt->close();
    }

    public function insertPhiChat($c) {
        // Step 1. Prepare the SQL query
        $query = "insert into phi_chats(phi_id, content, reg_date) values (?, ?, CURRENT_TIMESTAMP)";
        // Step 2. Prepare the mysqli_stmt object (stmt)
          $stmt = $this->mysqli->prepare($query);
        $i = mt_rand(1,13);
        $stmt->bind_param('ss', $i, $c);
        // Step 3. Execute the statement
        $stmt->execute();
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
        $stmt = $this->mysqli->prepare($query);

        // Step 3. Execute the statement
        $stmt->execute();
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
          $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ss', $t, $c);


        // Step 3. Execute the statement
        $stmt->execute();
        // Step 5. Close the connection
        $stmt->close();
    }

    public function getVisitorLogs() {
        // Step 1. Prepare the SQL query
        $query = "SELECT title, content, reg_date from visitor_log order by reg_date desc";

        // Step 2. Prepare the mysqli_stmt object (stmt)
          $stmt = $this->mysqli->prepare($query);

        // Step 3. Execute the statement
        $stmt->execute();

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
        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('s', $date);
        $stmt->execute();

        $stmt->bind_result($url);
        $items = array();
        while($stmt->fetch()) {
            $items[] = $url;
        }
        $stmt->close();
        return $items[0];
    }

    public function getDailyLeetCodeList() {
        $query = "SELECT url, reg_date from daily_problems order by reg_date";
        $stmt = $this->mysqli->prepare($query);

        $stmt->execute();

        $stmt->bind_result($url, $date);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array('url'=>$url,
                            'date'=>$date);
        }
        $stmt->close();
        return $items;
    }
}
