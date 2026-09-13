<?php
require_once __DIR__ . '/../lib/database.php';

class CatRepository extends DatabaseRepository {
    public function catCount() {
        // Step 1. Prepare the SQL query
        $query = "SELECT count(*) from cats";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        $stmt = $this->mysqli->prepare($query);
        $stmt->execute();
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
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $l);
        $stmt->execute();
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
}
