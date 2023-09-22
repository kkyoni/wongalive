$('.clos').click(function(){
    $('.online_users_chat').remove();

    var chat_title = $(".title").innerHeight();
    var messge_height = $(".message_write").innerHeight();
    var chat_rules = $(".text_colertext").innerHeight();
    var online_users = $(".online_users_chat").innerHeight();
    if(userId == 0){
        var totalH = parseInt(chat_title+messge_height+22);
        var totalVal = parseInt(totalH-online_users);
    } else {
        var totalHH = parseInt(chat_title+messge_height+chat_rules+44);
        var totalVal = parseInt(totalHH-online_users);
    }

    /*var totalVal = parseInt(chat_title+messge_height+chat_rules+44);*/
    var h = $(window).height()-totalVal;
    if ($(window).width() < 767) {
        $('#chatscroll').slimScroll({
            //height: 'auto',
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '80px',
        });
    } else {
       $('#chatscroll').slimScroll({
            //height: 'auto',
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '0px',
        });
    }
});

$(document).ready(function(){    
    var chat_title = $(".title").innerHeight();
    var messge_height = $(".message_write").innerHeight();
    var chat_rules = $(".text_colertext").innerHeight();
    var online_users = $(".online_users_chat").innerHeight();
    if(userId == 0){
        var totalVal = parseInt(chat_title+messge_height+online_users+22);
    } else {
        var totalVal = parseInt(chat_title+messge_height+chat_rules+online_users+44);
    }

    /*var totalVal = parseInt(chat_title+messge_height+chat_rules+44);*/
    var h = $(window).height()-totalVal;
    if ($(window).width() < 767) {
        $('#chatscroll').slimScroll({
            //height: 'auto',
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '80px',
        });
    } else {
       $('#chatscroll').slimScroll({
            //height: 'auto',
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '0px',
        });
    }

    /*$('#chatscroll').slimscroll({ scrollBy: '80px' });*/
});

$(window).resize(function() {
    var chat_title = $(".title").innerHeight();
    var messge_height = $(".message_write").innerHeight();
    var chat_rules = $(".text_colertext").innerHeight();
    var online_users = $(".online_users_chat").innerHeight();

    if(userId == 0){
        var totalVal = parseInt(chat_title+messge_height+online_users+22);
    } else {
        var totalVal = parseInt(chat_title+messge_height+chat_rules+online_users+44);
    }

    /*var totalVal = parseInt(chat_title+messge_height+chat_rules+44);*/
    var h = $(window).height()-totalVal;
    if($(window).width() < 767) {
        $('#chatscroll').slimScroll({
            //height: 'auto',
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '80px'
        });
    }else{
        $('#chatscroll').slimScroll({
            height: $('#chatscroll').css({'height': h+"px"}),
            start: 'bottom',
            width: '100%',
            adisableFadeOut: 'true',
            allowPageScroll: 'true',
            scrollBy: '80px'
        });
    }
});


//START: Send chat message when enter key press
$('.chat_message_div').on("keypress", function(e) {
    if (e.keyCode == 13) {
        chatMessageSave();
    }
});
//END: Send chat message when enter key press

//START: Send chat message when click on send button
$('.chat_msg_send').click(function(){
    chatMessageSave();   
});
//END: Send chat message when click on send button

$('.modelHideCoinflip').click(function(){
    $('#coinflip_provablyfair').modal('hide')
});

$('.modelHideRoulette').click(function(){
    $('#roulette_provablyfair').modal('hide')
});

$('[data-dismiss=modal]').on('click', function (e) {
      var checkedValue = $("input:checkbox[name=acceptTermAndCondition]:checked").val(); 
      if(checkedValue == '0'){
      } else if(checkedValue == '' || checkedValue == null || checkedValue == undefined){
             $.toast({heading: 'Error',text: "Please accept rules then you can chat.",position: 'top-right',icon: 'error',stack: false}); 
      } else {
             var data = {'userId':userId, 'checkedValue':checkedValue};
             socket.emit('chatrules', data, function(response){
                if(response.status == "success"){
                    $(".removeLi").hide();
                    $("#removeDiv").remove();
                    $(".chet_rul").addClass("afterremovediv");
                    $('#acceptTermAndCondition').val(0);
                    $.toast({heading: 'Success',text: response.message,position: 'top-right',icon: 'success',stack: false}); 
                }else{
                   $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false}); 
                }
            });     
      }  
     
})

