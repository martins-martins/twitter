$(document).ready(function($){

  function tweet_limit() {
    var length = $(this).val().length; 
    if(length>0) {
      $(this).parent().parent().find("input[name=post_tweet]").attr('disabled',false);    
    } else {
      $(this).parent().parent().find("input[name=post_tweet]").attr('disabled','disabled');    
    }
    var remaining = 140-length; 
    $(this).parent().parent().find(".counter").html(remaining);
  }
  $(".profile-box textarea[name=tweet], div.tweet textarea[name=tweet]").keyup(tweet_limit);

  function reply(e) {
    e.preventDefault();
    $(this).parent().find('form').toggle(0,function(){
      if($(this).css('display')=='none'){
        $(this).parent().find('.reply').html('Reply');    
      } else {
        $(this).parent().find('.reply').html('Hide');  
      }    
    });
  }
  $('.reply').click(reply) ;
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
  function retweet_unretweet(e) {
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
  } 
  $('a.retweet').click(retweet_unretweet);

  function parse_tweet_template(tweet, ajax_loader){
    var user_id = ajax_loader.attr('user_id'); 
    var tweet_template = '<div class="tweet" id="">' +
      '<div class="tweet-user">' +
      '<span class="tweet-user-fullname"></span>' +
      '<span class="light-grey"></span>' +
      '<span class="tweet-time"></span>' +
      '</div>' +
      '<p></p>' +
      '<a href="#" class="reply">Reply</a>' +
      '<form method="post" action="" role="form" style="display: none;">' +
      '<input type="hidden" name="tweet_id" value="">' +
      '<div class="form-group">' +
      '<textarea name="tweet" cols="40" rows="4" class="form-control" maxlength="140"></textarea>' +
      '</div>' +
      '<input name="post_tweet" disabled="disabled" type="submit" class="btn btn-default" value="Reply">' +
      '<span class="counter">140</span>' +
      '</form>' +
      '</div>';

    var tweet_div = $(tweet_template);
    tweet_div.attr('id', tweet["tweet_id"]);
    if(tweet["conversation"] == 1) {
      tweet_div.addClass("tweet-conversation");    
    }
    tweet_div.find('.tweet-user span:first').html(tweet["fullname"]);
    tweet_div.find('.tweet-user span.light-grey').html(tweet["username"]);
    tweet_div.find('.tweet-user span.tweet-time').html(tweet["time"]);
    tweet_div.find('p').html(tweet["text"]);
    if(tweet["retweeted_by"] == 1) {
      tweet_div.find('p').after('<p class="light-grey">Retweeted by ' + tweet["retweeted_by_users"] + '</p>');    
    }
    // conversation
    if(tweet["conversation_link"] == 1) {
      var tweets_conversation_link = ajax_loader.attr('tweets_link');
      var tweets_conversation_link_parts = tweets_conversation_link.split('/');
      tweets_conversation_link_parts.pop();
      tweets_conversation_link_parts.pop();
      tweet_div.find('p:last').after('<a href="' + tweets_conversation_link_parts.join('/') + '/' + tweet["tweet_id"] + '/1' + '" class="show-conversation">Show conversation</a>');        
      tweet_div.find('a.show-conversation').click(show_conversation);  
    }
    // reply, textarea
    tweet_div.find('a.reply').click(reply);
    tweet_div.find('input[name=tweet_id]').val(tweet["tweet_id"]);
    tweet_div.find('textarea[name=tweet]').keyup(tweet_limit);
    // retweeting
    if (user_id != tweet["user_id"]) {
      var retweet_link = ajax_loader.attr('retweet_link');
      var retweet_link_parts = retweet_link.split('/');
      retweet_link_parts.pop();
      retweet_link_parts.pop();
      var link_title = 'Retweet';
      if(tweet["retweeted"]) {
        link_title = 'Retweeted';    
      } 
      tweet_div.find('a.reply').before('<a href="' + retweet_link_parts.join('/') + '/' + tweet["tweet_id"] + '/' + tweet["retweeted"] + '" retweeted="' + tweet["retweeted"] + '" class="retweet">' + link_title + '</a>');         
      tweet_div.find('a.retweet').click(retweet_unretweet);
    }
    if (user_id == tweet["user_id"]) {
      var delete_link = ajax_loader.attr('delete_link');
      var delete_link_parts = delete_link.split('/');
      delete_link_parts.pop();
      tweet_div.find('a.reply').after('<a href="' + delete_link_parts.join('/') + '/' + tweet["tweet_id"] + '" class="delete">Delete</a>'); 
    }               
    return tweet_div;    
  }
  // show conversation function for assigning to links
  function show_conversation(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      context: this                 
    }).done(function(tweets) {
      // 1 success
      if(tweets.length > 0)
      { 
        var ajax_loader = $('div#loadmoreajaxloader');
        //var conversation = $('');
        for (index in tweets) {
          if (tweets.hasOwnProperty(index)) {
            var parsed_tweet_template = parse_tweet_template(tweets[index], ajax_loader);
            
            $(this).parent().before(parsed_tweet_template);
          }
        }
      }
    });
  }
  // clicking on "Show conversation"  
  $('a.show-conversation').click(show_conversation);
  // infinite scroll
  var miliseconds_interval = 51;
  var miliseconds;
  var miliseconds2;
  var more_tweets = true; 

  $(window).scroll(function()
    {      
      miliseconds = new Date().getTime();
      var ajax_loader = $('div#loadmoreajaxloader'); 
      if(($(document).height() - $(window).height()) == $(window).scrollTop() && $('div.tweet').length > 0 && more_tweets && miliseconds_interval > 50)
      {
        miliseconds2 = new Date().getTime();
        miliseconds_interval = miliseconds2 - miliseconds;
        ajax_loader.show();
        $.ajax({
          url: ajax_loader.attr('tweets_link'),
          success: function(tweets)
          {      
            if(tweets.length > 0)
            { 
              for (index in tweets) {
                if (tweets.hasOwnProperty(index)) {
                  var parsed_tweet_template = parse_tweet_template(tweets[index], ajax_loader);
                  ajax_loader.before(parsed_tweet_template);
                }
              }
              ajax_loader.hide();

              miliseconds2 = new Date().getTime();
              miliseconds_interval = miliseconds2 - miliseconds;

              if (tweets.length < 8) {
                more_tweets = false;
              }
            } else {
              more_tweets = false;
              ajax_loader.hide();
            }
          },
          error: function(){
            more_tweets = false;
            ajax_loader.hide();
          },
        });
      }
  });  
});