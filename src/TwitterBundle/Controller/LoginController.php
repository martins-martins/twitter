<?php

namespace TwitterBundle\Controller;

use TwitterBundle\Controller\BaseController;
              
class LoginController extends BaseController
{    
    public function indexAction($activation_hash = '')
    {
        if($this->user_id > 0) {
            return $this->redirect($this->generateUrl('_home'), 301);    
        }
        $success_message = '';
        $fail_message = '';
        
        if($activation_hash != '') {
            
             $res = mysqli_query($this->mysqli, "UPDATE users 
                                                    SET activated='1', hash='' 
                                                    WHERE hash='".mysqli_escape_string($this->mysqli, $activation_hash)."'");
             if(mysqli_affected_rows($this->mysqli)==1) {
                $success_message = 'Account activated. You can now log in.'; 
             } else {
                $fail_message = 'Invalid activation code'; 
             }   
        } 
        if(isset($_POST['username'])){
            $user = $_POST['username']; 
            $pass = $_POST['password'];
            $valid = false;
            if(strlen($user) > 0 && strlen($pass) > 0) {
               
                $res = mysqli_query($this->mysqli, "SELECT id, username, full_name, activated 
                                                    FROM users 
                                                    WHERE username='".mysqli_escape_string($this->mysqli, $user)."' AND 
                                                          password='".md5($pass)."'");
                if (mysqli_num_rows($res) > 0) {
                    $row = mysqli_fetch_assoc($res);
                    if($row['activated'] == '1') {                  
                        $valid = true;
                        
                        $this->session->set('user_id', $row['id']);
                        $this->session->set('username', $row['username']);
                        $this->session->set('fullname', $row['full_name']);
                    } else {
                      $fail_message = 'Account not activated';  
                    }
                } else {
                    $fail_message = 'Wrong username/password';    
                }    
            } else {
               $fail_message = 'Wrong username/password';  
            }
            if($valid) {
              return $this->redirect($this->generateUrl('_home'), 301);  
            }
        }
        
         return $this->render('TwitterBundle:Twitter:login.html.twig', array('success_message'=>$success_message,
                                                                             'fail_message'=>$fail_message));    
    }
                         
    public function signupAction()
    {
        if($this->user_id > 0) {
            return $this->redirect($this->generateUrl('_home'), 301);    
        }
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        
        $validated = false;
        $errors = array('fullname' => '',
                        'email' => '',
                        'password' => '',
                        'username' => '');
                        
        if (isset($_POST['username'])) {
            $validated = true;
            if(strlen($fullname) < 1) {
                $errors['fullname'] = 'Full name must be at least 1 character long.';
                $validated = false;    
            } elseif(strlen($fullname) > 50) {
                $errors['fullname'] = 'Full name must be shorter than 51 characters.';
                $validated = false;    
            }
            if($validated) {
                $res = mysqli_query($this->mysqli, "SELECT id FROM users WHERE full_name='".mysqli_escape_string($this->mysqli, $fullname)."'");
                if (mysqli_num_rows($res) > 0) {
                    $errors['fullname'] = 'Account already exists with Full name : '.$fullname.'.';
                    $validated = false;    
                }
            }
            if(strlen($email) > 50) {
                $errors['email'] = 'Email must be shorter than 51 characters.';
                $validated = false;    
            }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email is invalid.';
                $validated = false;
            }
            if($errors['email'] == '') {
                $res = mysqli_query($this->mysqli, "SELECT id FROM users WHERE email='".mysqli_escape_string($this->mysqli, $email)."'");
                if (mysqli_num_rows($res) > 0) {
                    $errors['email'] = 'Account already exists with email : '.$email.'. Please choose another email.';
                    $validated = false;    
                }
            }
            if(strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters long.';
                $validated = false;    
            } elseif ($password != $password2) {
                $errors['password'] = 'Passwords do not match.';
                $validated = false;    
            }
            if(strlen($username) < 1) {
                $errors['username'] = 'Username must be at least 1 character long.';
                $validated = false;    
            } elseif(strlen($username) > 50) {
                $errors['username'] = 'Username must be shorter than 51 characters.';
                $validated = false;    
            }
            if($errors['username'] == '') {
                $res = mysqli_query($this->mysqli, "SELECT id FROM users WHERE username='".mysqli_escape_string($this->mysqli, $username)."'");
                if (mysqli_num_rows($res) > 0) {
                    $errors['username'] = 'Account already exists with username : '.$username.'.';
                    $validated = false;    
                }
            }
        }
        
        $created_message = '';
        if($validated) {
            $activation_hash = md5(time().$username);
            $res = mysqli_query($this->mysqli, "INSERT INTO users (username, password, full_name, email, hash) 
                                                            VALUES ('".mysqli_escape_string($this->mysqli, $username)."',
                                                            '".md5(mysqli_escape_string($this->mysqli, $password))."',
                                                            '".mysqli_escape_string($this->mysqli, $fullname)."',
                                                            '".mysqli_escape_string($this->mysqli, $email)."','".$activation_hash."')");
            $url = $this->generateUrl('_activate',
                                        array('activation_hash' => $activation_hash), true);
            echo $url;
            $message = "Hello ".$fullname." !
                        Click on link to activate your Twitter account : <a href=".$url.">activate</a>";
            $mail_sent = mail($email, 'Activation of your new Twitter account', $message);
            if($mail_sent) {
                $created_message = 'Account created. Message with activation link sent to '.$email; 
            }
            $fullname = $email = $password = $password2 = $username = '';                                                
        }
        return $this->render('TwitterBundle:Twitter:signup.html.twig',
                            array('user' => array('fullname' => $fullname,
                                                  'email' => $email,
                                                  'password' => $password,
                                                  'password2' => $password2,
                                                  'username' => $username,),
                                'created_message' => $created_message,
                                'errors' => $errors));
    }
    
    public function logoutAction()
    {
        $this->session->invalidate();
        return $this->redirect($this->generateUrl('_login'), 301);   
    }
}      
?>