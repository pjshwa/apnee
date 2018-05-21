
<?php
include("header.php");
require('db.php');
session_start();
unset($_SESSION['login']);
session_destroy();
// destroy login info

?>
<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <img src="./static/pics/smilehand.gif" width="30%"/>
            <h3>Hello Hello!</h3>
        </div>
        <div class="col-sm-4">
            <br/><br/>
            <a href="./door.php"><img src="./static/pics/door.png" width="100px"/></a>
            <a href="./duck.html"><img src="./static/pics/ducky_flip_v.png" width="90px"/></a>
            <a href="javascript:var audio2 = new Audio('./static/pics/Dramatic-chipmunk.mp3');audio2.play();"><img src="./static/pics/slide.png" id="myImg" width="100px"/></a>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <p>(당구장) 공지</p>
            <p>Internet Explorer 11에서 ogg 파일이 제대로 재생되지 않는다는 사실을 발견 했습니다</p>
            <p>눈 빼고는 소리로는 다 ogg 파일을 사용하였기 때문에 웹사이트 경험에 차질이 있었을 것으로 사료가 됩니다</p>
            <p>별다른 조치를 취하지는 않고 있으며 크롬 설치를 권장합니다 ...</p>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <div class="container">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            ** 이벤트들
                        </h3>
                    </div>
                    <div class="panel-body">
                        <ul>
                            <?php
                                $events = $db->titlesOfEvents();
                                foreach($events as $event){
                                    echo '<li>';
                                    echo '<a data-toggle="modal" data-target="#eventsModal" data-id="'.$event['id'].'">'.$event['title'].'</a> ('.date('Y년 n월 j일', strtotime($event['date'])).')';
                                    echo '</li>';
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer-footer" style="margin-bottom:0;">
    <div class="container">
        <div class="col-sm-8">
        </div>
        <div class="col-sm-4">
            <div class="footer-policy-group--web">
                <a class="footer-link footer-link--right" href="personal.php">개인정보 처리방침</a>
            </div>
        </div>
    </div>
</footer>

<div id="mytextModal" class="textmodal">
    <div class="textmodal-content">
        <div class="textmodal-header">
            <span class="txtclose">x</span>
<!--             <img src="./static/pics/logo.png"/> -->
          </div>
    <!-- Modal content -->
        <div class="textmodal-body">
            <h3 id="txt"></h3>
          </div>
    </div>
</div>
<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">X</span>
  <img class="modal-content" id="img01" style="cursor:pointer;">
  <div id="caption"></div>
</div>

<!-- Modal for events -->
<div class="modal fade" id="eventsModal" tabindex="-1" role="dialog" aria-labelledby="eventsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventsModalLabel"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">네</button>
            </div>
        </div>
    </div>
</div>
<script>
    var events_modal = $('#eventsModal');
    events_modal.on('show.bs.modal', function (event) {
        var target = $(event.relatedTarget);
        var event_id = target.data('id');
        var modal = $(this)
        var xhttp = new XMLHttpRequest();
        var params = "event_id="+event_id;
        xhttp.open("POST", "events_get.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // for POST
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var resp = (JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText));
                modal.find('.modal-title').text(resp.title);
                modal.find('.modal-body').html(resp.content);
            }
        };
        xhttp.send(params);
    });

    var txtmodal = document.getElementById('mytextModal');
    var txtspan = document.getElementsByClassName("txtclose")[0];
    $('.txtmod').click(function(){
        txtmodal.style.display = "block";
        txt.innerHTML = this.title;
    })
    txtspan.onclick = function() {
        txtmodal.style.display = "none";
    }
    $(document).keyup(function(ev){
    if(ev.keyCode == 27)
        txtmodal.style.display = "none";
    });

    var modal = document.getElementById('myModal');

    var img = document.getElementById('myImg');
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    img.onclick = function(){
        modal.style.display = "block";
        modalImg.src = this.src;
        modalImg.alt = this.alt;
        captionText.innerHTML = this.alt;
    }
    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];
    // When the user clicks on <span> (x), close the modal
    modalImg.onclick = function() {
        modal.style.display = "none";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }

    $(document).keyup(function(ev){
    if(ev.keyCode == 27)
        modal.style.display = "none";
    });
</script>
</body>

</html>
