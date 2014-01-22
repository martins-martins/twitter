<?php

namespace TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
              
class BaseController extends Controller
{
    protected $mysqli;
    protected $session;
    protected $user_id;
    protected $user_fullname;
    
    function __construct() {
        $this->session = new Session();
        $this->session->start();
        $this->user_id = 0;
        if($this->session->has('user_id')) {
           $this->user_id = $this->session->get('user_id');  
           $this->user_fullname = $this->session->get('fullname');  
        }
        $this->mysqli = mysqli_connect("localhost", "root", "", "twitter"); 
    }
}
      
?>