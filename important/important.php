<?php
require('db.php');
$page = 1;
$meal = (int)$_GET['meal'];
if($meal != 1 and $meal != 0){
  echo '다른 숫자 입력하지 마 바보야';
}
else {
if(isSet($_GET['page'])){
  $page = (int)$_GET['page'];
  if($page < 1) $page = 1;
}
$items = $db->getFood($meal, 10*($page-1));
$totalCount = $db->picCount($meal);
$is_next_page = false;
$is_previous_page = false;
if($page > 1) $is_previous_page = true;
if($totalCount > 10*$page) $is_next_page = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>  
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>중요한 목록</title>
  <link href="../static/css/bootstrap.min.css" rel="stylesheet" />
  <script src="../static/js/jquery.js"></script>
  <script src="../static/js/bootstrap.min.js"></script>
</head>

<style>
@media only screen and (min-width: 320px) { 
  .foodimage {
    width: 70%;
  } 
} 
@media only screen and (min-width: 769px) { 
  .foodimage {
    width: 40%;
  } 
} 
</style>

<body style="margin-top: 75px;">

  <!-- Body -->
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php
        foreach($items as $item){
          echo '<article>';
          echo '<h2>'.$item['canting'].'</h2>';
          echo '<h4>'.$item['date'].'</h4>';
          echo '<br/>';
        ?>
        <img src="/static/images/important/<?php echo $item['img_src']; ?>" class="img-responsive foodimage"/>
        <?php
          echo '<br/>';
          echo '<p class="lead" style="white-space: pre-wrap;">'.$item['comment'].'</p>';
          if($item['have_been'] === 1){
            echo '<br/>';
            echo '<h4 style="color:blue;">* 리뷰</h2>';
            echo '<p class="lead" style="white-space: pre-wrap;">'.$item['review'].'</p>';
          }
          echo '<hr/>';
          echo '</article>';
        }
        echo '<ul class="pager">';
        if($is_previous_page){
          echo sprintf('<li class="previous"><a href="./important.php?meal=%d&page=%d">&larr; 이전 페이지</a></li>', $meal, $page-1);
        }
        else{
          echo '<li class="previous disabled"><a href="#">&larr; 이전 페이지</a></li>';
        }
        if($is_next_page){
          echo sprintf('<li class="next"><a href="./important.php?meal=%d&page=%d">다음 페이지 &rarr;</a></li>', $meal, $page+1);
        }
        else{
          echo '<li class="next disabled"><a href="#">다음 페이지 &rarr;</a></li>';
        }
        ?>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
<?php } ?>
