<?php

class Page {

    
    /* Call this method to start generating the page header and footer */
    public function generate($db) {
        $this->displayHeader($db);
        register_shutdown_function(array($this, 'displayFooter'));// Call the displayFooter function at the end of the page (but not now).
    }
    
    /* display functions */
    public function displayHeader($db) {
        echo '<!doctype html>';
        
        ?>
        <?php
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $db->updateTempQ($_POST['newq'], $_POST['newa']);
                    echo "<script>alert('문제 등록 요청을 하였습니다.');location.href='catalog.php';</script>";
                } else {
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

                <section id="items">
            <article class="item">
            <div class="description">
               <header>
                  <h2><?php echo "새로운 문제 등록 요청"; ?></h2>
               </header>
              <form id="submit-box" action="register.php" method="post">
              			<p><label for="newq">문제</label></p>
                        <p><textarea id="newq" form="submit-box" name="newq" rows="10" cols="100"></textarea></p>
                        <p><label for="newa">그 정답</label></p>
                        <p><input id="newa" type="text" name="newa"></p>
                        <input type="submit" value="등록">
                    </form>
            </div>


             <!-- Display name and description -->


        </article>
            </section>
                    
                <?php
                 }
    }

    public function displayFooter() {
    
        ?>
            </body>
        </html>
        <?php

    }

};

$page = new Page();

require('db.php');
/* Start generating the page header */
$page->generate($db);
