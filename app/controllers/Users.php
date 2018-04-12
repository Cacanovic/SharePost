<?php 
class Users extends Controller {
	public function __construct() {
		$this->userModel=$this->model('User');
	}

	public function register(){
		//check for post
		if($_SERVER['REQUEST_METHOD']=='POST'){
			//proccess form

			//sanitaze post data
			$_POST=filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			//init data
			$data=[
				'name'=>trim($_POST['name']),
				'email'=>trim($_POST['email']),
				'password'=>trim($_POST['password']),
				'confirm_password'=>trim($_POST['confirm_password']),
				'name_err'=>'',
				'email_err'=>'',
				'password_err'=>'',
				'confirm_password_err'=>''
			];
			//VALIDATE EMAIL
			if(empty($data['email'])){
				$data['email_err']='Please enter your email';
			} else{
				//check email
				 if($this->userModel->findUserByEmail($data['email'])){
				 	$data['email_err']='Email is already taken';
				 }
			}
			//validate name 
			if(empty($data['name'])){
				$data['name_err']='Please enter your name';
			}
			//validate password 
			if(empty($data['password'])){
				$data['password_err']='Please enter your password';
			}elseif (strlen($data['password'])<6) {
				$data['password_err']='Please must be at least 6 characters ';
			}
			//validate confirm password 
			if(empty($data['confirm_password'])){
				$data['confirm_password_err']='Please confirm password';
			} elseif($data['password']!=$data['confirm_password']){
				$data['confirm_password_err']='Passwords do not match';
			}
			//make sure errors are empty
			if(empty($data['email_err'])&&empty($data['name_err'])&&empty($data['password_err'])&&empty($data['confirm_password_err'])){
				//validated

				//hash password
				$data['password']=password_hash($data['password'],PASSWORD_DEFAULT);

				//Register user
				if($this->userModel->register($data)){
					flash('register_success', 'You are now registered and can log in '  );
					redirect('users/login');

				}else {
					die('ima neka greska');
				}
				
			}else{
				//load view with errors
				$this->view('users/register',$data);
			}


		}else {
			//init data
			$data=[
				'name'=>'',
				'email'=>'',
				'password'=>'',
				'confirm_password'=>'',
				'name_err'=>'',
				'email_err'=>'',
				'password_err'=>'',
				'confirm_password_err'=>''
			];
			//load view
			$this->view('users/register',$data);
		}
	}

	public function login(){
		//check for post
		if($_SERVER['REQUEST_METHOD']=='POST'){
			//proccess form
			//sanitaze post data
			$_POST=filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			//init data
			$data=[
				
				'email'=>trim($_POST['email']),
				'password'=>trim($_POST['password']),				
				'email_err'=>'',
				'password_err'=>''
				
			];

			//VALIDATE EMAIL
			if(empty($data['email'])){
				$data['email_err']='Please enter your email';
			}

			//VALIDATE password
			if(empty($data['password'])){
				$data['password_err']='Please enter password';
			}

			//check for user email
			if($this->userModel->findUserByEmail($data['email'])){
				//user found
			}else{
				//user not found
				$data['email_err']='No user found';
			}

			//make sure errors are empty
			if(empty($data['email_err'])&&empty($data['password_err'])){
				//validated
				//check and set logged in user
				$loggedInUser=$this->userModel->login($data['email'],$data['password']);

				if($loggedInUser){
					//create session
					$this->createUserSession($loggedInUser);
				}else{
					$data['password_err']='Password incorect';
					$this->view('users/login',$data);
				}
			}else{
				//load view with errors
				$this->view('users/login',$data);
			}

		}else {
			//init data
			$data=[
				'email'=>'',
				'password'=>'',			
				'email_err'=>'',
				'password_err'=>''			
			];
			//load view
			$this->view('users/login',$data);
		}
	}

	public function createUserSession($user){
		$_SESSION['user_id']=$user->id;
		$_SESSION['user_email']=$user->email;
		$_SESSION['user_name']=$user->name;
		redirect('posts');
	}

	public function logout(){
		unset($_SESSION['user_id']);
		unset($_SESSION['user_email']);
		unset($_SESSION['user_name']);
		session_destroy();
		redirect('users/login');
	}

	



}