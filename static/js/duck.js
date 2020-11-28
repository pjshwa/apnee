var quack = new Audio('./m/quack.ogg');
var txt = document.getElementById("center_text");

$(document).ready(function() {
  $('#duck1').click(function(e) {
    quack.play();
  });
  $('#duck2').click(function(e) {
    quack.play();
  });
  $('#duck3').click(function(e) {
    quack.play();
  });

  var event_1 = setInterval(function(){
    txt.innerHTML = "오리는 3마리 까지밖에 안 나와요";
    clearInterval(event_1);
  }, 100000);

  var event_2 = setInterval(function(){
    txt.innerHTML = "이걸 2분 30초 동안 보고 있었어요";
    clearInterval(event_2);
  }, 150000);

  var event_3 = setInterval(function(){
    txt.innerHTML = "이게 끝이에요 더이상 뭐 안 나와요";
    clearInterval(event_3);
  }, 210000);

  var event_4 = setInterval(function(){
    txt.innerHTML = "얼른 잠을 자요 내일을 준비해요";
    clearInterval(event_4);
  }, 280000);

  // Story Time
  // 
  // var event_5 = setInterval(function(){
  //   var prompts = ["a","b"];
  //   var index = 0;
  //   var story = setInterval(function(){
  //     txt.innerHTML = prompts[index];
  //     index++;
  //     if(index == prompts.length) clearInterval(story);
  //   }, 4000);
  //   clearInterval(event_5);
  // }, 300000);

});

var all_ducks = document.getElementsByClassName("duck");
var active_ducks_cnt = 0;

var de = document.documentElement.getBoundingClientRect();
var duck_directions = [];

// Initially all ducks are facing right.
for (var i = 0; i < all_ducks.length; i++) duck_directions.push(false);

var periodicalCollisionCheck = setInterval(function(){
  for (var i = 0; i < active_ducks_cnt; i++) {
    for (var j = i + 1; j < active_ducks_cnt; j++) duckCollisionCheck(i, j);

  }
}, 1000);

addOneDuck();

function addOneDuck(){
  all_ducks[active_ducks_cnt].style.visibility = "visible";
  all_ducks[active_ducks_cnt].style.left = '0px';
  duckVerticalMove(all_ducks[active_ducks_cnt], active_ducks_cnt);
  duckHorizontalMove(all_ducks[active_ducks_cnt], active_ducks_cnt);
  active_ducks_cnt++;
}

function changeDuckDirection(duck_index, direction) {
  if (!duck_directions[duck_index]){
    all_ducks[duck_index].style.webkitTransform = "translate(-50%, -50%) scaleX(1)";
    all_ducks[duck_index].style.transform = "translate(-50%, -50%) scaleX(1)";
  }
  else {
    all_ducks[duck_index].style.webkitTransform = "translate(-50%, -50%) scaleX(-1)";
    all_ducks[duck_index].style.transform = "translate(-50%, -50%) scaleX(-1)";
  }
  duck_directions[duck_index] = direction;
}

var addDucks = setInterval(function(){
  if (active_ducks_cnt >= all_ducks.length) clearInterval(addDucks);
  else addOneDuck();
}, 13000);

// rand number generator
function randomIntFromInterval(min,max) {
  return Math.floor(Math.random()*(max-min+1)+min);
}

function duckVerticalMove(duck, index){
  const limit = 15;
  var initial_y_pos = (de.bottom - de.top) * 0.5;
  var y_pos = initial_y_pos;
  var direction = false;
  var move = setInterval(function(){
    de = document.documentElement.getBoundingClientRect();
    initial_y_pos = (de.bottom - de.top) * 0.5;
    if (direction) y_pos += 1;
    else y_pos -= 1;
    if (y_pos - initial_y_pos > limit) direction = false;
    else if (y_pos - initial_y_pos < -limit) direction = true;
    duck.style.top = y_pos + 'px';
  }, 30);
}

function duckHorizontalMove(duck, index){
  var x_pos = 0;
  var x_limit = de.right - de.left;
  const speed = randomIntFromInterval(100,190) * 0.01;
  var move = setInterval(function(){
    de = document.documentElement.getBoundingClientRect();
    x_limit = de.right - de.left;
    if (duck_directions[index]) x_pos += speed;
    else x_pos -= speed;
    if (x_pos > x_limit) changeDuckDirection(index, false);
    else if (x_pos < de.left) changeDuckDirection(index, true);
    duck.style.left = x_pos + 'px';
  }, 30);
}

function duckCollisionCheck(duck1_i, duck2_i) {
  // collision condition: ax < bx + bw && bx < ax + aw
  if (all_ducks[duck1_i].offsetLeft < all_ducks[duck2_i].width + all_ducks[duck2_i].offsetLeft &&
    all_ducks[duck2_i].offsetLeft < all_ducks[duck1_i].width + all_ducks[duck1_i].offsetLeft) {

    // duck 1 is left to duck 2
    if (all_ducks[duck1_i].offsetLeft < all_ducks[duck2_i].offsetLeft) {
      if (duck_directions[duck1_i] || !duck_directions[duck2_i]) {
        changeDuckDirection(duck1_i, !duck_directions[duck1_i]);
        changeDuckDirection(duck2_i, !duck_directions[duck1_i]);
      }
    }
    else {
      if (duck_directions[duck2_i] || !duck_directions[duck1_i]) {
        changeDuckDirection(duck1_i, !duck_directions[duck1_i]);
        changeDuckDirection(duck2_i, !duck_directions[duck1_i]);
      }
    }
  }
}