function chatMessageSave(){
    
    if(userId != "" && userId != 0 && userId != null && userId != undefined){
        var message = $('.chat_message_div').text();
        $('.chat_message_div').text('');
        if(message != ""){
            var data = {'userId':userId, 'message':message};
            socket.emit('chatMessageSave', data, function(response){
                if(response.status == "success"){
                    $('.chat_message_div').text('');
                    //$.toast({heading: 'Success',text: response.message,position: 'top-right',icon: 'success',stack: false}); 
                }else{
                    $.toast({heading: 'Error',text: response.message,position: 'top-right',icon: 'error',stack: false}); 
                }
            });
        }else{
            $.toast({heading: 'Error',text: 'Please enter message',position: 'top-right',icon: 'error',stack: false}); 
        }
    }else{
        $.toast({heading: 'Error',text: 'Please login',position: 'top-right',icon: 'error',stack: false});
    } 

    
}

//START: Socket call to getting chat messages
socket.emit('getMessages',async function(response){
    if(response.status == "success"){
        var chatHtml = ''; 
        for(var i=0; i<response.data.length; i++){
            var detail = response.data[i];
            chatHtml += await chatMessageHtml(detail);
        }
        $('.chat_message_list').html(chatHtml);
    }

     //$('#chatscroll').html(html);
    setTimeout(function(){
        var height = 10;
        $('.list-unstyled').find('li').each(function(){
            height += $(this).outerHeight();
        })
        $("#chatscroll").slimScroll({scrollTo:(parseInt(height))+'px'})
    }, 100)

});
//END: Socket call to getting chat messages

socket.emit('onlineUserCount', function(response){});

// Socket Count 
socket.on('countAllOnlineUser', async function(response){
    $('.online_users').text(response.data);
});
//end: Socket Count 


//START: Socket emit from server new user new message enter
socket.on('newChatMessage', async function(response){
    var chatHtml = '';
    if(response.data.length){
        for(let i=0;i<response.data.length;i++){
            chatHtml += await chatMessageHtml(response.data[i]);
        }
    }
    $('.chat_message_list').append(chatHtml);
    //$('#chatscroll').slimscroll({ scrollBy: '80px' });
        var height = 10;
        $('.list-unstyled').find('li').each(function(){
            height += $(this).outerHeight();
        })
    $("#chatscroll").slimScroll({scrollTo:(parseInt(height))+'px'})

});
//end: Socket emit from server new user new message enter

//START: Chat html create
function chatMessageHtml(detail){
    let name,profilePicc;
    if(detail.anymos == 1){
        name = 'Anonymous';
        profilePicc = baseUrl+'frontend/upload/user/anymos.jpg';
    } else if(detail.userDetail.profile_image == 'default.png'){
             name = detail.userDetail.name;
             profilePicc = baseUrl+'frontend/upload/user/'+detail.userDetail.profile_image;
    }else{
        name = detail.userDetail.name;
        profilePicc = detail.userDetail.profile_image;
    }

    var moderatorUser = '';
    var chatClose = '';
    if(detail.userDetail.moderator_type == 'moderator'){
            moderatorUser = "moderator"
    } else {
            moderatorUser = '';
    }

    if(userType == "moderator"){
             if(userId != "" && userId != 0 && userId != null && userId != undefined){
                    if(detail.userDetail.moderator_type == 'moderator'){
                            chatClose = '';
                    } else {
                            chatClose = '<a href="/profile/userchatdelete/'+detail.id+'"><i class="fa fa-times closeSignChange"></i></a>'; 
                    }        
            }
    } else {
            chatClose = '';
    }

    html = '<li class="left clearfix">';
    html += '<div class="chat-img pull-left">';
    html += '<img src="'+profilePicc+'"  alt="'+name+'">';
    html += '</div>';
    html += '<div class="chat-details">';
    html += '<div class="chat-user-name">';
    html += '<span class="cun-left '+moderatorUser+'">'+name+'</span>';
    html += '<span class="cun-right">'+moment(detail.created_at).format('hh:mm a')+" "+chatClose+' </span>';
    html += '</div>';
    html += '<div class="chat-body clearfix">';
    html += '<div class="header_sec">'+detail.chat_message+'</div>';
    html += '</div>';
    html += '</div>';
    html += '</li>';

    return html;

    
}
//END: Chat html create
