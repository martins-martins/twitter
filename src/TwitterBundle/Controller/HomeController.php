<?php

namespace TwitterBundle\Controller;

use TwitterBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
              
class HomeController extends BaseController
{   
    // show : all_tweets|my_tweets|following|followers|all_users|search
    public function homeAction($show = 'all_tweets')
    {   
        if($this->user_id == 0) {
            return $this->redirect($this->generateUrl('_login'), 301);    
        }
        $titles = array('all_tweets'=> 'Tweets',
                        'followers'=> 'Followers',
                        'all_users'=> 'All Users',
                        'search'=> 'Search',
                        'my_tweets'=> 'My Tweets',
                        'following'=> 'Following');
        $title = $titles[$show];
        
        // insert tweet
        if(isset($_POST['tweet']) && strlen($_POST['tweet']) > 0 && strlen($_POST['tweet']) < 141) {
            
            $parent_id = isset($_POST['tweet_id'])?$_POST['tweet_id']:0;
            
            $res = mysqli_query($this->mysqli, "INSERT INTO tweets (user_id, parent_id, tweet) 
                                                VALUES (".intval($this->user_id).",
                                                ".intval($parent_id).",
                                                '".mysqli_escape_string($this->mysqli, $_POST['tweet'])."')");
                                                
            $last_id = mysqli_insert_id($this->mysqli); 
            //for($i=0;$i<10;$i++){
//                $res = mysqli_query($this->mysqli, "INSERT INTO tweets (user_id, parent_id, tweet) 
//                                                VALUES (".intval($this->user_id).",
//                                                ".intval($last_id).",
//                                                '".$i."')");
//                                                
//                $last_id = mysqli_insert_id($this->mysqli);
//            }
            return $this->redirect($this->generateUrl('_home'), 301);                                        
        }
        $message = '';
        $my_tweets_count = 0;
        $following_count = 0;
        $followers_count = 0;
        // my tweets count
        $res = mysqli_query($this->mysqli, "SELECT count(id) as created_tweet_count, 
                                                    (SELECT count(tweet_id) 
                                                     FROM retweeted 
                                                     WHERE user_id=".intval($this->user_id).") as retweeted_tweet_count  
                                            FROM tweets  
                                            WHERE user_id=".intval($this->user_id));
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $my_tweets_count = $row['created_tweet_count']+$row['retweeted_tweet_count']; 
        }
        // following count
        $res = mysqli_query($this->mysqli, "SELECT count(user_id) as following_count 
                                            FROM following 
                                            WHERE user_id_follower=".intval($this->user_id));
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $following_count = $row['following_count']; 
        }
        // followers count
        $res = mysqli_query($this->mysqli, "SELECT count(user_id) as followers_count 
                                            FROM following 
                                            WHERE user_id=".intval($this->user_id));
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $followers_count = $row['followers_count']; 
        }
        
        $tweets = array();
        $conversation = array();
        $users = array();
        $search_keyword = isset($_GET['search_keyword'])?$_GET['search_keyword']:'';
        // show tweets else users
        if($show == 'all_tweets' || $show == 'my_tweets') {
            $this->get_tweets($tweets, $conversation, $show);
            
            if(count($tweets) < 1) {
              $message = 'There are no tweets.';  
            }        
         } else {
            switch($show){
                case 'following';
                    $query = "SELECT full_name, username, id, 'following' 
                                FROM users 
                                WHERE id!=".intval($this->user_id)." AND 
                                        id IN (SELECT user_id 
                                                FROM following 
                                                WHERE users.id=user_id AND user_id_follower=".intval($this->user_id).")";
                break;
                case 'followers';
                    $query = "SELECT full_name, username, id, 
                                    (SELECT user_id 
                                    FROM following 
                                    WHERE users.id=user_id AND user_id_follower=".intval($this->user_id).") as following 
                                FROM users 
                                WHERE id!=".intval($this->user_id)." AND 
                                    id IN (SELECT user_id_follower 
                                            FROM following 
                                            WHERE users.id=user_id_follower AND user_id=".intval($this->user_id).")";    
                break;
                case 'all_users';
                    $query = "SELECT full_name, username, id, 
                                    (SELECT user_id 
                                    FROM following 
                                    WHERE users.id=user_id AND user_id_follower=".intval($this->user_id).") as following 
                                FROM users 
                                WHERE id!=".intval($this->user_id); 
                break;
                case 'search';
                    $query = "SELECT full_name, username, id, 
                                    (SELECT user_id 
                                    FROM following 
                                    WHERE users.id=user_id AND user_id_follower=".intval($this->user_id).") as following 
                                FROM users 
                                WHERE (username LIKE '%".mysqli_escape_string($this->mysqli, $search_keyword)."%') AND id!=".intval($this->user_id);
                break;    
            }
            $res = mysqli_query($this->mysqli, $query);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    
                    $users[] = array('fullname' => $row['full_name'],
                                    'username'=> $row['username'],
                                    'user_id' => $row['id'],
                                    'following' => isset($row['following']) ? 1 : 0);
                }
            } else {
                switch($show){
                    case 'all_users';
                        $message = 'There are no users.';    
                    break;
                    case 'search';
                        $message = 'Search returned no users.';
                    break;
                    case 'following';
                        $message = 'You are not following anyone.';    
                    break;
                    case 'followers';
                        $message = 'There are no followers.';
                    break;
                }   
            }
         }
         
