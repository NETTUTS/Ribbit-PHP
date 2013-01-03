<?php

class Router{
	private $routes;
	
	function __construct(){
		$this->routes = array(
			"home" => "indexPage",
            "signup" => "signUp",
            "login" => "login",
            "buddies" => "buddies",
            "ribbit" => "newRibbit",
            "logout" => "logout",
            "public" => "publicPage",
            "profiles" => "profiles",
            "unfollow" => "unfollow",
            "follow" => "follow"
		);
	}
	
	public function lookup($query)
	{
		if(array_key_exists($query, $this->routes))
		{
			return $this->routes[$query];
		}
		else
		{
			return false;
		}
	}
}