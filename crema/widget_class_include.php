    <?php 
      $total_count = count($contents);
      $done_count = 0;
      foreach($contents as $content){
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
            <th>변경할 클래스</th>
        </tr>
    </thead>
    <tbody>
      <?php
      $index = 0;
      foreach($contents as $content){
        $index += 1;
        echo "<tr><td class='col-md-1'>".$index."</td><td class='col-md-6'>".$content['content']."</td><td class='col-md-5'><input type='text' class='js-new-style-input col-md-12' id='".$content['content_id']."' value='".$content['new_content']."'></td></tr>";
      }
      ?>
    </tbody>
	</table>
<script>
$(".js-new-style-input").on('change', function() {
  var xhttp = new XMLHttpRequest();
  var params = 'content_id='+encodeURIComponent($(this).attr('id'))+'&content='+encodeURIComponent($(this).val());
  xhttp.open("POST", "widget_post.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(params);
});
$(".js-new-style-input").on('focus', function() {
  $(this).closest('tr').addClass('yellow');
});
$(".js-new-style-input").on('focusout', function() {
  $(this).closest('tr').removeClass('yellow');
});
</script>
</body>
</html>