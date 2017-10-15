<?php
require('db.php');
$contents_mobile = $db->getContent(30);
?>
<!DOCTYPE html>
<html lang="en">
<head>	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>크리마 메모</title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Inconsolata" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
  <style>
    .yellow {
      background-color: #fdeab4 !important; 
    }
    .hidden {
      display: none;
    }
  </style>
</head>

<body style="margin-top: 75px; font-family: Inconsolata; font-size: 18px;">
	<!-- Body -->
  <div class="container">
    <h2>widget_device_type == WidgetDeviceType::MOBILE</h2>
    <?php 
      $total_count = count($contents_mobile);
      $done_count = 0;
      foreach($contents_mobile as $content){
        if ($content['new_content'] != '') $done_count += 1;
      }
      $rate = round(100.0*(float)$done_count/(float)$total_count);
      echo '<div class="progress">';
      echo '<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"';
      echo 'aria-valuenow="'.(int)$rate.'" aria-valuemin="0" aria-valuemax="100" style="width:'.(int)$rate.'%">'.(int)$rate.'%</div></div>';
    ?>
    <h4>찾을 수 없거나 제거된 스타일은 input에 <code>//</code>라고 적어 주세요</h4>
  </div>
	<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>기존 클래스</th>
            <th></th>
            <th>변경할 클래스</th>
        </tr>
    </thead>
    <tbody>
      <?php
      $index = 0;
      foreach($contents_mobile as $content){
        $index += 1;
        echo "<tr class='js-table-row' id='row_".$content['content_id']."'><td class='col-md-1'>".$index."</td><td class='col-md-5' id='content_".$content['content_id']."'>".$content['content']."</td><td class='col-md-1'><button class='js-style-bokbut' id='button_".$content['content_id']."' tabindex='-1'>복붙</button></td><td class='col-md-5'><input type='text' class='js-new-style-input col-md-12' id='input_".$content['content_id']."' value='".$content['new_content']."'></td></tr>";
      }
      ?>
    </tbody>
	</table>
<script>
$(".js-style-bokbut").on('click', function() {
  var content_id = $(this).attr('id').split('_')[1];
  $('#input_' + content_id).val($('#content_' + content_id).text());
  postSave(document.getElementById('input_' + content_id));
});
$(".js-new-style-input").on('change', function() {
  postSave(this);
});
$(".js-new-style-input").on('focus', function() {
  $(this).closest('tr').addClass('yellow');
});
$(".js-new-style-input").on('focusout', function() {
  $(this).closest('tr').removeClass('yellow');
});

function postSave(input) {
  var xhttp = new XMLHttpRequest();
  var params = 'content_id='+encodeURIComponent($(input).attr('id').split('_')[1])+'&content='+encodeURIComponent($(input).val());
  xhttp.open("POST", "widget_post.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(params);
}
</script>
</body>
</html>