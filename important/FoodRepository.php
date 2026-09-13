<?php
require_once __DIR__ . '/../lib/database.php';

class FoodRepository extends DatabaseRepository {
    public function picCount($meal) {
        // Step 1. Prepare the SQL query
        $query = "SELECT count(*) from important where meal = ?";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $meal);
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

    public function getFood($meal, $l) {
        // Step 1. Prepare the SQL query
        $query = "SELECT canting, img_src, have_been,  comment, review, date(reg_date)
        from important
        where meal = ?
        order by reg_date desc limit ?,10";
        // Step 2. Prepare the mysqli_stmt object (stmt)
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ss', $meal, $l);
        $stmt->execute();
        $stmt->bind_result($canting, $img_src, $have_been, $comment, $review, $d);
        $items = array();
        while($stmt->fetch()) {
            $items[] = array(
                            'canting'=>$canting,
                            'img_src'=>$img_src,
                            'have_been'=>$have_been,
                            'comment'=>$comment,
                            'review'=>$review,
                            'date'=>$d,
                            );
        }

        $stmt->close();

        // Step 6. Return the selected $items to the function caller
       return $items;
    }
}
