  // Start : carusel slider 
var cases = [
    ["dark-gradiant","13"],
    ["danger-gradiant","3"],
    ["dark-gradiant","12"],
    ["danger-gradiant","4"],
    ["dark-gradiant","11"],
    ["danger-gradiant","5"],
    ["dark-gradiant","10"],
    ["danger-gradiant","6"],
    ["dark-gradiant","9"],
    ["success-gradiant","0"],
    ["danger-gradiant","7"],
    ["dark-gradiant","8"], 
    ["danger-gradiant","1"],
    ["dark-gradiant","14"],
    ["danger-gradiant","2"],
    ["dark-gradiant","13"],
    ["danger-gradiant","3"],
    ["dark-gradiant","12"],
    ["danger-gradiant","4"],
    ["success-gradiant","0"],
    ["dark-gradiant","11"],
    ["danger-gradiant","5"],
    ["dark-gradiant","10"],
    ["danger-gradiant","6"],
    ["dark-gradiant","9"],
    ["danger-gradiant","7"],
    ["dark-gradiant","8"],
    ["danger-gradiant","1"],
    ["dark-gradiant","14"],
    ["danger-gradiant","2"],
    ["dark-gradiant","13"],
    ["danger-gradiant","3"],
    ["dark-gradiant","12"],
    ["danger-gradiant","4"],
    ["success-gradiant","0"],
    ["dark-gradiant","11"],
    ["danger-gradiant","5"],
    ["dark-gradiant","10"],
    ["danger-gradiant","6"],
    ["dark-gradiant","9"],
    ["danger-gradiant","7"],
    ["dark-gradiant","8"], 
    ["danger-gradiant","1"],
    ["dark-gradiant","14"],
    ["danger-gradiant","2"],
    ["dark-gradiant","13"],
    ["danger-gradiant","3"],
    ["dark-gradiant","12"],
    ["danger-gradiant","4"],
    ["success-gradiant","0"],
    ["dark-gradiant","11"],
    ["danger-gradiant","5"],
    ["dark-gradiant","10"],
    ["danger-gradiant","6"],
    ["dark-gradiant","9"],
    ["danger-gradiant","7"],
    ["dark-gradiant","8"],
    ["danger-gradiant","1"],
    ["dark-gradiant","14"],
    ["danger-gradiant","2"],
    ["dark-gradiant","13"],
    ["danger-gradiant","3"],
    ["dark-gradiant","12"],
    ["danger-gradiant","4"],
    ["success-gradiant","0"],
    ["dark-gradiant","11"],
    ["danger-gradiant","5"],
    ["dark-gradiant","10"],
    ["danger-gradiant","6"],
    ["dark-gradiant","9"],
    ["danger-gradiant","7"],
    ["dark-gradiant","8"],
    ["danger-gradiant","1"],
    ["dark-gradiant","14"],
    ["danger-gradiant","2"],
    ["dark-gradiant","9"],
    ["danger-gradiant","7"],
    ["dark-gradiant","12"],
    ["danger-gradiant","3"],
    ["dark-gradiant","13"],

];
//alert(cases[56])

function getName(name) {
    var arr = name.split('|');
    return (arr.length == 1) ? name : arr[1];
}
Array.prototype.shuffle = function () {
    var o = this;
    for (var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
}
Array.prototype.mul = function (k) {
    var res = []
    for (var i = 0; i < k; ++i) res = res.concat(this.slice(0))
    return res
}
Math.rand = function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
function getImage(str, w, h) {
    w = w || 384;
    h = h || 384;
    return str + '/' + w + 'fx' + h + 'f';
}
fillRoulette(cases);

function fillRoulette(cases){
  var casesCarusel = $('#casesCarusel');
  var arr = cases
  var el = ''
  arr.forEach(function (item, i, arr) {
      el += '<div class="itm ' + item[0] + '">' +
          '<div class="name" data-inventory-id='+getName(item[1])+'>  ' + getName(item[1]) + ' </div> </div>'
  });
  casesCarusel.css("margin-left", "0px")
 casesCarusel.html(el);
}

function fillCarusel(cases,rotateDuration,stopPosition) {
       var casesCarusel = $('#casesCarusel');
        var arr = cases
        var el = ''
        arr.forEach(function (item, i, arr) {
            el += '<div class="itm ' + item[0] + '">' +
                '<div class="name" data-inventory-id='+getName(item[1])+'>  ' + getName(item[1]) + ' </div> </div>'
        });
        casesCarusel.css("margin-left", "0px")
       casesCarusel.html(el)
     
        $('#casesCarusel').animate({marginLeft: -1 * stopPosition}, {
            duration: rotateDuration,
            //easing: 'swing',
            start: function () {
                $('.divDisable').css({'opacity':'0.5','pointer-events':'none'});
            },
            complete: function () {
                openingCase = false;
                setTimeout(function () {
                    fullname = $( "div.itm:nth-child(21)").find(".name").html();
                    $('.divDisable').removeAttr('style');
                },3000)
            }
        })
}
// End : carusel slider 

//START : Bet amount change
$('#betAmounts').keyup(function(event) {
  if(event.which == 46 && $('#betAmounts').val().split('.').length > '1'){
        event.preventDefault();
    }
    
    if (event.which != 46 && (event.which < 48 || event.which > 59)){
      event.preventDefault();
        if ((event.which == 46) && ($(this).indexOf('.') != -1) ) {
            event.preventDefault();
        }
    }
    var betAmt = $('#betAmounts').val();
});

$(".bet-button").on("click",function(){
  amountMul($(this).data("num"));
});

function amountMul(amountVal){
          var betAmt = $('#betAmounts').val();
          if(betAmt != '' && betAmt != null && betAmt != undefined){
                       var originalBet = parseFloat(betAmt);
                       var userSelectAmountVal = parseFloat(amountVal);
                       var betAmountDouble = parseFloat(originalBet) + parseFloat(userSelectAmountVal);
                       $('#betAmounts').val(betAmountDouble);
          } else {
                      var userSelectAmountVal = parseFloat(amountVal);
                      $('#betAmounts').val(userSelectAmountVal);
          }
} 

//END : Bet amount change 
//fillCarusel();

$(".btn-play").on("click",function(){
  bettingStart($(this).data("color"));
});

//Bet amount allowed only numbers
$("#betAmounts").on("keypress",function(evt){
  evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
});
//Bet amount allowed only numbers

// Start : user betting start in game
function bettingStart(btn_click){
          var betAmt = $.trim($('#betAmounts').val());
          $("#betAmounts").val(""); //Clear bet amount after successful bet
          if(!userId){
            $.toast({heading: 'Error',text: 'Please login',position: 'top-right',icon: 'error',stack: false});
            return false;
          }else  if(betAmt==""){
            $.toast({heading: 'Error',text: 'Please enter amount',position: 'top-right',icon: 'error',stack: false});
            return false; 
          }else if(betAmt<=0){
            $.toast({heading: 'Error',text: 'Please enter valid amount',position: 'top-right',icon: 'error',stack: false});
          }else{
              socket.emit('bettingStart',{'user_id':userId,'bet_amount':betAmt,'btn_click':btn_click}, function(response){
                  if(response.status == "success"){
                         $("#betAmounts").val(""); //Clear bet amount after successful bet
                         $.toast({heading: 'Success',text: 'Bet placed successfully',position: 'top-right',icon: 'success',stack: false});
                  }else{
                         $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false});
                  } 
               });
          } 
}
// End : user betting start in game

