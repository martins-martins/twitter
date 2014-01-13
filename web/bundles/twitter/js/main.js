$(document).ready(function(){

    $(".profile-box textarea[name=tweet], div.tweet textarea[name=tweet]").keyup(function(){ 
        var length = $(this).val().length; 
        if(length>0) {
            $(this).parent().parent().find("input[name=post_tweet]").attr('disabled',false);    
        } else {
            $(this).parent().parent().find("input[name=post_tweet]").attr('disabled','disabled');    
        }
        var remaining = 140-length; 
        $(this).parent().parent().find(".counter").html(remaining);
    });
    
    $('.reply').click(function(e){
        e.preventDefault();
        $(this).parent().find('form').toggle(0,function(){
            if($(this).css('display')=='none'){
              $(this).parent().find('.reply').html('Reply');    
            } else {
              $(this).parent().find('.reply').html('Hide');  
            }    
        });    
    }) ;
    // follow, unfollow
    $('a.follow').click(function(e){
        e.preventDefault();
        var following = $(this).attr('following');
        var href_parts = $(this).attr('href').split("/");
        href_parts.pop();  
        $.ajax({
          url: $(this).attr('href'),
          context: this                 
        }).done(function(data) {
            // 1 success
            data = 1;
            if(data==1) {
                var following_count =  $('#following_count').html();
                if(following==1) {
                    following = 0;
                    $(this).html('Follow');
                    $('#following_count').html(parseInt(following_count)-1);    
                } else {
                    following = 1; 
                    $(this).html('Unfollow');
                    $('#following_count').html(parseInt(following_count)+1);     
                }
                $(this).attr('following', following);
                $(this).attr('href', href_parts.join('/')+'/'+following);   
            }
        });
    });
    // retweet, unretweet
    $('a.retweet').click(function(e){
        e.preventDefault();
        var retweeted = $(this).attr('retweeted');
        var href_parts = $(this).attr('href').split("/");
        href_parts.pop();
        $.ajax({
          url: $(this).attr('href'),
          context: this                 
        }).done(function(data) {
            // 1 success
            if(data==1) {
                var my_tweets_count =  $('#my_tweets_count').html();
                if(retweeted==1) {
                    retweeted = 0;
                    $(this).html('Retweet');
                    $('#my_tweets_count').html(parseInt(my_tweets_count)-1);   
                } else {
                    retweeted = 1;
                    $(this).html('Retweeted');
                    $('#my_tweets_count').html(parseInt(my_tweets_count)+1);    
                }
                $(this).attr('retweeted', retweeted);
                $(this).attr('href', href_parts.join('/')+'/'+retweeted);   
            }
        });  
    });   
});