        return $this->render('TwitterBundle:Twitter:home.html.twig',
                                array('my_tweets_count' => $my_tweets_count,
                                      'followers_count' => $followers_count,
                                      'following_count' => $following_count,
                                      'tweets' => $tweets,
                                      'users' => $users,
                                      'user_id' => $this->user_id,
                                      'user_fullname' => $this->user_fullname,
                                      'url_show_fragment' => $show,
                                      'h4_title' => $title,
                                      'search_keyword' => $search_keyword,
                                      'message' => $message));
    }
    public function deleteAction($show, $tweet_id)
    {
        if($this->user_id == 0) {
            return $this->redirect($this->generateUrl('_login'), 301);    
        }
        $query = "DELETE FROM tweets WHERE id=".intval($tweet_id)." AND user_id=".intval($this->user_id);
        mysqli_query($this->mysqli, $query);
        $query = "DELETE FROM retweeted WHERE tweet_id=".intval($tweet_id);
        mysqli_query($this->mysqli, $query);
        
        return $this->redirect($this->generateUrl('_home', array('show' => $show)), 301);   
    }
    // When clicked on "Follow/Unfollow"
    public function ajaxFollowAction($user_id = 0, $following = 0)
    {    
        if($this->user_id == 0) {
            return $this->redirect($this->generateUrl('_login'), 301);    
        }
        if($following == 1) {
           $query = "DELETE FROM following WHERE user_id=".intval($user_id)." AND user_id_follower=".intval($this->user_id); 
        } else {
           $query = "INSERT INTO following (user_id, user_id_follower) VALUES (".intval($user_id).", ".intval($this->user_id).")";     
        }
        mysqli_query($this->mysqli, $query);
        $return = 0;
        if(mysqli_affected_rows($this->mysqli) > 0) {
            $return = 1;
        }  
        $response = new Response();
        $response->setContent($return); 
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/plain');
        return $response; 

    }
    // When clicked on "Retweet/Retweeted"
    public function ajaxRetweetAction($tweet_id = 0, $retweeted = 0)
    {   
        if($this->user_id == 0) {
            return $this->redirect($this->generateUrl('_login'), 301);    
        }
        if($retweeted == 1) {
           $query = "DELETE FROM retweeted WHERE user_id=".intval($this->user_id)." AND tweet_id=".intval($tweet_id); 
        } else {
           $query = "INSERT INTO retweeted (user_id, tweet_id) VALUES (".intval($this->user_id).", ".intval($tweet_id).")";     
        }
        mysqli_query($this->mysqli, $query);
        $return = 0;
        if(mysqli_affected_rows($this->mysqli) > 0) {
            $return = 1;
        }  
        $response = new Response();
        $response->setContent($return); 
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
    
    private function get_tweets(&$tweets, &$conversation, $show, $tweet_id = 0, $recursive = 0, $ajax = 0) {
        
        $query = "SELECT tweets.id, tweets.parent_id, tweets.user_id, tweets.tweet, tweets.time_created, users.full_name, users.username, 
                        (SELECT user_id 
                            FROM retweeted 
                            WHERE tweet_id=tweets.id AND user_id=".intval($this->user_id).") as retweeted, 
                        (SELECT count(user_id) 
                            FROM retweeted 
                            WHERE tweet_id=tweets.id AND user_id IN (
                                                             SELECT user_id 
                                                             FROM following 
                                                             WHERE user_id_follower=".intval($this->user_id).")) as retweeted_by,
                        (SELECT group_concat(username SEPARATOR ', ') as users 
                            FROM retweeted LEFT JOIN users ON (retweeted.user_id=users.id)  
                            WHERE tweet_id=tweets.id AND users.id != ".intval($this->user_id)." AND user_id IN (
                                                             SELECT user_id 
                                                             FROM following 
                                                             WHERE user_id_follower=".intval($this->user_id).")) as retweeted_by_users
                FROM tweets LEFT JOIN users ON (tweets.user_id=users.id)";
        // if recursive, parent for one of the tweets is fetched
        if($recursive) {
            $query .= " WHERE tweets.id=".intval($tweet_id);    
        } else if ($show == 'all_tweets') {
        // user's created tweets 
        // tweets from users to whom current user is following 
        // retweeted tweets by users to whom current user is following         
        $query .= " 
                WHERE tweets.id IN (SELECT id 
                                    FROM tweets 
                                    WHERE tweets.user_id=".intval($this->user_id).") OR 
                                          tweets.user_id IN (SELECT user_id 
                                                             FROM following 
                                                             WHERE user_id_follower=".intval($this->user_id).") OR 
                     tweets.id IN (SELECT tweet_id 
                                    FROM retweeted 
                                    WHERE retweeted.user_id IN (SELECT user_id 
                                                             FROM following 
                                                             WHERE user_id_follower=".intval($this->user_id)."))";
        } else {
        // user's created tweets 
        // user's retweeted tweets
            $query .= " 
                WHERE tweets.id IN (SELECT id 
                                    FROM tweets 
                                    WHERE tweets.user_id=".intval($this->user_id).") OR 
                      tweets.id IN (SELECT tweet_id 
                                     FROM retweeted 
                                     WHERE user_id=".intval($this->user_id).")";    
        }
        $query .= " ORDER BY tweets.time_created DESC";
        if ($recursive == 0) {
            if($ajax == 0) {
               $page = 1;  
            } else {
                $page = $this->session->get('tweets_page');
            }
            
            $query .= " LIMIT 8 OFFSET ".($page-1)*8;
            
            $page += 1;
            $this->session->set('tweets_page', $page);
        } 
        $res = mysqli_query($this->mysqli, $query);
                
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $array = array('user_id' => $row['user_id'],
                        'tweet_id' => $row['id'],
                        'text' => $row['tweet'],
                        'fullname' => $row['full_name'],
                        'username' => $row['username'],
                        'time' => date('d-m-Y H:i', strtotime($row['time_created'])),
                        'retweeted_by' => $row['retweeted_by']!='0'?1:0,
                        'retweeted_by_users' => isset($row['retweeted_by_users']) ? $row['retweeted_by_users'] : '',
                        'conversation' => $tweet_id == $row['id'] ? 1 : 0,
                        'conversation_link' => $row['parent_id'] > 0 && $tweet_id != $row['id'] ? 1 : 0,
                        'retweeted' => isset($row['retweeted']) ? 1 : 0);
                // if recursive or parameter from URI is present, add to conversation
                if($recursive || $tweet_id == $row['id']) {
                    $conversation[] = $array;    
                } else {
                    $tweets[] = $array;     
                }
                // if current tweet belongs to conversation and parent exists, go fetch one
                if($tweet_id == $row['id'] && $row['parent_id'] > 0) {
                    $this->get_tweets($tweets, $conversation, $show, $row['parent_id'], 1);
                }
                // checks for conversation
                if(count($conversation) > 0) {
                    // on top wee need oldest tweets, so array_reverse must be performed
                    $conversation = array_reverse($conversation);
                    // merging all tweets with conversation
                    $tweets = array_merge($tweets, $conversation);
                    // after recursion, when all parents are fetched and added to all tweets, converstion array is cleared
                    $conversation = array(); 
                } 
            }
        }
        
        if($ajax == 1) {
            return $tweets;    
        }
    }
    // returns tweets for infinite scroll or conversation
    public function ajaxTweetsAction($show, $tweet_id = 0, $recursive = 0)
    {   
        $tweets = array();
        $conversation = array();
           
        $response = new Response();
        $response->setContent(json_encode($this->get_tweets($tweets, $conversation, $show, $tweet_id, $recursive, 1))); 
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }
}      
?>