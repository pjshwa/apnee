<?php
require_once __DIR__ . '/../lib/database.php';

class JudgeRepository extends DatabaseRepository {
    public function getItems($page='1') {
		// Step 1. Prepare the SQL query
        $page = (int)$page; if ($page < 1) $page = 1;
        $pg_idx = ((int)$page - 1) * 5;
        $query = "SELECT question_key, question, tries, corrects from questions limit ?,5";


		// Step 2. Prepare the mysqli_stmt object (stmt)
          $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('s', $pg_idx);



		// Step 3. Execute the statement
        $stmt->execute();


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
          $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('s', $k);

        // Step 3. Execute the statement
        $stmt->execute();
    }
    public function updateTempQ($q, $a) {
        // Step 1. Prepare the SQL query
        $query = "insert into temp_questions (question, answer) values (?, ?)";


        // Step 2. Prepare the mysqli_stmt object (stmt)
          $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('ss', $q, $a);

        // Step 3. Execute the statement
        $stmt->execute();
    }

    public function getTotalPage() {
        $query = "SELECT count(*) from questions";


        // Step 2. Prepare the mysqli_stmt object (stmt)
          $stmt = $this->mysqli->prepare($query);



        // Step 3. Execute the statement
        $stmt->execute();


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
          $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('s', $k);

        // Step 3. Execute the statement
        $stmt->execute();


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
}
