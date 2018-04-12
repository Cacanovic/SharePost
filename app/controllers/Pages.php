<?php
class Pages extends Controller{
	public function __construct(){
		
	}
	public function index(){
		 if(isLoggedIn){
		 	redirect('posts');
		 }
		$data=[
		'title'=>'SharePosts',
		'description'=>'Simple social network build on the MVC php FREAMWORK'
		]; 

		$this->view('pages/index', $data);
	}
	public function about(){
		$data=[
		'title'=>'About',
		'description'=>'App to share posts with other users!'
		];
		$this->view('pages/about', $data);
		 
	}
}