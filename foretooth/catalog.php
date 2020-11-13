<?php

class Page {

    /* Utilities */
    
    /* Get data sent from GET method */
    public function getPageParam($var) {
        if(isSet($_GET[$var])) {
            // for PHP <5.4.0
            if (get_magic_quotes_gpc())
                return stripslashes($_GET[$var]);
            else 
                return $_GET[$var];
        } else {
            return '1';
        }
    }


    
    /* Call this method to start generating the page header and footer */
    public function generate() {
        $this->displayHeader();
        register_shutdown_function(array($this, 'displayFooter'));// Call the displayFooter function at the end of the page (but not now).
    }
    
    /* display functions */
    public function displayHeader() {
        echo '<!doctype html>';
        
        ?>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>앞니저지</title>
                <link href="style.css" rel="stylesheet" type="text/css" />
                <link rel="shortcut icon" href="favicon.ico">
            </head>
            <body>
                <header>
<nav id="main-menu">
<a href="catalog.php"><img src="foretooth.jpg"/></a><h1>앞니저지 Apnee Judge</h1>

                        <li><a href="register.php">문제 등록</a></li>
                        </nav>
                </header>
        <?php

    }

    public function displayFooter() {
    
        ?>
            </body>
        </html>
        <?php

    }

    
    /* display array of items */
    public function displayAllItems($items, $total_questions, $pageno) {
    
        if(is_array($items) && count($items)>0) {
            
            ?>
            <section id="items">
                <div class="pagination"><?php
                $page_count = (int)ceil($total_questions / 5);
                echo "<h2>";
                for ($i = 1; $i <= $page_count; $i++) {
                    echo '<a href="catalog.php?page=' . $i . '">'. $i . '</a>  ';
                }

                echo "</h2>";
                ?></div>
                <?php
                foreach($items as $item) { 
                    $this->displayOneItem($item, $pageno);
                } 
            ?><div class="pagination"><?php
                $page_count = (int)ceil($total_questions / 5);
                echo "<h2>";
                for ($i = 1; $i <= $page_count; $i++) {
                    echo '<a href="catalog.php?page=' . $i . '">'. $i . '</a>  ';
                }

                echo "</h2>";
                ?></div>
            </section>
            <?php
        
        }
        
    }
    
    /* display one item */
    public function displayOneItem($item, $pageno) {
        if($item['tries'] === 0){
            $rate = 0.0;}
        else {$rate = round(100.0*(float)$item['corrects']/(float)$item['tries'], 3);}
         ?>
        
        <article class="item">
            <div class="description">
               <header>
                  <h2><?php echo "문제 번호 : ".$item['id']; ?></h2>
               </header>
               <h4><?php echo "시도: ".$item['tries']." 정답: ".$item['corrects']." 정답률: ".$rate."%"; ?></h4>
              <p style="white-space: pre-wrap;"><?php echo $item['question']; ?></p>
              <p>정답을 입력하세요: </p>
              <form id="submit-box" action="check.php" method="post">

                        <input type="text" name="ans" placeholder="여기에">
                        <input type="hidden" name="q_key" value="<?php echo $item['id'];?>">
                        <input type="hidden" name="q_page" value="<?php echo $pageno;?>">
                        <input type="submit" value="고고">
                    </form>
            </div>


             <!-- Display name and description -->


        </article>
        <?php
    } 

};

$page = new Page();

require('db.php');

/* For searching */
$pageno =$page->getPageParam('page');

/* get items */

$items = $db->getItems($pageno);
$total_questions = $db->getTotalPage();


// $items = $db-> getItems($searchText); 

/* Start generating the page header */
$page->generate();

/* display content */
$page->displayAllItems($items, $total_questions, $pageno);
