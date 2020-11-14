var score = 0;
var counter = 15;
var score_span = document.getElementById("score");
var count_span = document.getElementById("count");
var sound_check = document.getElementsByName("sound")[0];
var pre_cows = [];

function preload() {
  for (var i = 0; i < arguments.length; i++) {
    pre_cows[i] = new Image();
    pre_cows[i].src = preload.arguments[i];
  }
}

preload(
  "./static/pics/cows/n_1.png",
  "./static/pics/cows/n_2.png",
  "./static/pics/cows/n_3.png",
  "./static/pics/cows/n_4.png",
  "./static/pics/cows/n_5.png",
  "./static/pics/cows/n_6.png",
  "./static/pics/cows/y_1.png",
  "./static/pics/cows/y_2.png",
  "./static/pics/cows/y_3.png",
  "./static/pics/cows/y_4.png",
  "./static/pics/cows/y_5.png",
  "./static/pics/cows/y_6.png"
)

$("#startClock").click( function(){
  document.getElementById("intro").style.visibility = "hidden";
  document.getElementById("startClock").style.visibility = "hidden";
  document.getElementById("front_row").style.height = "0px";
  document.getElementById("front_image").style.height = "0px";
  document.getElementById("scoreboard").style.height = "0px";
  document.getElementById("scoreboard").style.visibility = "hidden";
  document.getElementById("sound_box").style.visibility = "hidden";
  document.getElementById("ig").style.visibility = "visible";
  refreshCows();

  var counterInterval = setInterval(function() {
    counter--;
    if (counter >= 0) {
      count_span.innerHTML = counter;
    }
    if (counter === 0) {
      gameOver();
      clearInterval(counterInterval);
    }
    if (counter > 15) {
      cheater();
      clearInterval(counterInterval);
    }
  }, 1000);
});

const yes_prompts = ["얼룩소를 고르세요", "얼룩소를 찾으세요", "얼룩소가 아닌 소를 고르지 마세요", "얼룩 무늬가 있는 소를 고르세요"];
const no_prompts = ["얼룩소를 고르지 마세요", "얼룩소가 아닌 소를 고르세요", "얼룩소를 고르지 마세요", "얼룩소가 아닌 소를 고르지 말지 마세요"];
var prompt = 0;
var prompt_text = "";
var answer = 0;
var prompt_span = document.getElementById('prompt_span');
var cows = [];

for (var i = 0; i < 4; i++) {
  cows.push(document.getElementById('cow_'+(i+1)));
}

// rand number generator
function randomIntFromInterval(min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min);
}

function refreshCows() {
  var prompt = randomIntFromInterval(0, 3);
  var yes_or_no = randomIntFromInterval(0, 1);

  if (yes_or_no === 0) prompt_text = no_prompts[prompt];
  else prompt_text = yes_prompts[prompt];

  prompt_span.innerHTML = prompt_text;
  var answer = randomIntFromInterval(0, 3);
  for (var i = 0; i < 4; i++) {
    if (i === answer) {
      cows[i].setAttribute('onclick','moveOn()');
      if (yes_or_no === 0) cows[i].src = './static/pics/cows/n_'+randomIntFromInterval(1, 6)+'.png';
      else cows[i].src = './static/pics/cows/y_'+randomIntFromInterval(1, 6)+'.png';
    }
    else {
      cows[i].setAttribute('onclick','gameOver()');
      if (yes_or_no === 0) cows[i].src = './static/pics/cows/y_'+randomIntFromInterval(1, 6)+'.png';
      else cows[i].src = './static/pics/cows/n_'+randomIntFromInterval(1, 6)+'.png';
    }
  }
}

var cow_01 = new Audio('./m/moo1.ogg');
var cow_02 = new Audio('./m/moo2.ogg');
var cow_03 = new Audio('./m/moo3.ogg');
var cow_04 = new Audio('./m/moo4.ogg');

function playSound(){
  var sound_pick = randomIntFromInterval(1, 4);

  if (sound_pick === 1) cow_01.play();
  else if (sound_pick === 2) cow_02.play();
  else if (sound_pick === 3) cow_03.play();
  else if (sound_pick === 4) cow_04.play();
}

function moveOn() {
  score++;
  score_span.innerHTML = score;
  refreshCows();
  if (sound_check.checked) playSound();
}

function gameOver() {
  window.alert("게임 끝");
  window.location.reload();
  document.getElementById("sc").value = document.getElementById("score").innerHTML;
  document.forms["score"].submit();
}

function cheater() {
  window.alert("사기꾼");
  var loc = window.location;
  window.location = loc.protocol + '//' + loc.host + loc.pathname + loc.search;
  // force GET
}
