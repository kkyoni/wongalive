
function getgameDetail(id){

        var finalGame = '';
        if(id != '' && id != undefined && id != null){
              finalGame = id;
        } else {
              var gamedata = $('#allHashGame').val();
              finalGame = gamedata;
        }

          var data = {"gamehash":finalGame};
          socket.emit('hashGameDetail',data,function(response){
                    if(response.status == 'fail'){
                          $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false});
                    } else if(response.status == 'success') {
                          if(response.data != null){
                              $('.error').hide();
                              $('.succ').show();
                              $('.gamehash').val(response.data.game_hash);
                              $("#gameNumber").text(response.data.game_number);
                              $("#gamehash").text(response.data.game_hash);
                              $("#finalGmaehash").text('SHA224('+response.data.round_number+'-'+response.data.hash_salt+')');
                              $("#games").text(response.data.game_hash);
                              $("#serverSeed").text(response.data.hash_salt);
                              $("#roundNumber").text(response.data.round_number);
                              $("#randomstr").text(response.data.random_str);
                              $("#random_signature").text(response.data.random_signature);
                          } else {
                              $('.error').show();
                              $('.succ').hide();
                              $("#noDataFOund").text("Game Detail Not Found");
                          }
                    }
          });
}

function getgameDetailRoulette(id){

           var finalGame = '';
          if(id != '' && id != undefined && id != null){
                finalGame = id;
          } else {
                var gamedata = $('#allHashGame').val();
                finalGame = gamedata;
          }


          var data = {"gamehash":finalGame};
          socket.emit('hashGameDetailRoulette',data,function(response){
                    if(response.status == 'fail'){
                          $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false});
                    } else if(response.status == 'success') {
                          if(response.data != null){
                              $('.error').hide();
                              $('.succ').show();
                              $('.gamehash').val(response.data.game_hash);
                              $("#gameNumber").text(response.data.game_number);
                              $("#gamehash").text(response.data.game_hash);
                              $("#finalGmaehash").text('SHA224('+response.data.round_number+'-'+response.data.hash_salt+')');
                              $("#games").text(response.data.game_hash);
                              $("#serverSeed").text(response.data.hash_salt);
                              $("#roundNumber").text(response.data.round_number);
                              $("#randomstr").text(response.data.random_str);
                              $("#random_signature").text(response.data.random_signature);
                              
                          } else {
                              $('.error').show();
                              $('.succ').hide();
                              $("#noDataFOund").text("Game Detail Not Found");
                          }
                    }
          });
}

//START: New game html create and append into coinflip game listing
socket.on('depositInventory', function(response){
          if(response.userids == userId){
                if(response.status == 'fail'){
                      $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false});
                } else if(response.status == 'success') {
                       var totalAmt = response.data; 
                       $("#addURL").html('');
                       $(".user_main_balance").text(totalAmt);
                       $("#depositCoinsChange").text(totalAmt);
                      $.toast({heading: 'Success',text:response.message,position: 'top-right',icon: 'success',stack: false});
                }
          }
});
//END: New game html create and append into coinflip game listing


//START: New game html create and append into coinflip game listing
socket.on('depositCoinpayment', function(response){
          if(response.userids == userId){
                if(response.status == 'fail'){
                      $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false});
                } else if(response.status == 'success') {
                       var totalAmt = response.data; 
                       $("#addURL").html('');
                       $(".user_main_balance").text(totalAmt);
                       $("#depositCoinsChange").text(totalAmt);
                       $('#deposit_md234').modal('hide');
                      $.toast({heading: 'Success',text:response.message,position: 'top-right',icon: 'success',stack: false});
                }
          }
});
//END: New game html create and append into coinflip game listing