//Start: Roulette Countdown
socket.on("rouletteGameStarted",function(response){
  if(response.status=="success"){
    fillCarusel(response.cases,
                response.rouletteRotateDuration,
                response.stopPosition);
  }
});
//End: Roulette Countdown

//Start: Roulette Countdown
socket.on("rouletteStartCountDown",function(response){
  $("#timer").html(response.count);
  $(".progress-bar-striped").css("width",response.width+"%");
});
//End: Roulette Countdown

//Start: Rolette Stopped and Update History
socket.on("rouletteStoppedUpdate",function(response){
  if($(".badges").length>=7){
    $(".history-batch-list>ul li:last").remove();
  }
  if(response.gameStoppedOn=="danger"){
      $('<li class="badges danger-gradiant">'+response.stoppedOnNumber+'</li>').insertBefore(".history-batch-list>ul li:nth-child(2)")
  }else if(response.gameStoppedOn=="green"){
      $('<li class="badges success-gradiant">'+response.stoppedOnNumber+'</li>').insertBefore(".history-batch-list>ul li:nth-child(2)")
  }else if(response.gameStoppedOn=="black"){
      $('<li class="badges dark-gradiant">'+response.stoppedOnNumber+'</li>').insertBefore(".history-batch-list>ul li:nth-child(2)")
  }
});
//End: Rolette Stopped and Update History

//Start: When user place bet then update user list
socket.on("rouletteJoinedByUser",function(response){
  let greenHtml = '';
  let redHtml = '';
  let greyHtml = '';
  for(let i=0;i<response.length;i++){
    let html = '<div class="pull-left col-md-7">'+
            '<div class="left-det"> <span> '+
            /*'<img src="frontend/img/user-win.png"> </span>'+*/
            '<img style="width:32px" src='+response[i].profile_image+'> </span>'+
              '<p>'+response[i].name+'</p>'+
            '</div>'+
          '</div>'+
          '<div class="pull-right col-md-5">'+
            '<div class="right-det">'+
              '<p><img style="width:15px" src="frontend/img/coins.png"> '+response[i].bet_amount+'</p>'+
            '</div>'+
          '</div>'+
          '<div class="clearfix"></div>'+
          '<hr>'+
           '<div class="green"></div>'+
          '<div class="clearfix"></div>';
    if(response[i].selected_color=="danger"){
      redHtml += html;
    }else if(response[i].selected_color=="black"){
      greyHtml += html;
    }else if(response[i].selected_color=="green"){
      greenHtml += html;
    }
  }
  $(".red-winner").find(".winbox-body").html(redHtml);
  $(".grey-winner").find(".winbox-body").html(greyHtml);
  $(".green-winner").find(".winbox-body").html(greenHtml);
});
//Start: When user place bet then update user list

//Start: On Roulette Game start remove all bet player
socket.on("rouletteClearBetPlayer",function(response){
  $(".winbox-body").html("");
});
//Start: On Roulette Game start remove all bet player

//Start: Update Balance of When User Bet Roulette
socket.on("rouletteBalanceAfterBet",function(response){
    if(response.user_id == userId){
      $(".user_main_balance").html(response.main_balance);
    }
});
//End: Update Balance of When User Bet Roulette



//Start: Update Balance of Win User
socket.on("rouletteWonUser",function(response){
  for(let i=0;i<response.length;i++){
    if(response[i].user_id == userId){
      $(".user_main_balance").html(parseInt(response[i].main_balance));
    }
  }
});
//Start: Update Balance of Win User

