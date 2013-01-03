<?php
require("model.php");
require("router.php");

class Flash{
    
    public $msg;
    public $type;
    
    function __construct($msg, $type)
    {
        $this->msg = $msg;
        $this->type = $type;
    }
    
    public function display(){
        echo "<div class=\"flash " . $this->type . "\">" . $this->msg . "</div>";
    }
}

class Controller{
	
//--------Variables------------
	private $model;
	private $router;
//--------Functions------------
	
	//Constructor
	function __construct(){
		//initialize private variables
		$this->model = new Model();
		$this->router = new Router();
		
        //Proccess Query String
        if(strlen($_GET['query']) > 0)
        {
            $queryParams = explode("/", $_GET['query']);
        }
        else{
            $queryParams = false;
        }
        
        $page = $_GET['page'];
        
		//Handle Page Load
		$endpoint = $this->router->lookup($page);
		if($endpoint === false)
		{
			header("HTTP/1.0 404 Not Found");
		}
		else
		{
            $this->$endpoint($queryParams);
            
		}
	}
    private function redirect($url){
        header("Location: /" . $url);
    }
	
	//--- Framework Functions
	private function loadView($view, $data = null){
		if(is_array($data))
		{
			extract($data);
		}
		require("Views/" . $view . ".php");
	}
	private function loadPage($user, $view, $data = null, $flash = false){
        $this->loadView("header", array('User' => $user));
        if($flash !== false)
        {
            $flash->display();
        }
        $this->loadView($view, $data);
        $this->loadView("footer");
    }
	//--- Security Functions
	private function checkAuth(){
        if(isset($_COOKIE['Auth']))
        {
            return $this->model->userForAuth($_COOKIE['Auth']);
        }
        else
        {
            return false;
        }
	}
	
	private function indexPage($params){
        $user = $this->checkAuth();
        if($user !== false){ $this->redirect("buddies"); }
        else
        {
            $flash = false;
            if($params !== false)
            {
                $flashArr = array(
                    "0" => new Flash("Your Username and/or Password was incorrect.", "error"),
                    "1" => new Flash("There's already a user with that email address.", "error"),
                    "2" => new Flash("That username has already been taken.", "error"),
                    "3" => new Flash("Passwords don't match.", "error"),
                    "4" => new Flash("Your Password must be at least 6 characters long.", "error"),
                    "5" => new Flash("You must enter a valid Email address.", "error"),
                    "6" => new Flash("You must enter a username.", "error"),
                    "7" => new Flash("You have to be signed in to acces that page.", "warning")
                );
                $flash = $flashArr[$params[0]];
            }
            $this->loadPage($user, "home", array(), $flash);
        }
	}

	private function signUp(){
        if($_POST['email'] == "" || strpos($_POST['email'], "@") === false){
            $this->redirect("home/5");
        }
        else if($_POST['username'] == ""){
            $this->redirect("home/6");
        }
        else if(strlen($_POST['password']) < 6)
        {
            $this->redirect("home/4");
        }
        else if($_POST['password'] != $_POST['password2'])
        {
            $this->redirect("home/3");
        }
        else{
            $pass = hash('sha256', $_POST['password']);
            $signupInfo = array(
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $pass,
                'name' => $_POST['name']
            );
            $resp = $this->model->signupUser($signupInfo);
            if($resp === true)
            {
                $this->redirect("buddies/1");
            }
            else
            {
                $this->redirect("home/" . $resp); 
            }
        }
    }
    
    private function login(){
        $pass = hash('sha256', $_POST['password']);
		$loginInfo = array(
			'username' => $_POST['username'],
			'password' => $pass
		);
		if($this->model->attemptLogin($loginInfo))
        {
            $this->redirect("buddies/0");
        }
        else
        {
            $this->redirect("home/0");
        }
	}
	
	private function logout(){
        $this->model->logoutUser($_COOKIE['Auth']);
        $this->redirect("home");
    }
    
    private function buddies($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else
        {
            $userData = $this->model->getUserInfo($user);
            $fribbits = $this->model->getFollowersRibbits($user);
            $flash = false;
            if(isset($params[0]))
            {
                $flashArr = array(
                    "0" => new Flash("Welcome Back, " . $user->name, "notice"),
                    "1" => new Flash("Welcome to Ribbit, Thanks for signing up.", "notice"),
                    "2" => new Flash("You have exceeded the 140 character limit for Ribbits", "error")
                );
                $flash = $flashArr[$params[0]];
            }
            $this->loadPage($user, "buddies", array('User' => $user, "userData" => $userData, "fribbits" => $fribbits), $flash);
        }
    }
    
    private function newRibbit($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $text = mysql_real_escape_string($_POST['text']);
            if(strlen($text) > 140)
            {
                $this->redirect("buddies/2");
            }
            else
            {
                $this->model->postRibbit($user, $text);
                $this->redirect("buddies");
            }
        }
        
    }
    
    private function publicPage($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else
        {
            $q = false;
            if(isset($_POST['query']))
            {
                $q = $_POST['query'];
            }
            $ribbits = $this->model->getPublicRibbits($q);
            $this->loadPage($user, "public", array('ribbits' => $ribbits));
        }
    }
    
    private function profiles($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $q = false;
            if(isset($_POST['query']))
            {
                $q = $_POST['query'];
            }
            $profiles = $this->model->getPublicProfiles($user, $q);
            $this->loadPage($user, "profiles", array('profiles' => $profiles));
        }
    }
    private function follow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->follow($user, $params[0]);
            $this->redirect("profiles");
        }
    }

    private function unfollow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->unfollow($user, $params[0]);
            $this->redirect("profiles");
        }
    }
    
	/*
    
    
	
	
	
    

    

    
    private function unfollow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->unfollow($user, $params[0]);
            $this->redirect("profiles");
        }
    }
    
    private function follow($params){
        $user = $this->checkAuth();
        if($user === false){ $this->redirect("home/7"); }
        else{
            $this->model->follow($user, $params[0]);
            $this->redirect("profiles");
        }
    }
    
    */
}