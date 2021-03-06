<?php defined('BASEPATH') OR exit('No direct script access allowed');
class accounts extends CI_Controller
{
    function __construct() {
		parent::__construct();

		$this->load->library('facebook');
		$this->load->model('login_tokens','login_tokens');
		$this->load->library('login');
		$this->load->library('mail');
		$this->load->model('users','users');
		$this->load->model('auth');
		$this->load->model('upload');
		$this->load->model('about','about');
		$this->load->model('password_tokens');
    }
    
    public function index(){
		
		if(!$this->login->isLoggedIn()){
		$userData = array();
		// Check if user is logged in
		if($this->facebook->is_authenticated()){
			// Get user facebook profile details
			$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');
            
            // Preparing data for database insertion
            //if new user ccontinue
            if($userProfile['id']!=null){
	            $userData['oauth_provider'] = 'facebook';
	            $userData['oauth_uid'] = $userProfile['id'];
	            $userData['first_name'] = $userProfile['first_name'];
	            $userData['last_name'] = $userProfile['last_name'];
	            $userData['email'] = $userProfile['email'];
	            $userData['gender'] = $userProfile['gender'];
	            $userData['locale'] = $userProfile['locale'];
	            $userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
	            $userData['picture_url'] = $userProfile['picture']['data']['url'];
				
				$selector = 'email';
				$condition = array('email'=> $userData['email']);
				if(!$this->auth->read($condition,$selector)){
					$data = array('id'=>null,
								'oauth_provider'=>'facebook',
								'oauth_uid'=>$userData['oauth_uid'],
								'first_name'=>$userData['first_name'],
								'last_name'=>$userData['last_name'],
								'email'=>$userData['email'],
								'gender'=>$userData['gender'],
								'locale'=> $userData['locale'],
								'profile_url'=>$userData['profile_url'],
								'picture_url'=>$userData['picture_url']
								);
					$this->auth->create($data);
					 //insert data in users table data
	            	$email = $userData['email'];
	            	$selector = 'email';
					$condition = array('email'=>$email);
					if(!$this->users->read($condition,$selector)){
						$usersdata = array(
										'id'=>null,
										'username'=>$userData['oauth_uid'],
										'firstname'=>$userData['first_name'],
										'lastname'=>$userData['last_name'],
										'password'=>null,
										'email'=>$email,
										'picture'=>$userData['picture_url'],
										'header'=>'https://i.imgur.com/Np6wf8U.jpg',
							);
						$this->users->create($usersdata);
						$lastid = $this->users->c();
					    $data=array('id'=>null,'user_id'=>$lastid,'about'=>'','genre1'=>'','genre2'=>'','genre3'=>'','career'=>'');
					    $this->about->create($data);
					}//end of users table data insertion

					$cstrong = True;
				    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				    $data = array('id'=>null,'username'=>$userData['oauth_uid'],'token'=>sha1($token));
				    $this->upload->insert('oauth_token',$data);
					redirect('accounts/signup/'.$token.'');
				}
				else{
					$cstrong = True;
				    $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				    $selector = 'id';
				    $condition = array('email'=>$userData['email']);
				    $user_id = $this->users->read($condition,$selector)[0]['id'];
				    $data = array('id'=>null,'token'=>sha1($token),'user_id'=>$user_id);
				    $this->login_tokens->create($data);
				                  
				    setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
				    setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

				    $modified = date("Y-m-d H:i:s");
				    $data = array('modified'=>$modified);
				    $con = array('email'=>$userData['email']);
				    $this->users->update($data, $con);

				    $this->facebook->destroy_session();
					// Remove user data from session
					$this->session->unset_userdata('userData');
				            redirect('mimo');
				}
				
        	}
        	
        	//if new user cancelled
        	else{
        		redirect('/accounts');
        	}
		}//end of authentication
		
		else{
            $fbuser = '';
			
			// Get login URL
            $data['authUrl'] =  $this->facebook->login_url();
            $headerdata['title'] = "MimO | Login/Sign up";
			$this->load->view('include/header',$headerdata);
			$this->load->view('mimo_v/login',$data);
			$this->load->view('include/footer');
        }
        }
        else{
        	redirect('mimo');
        }
    }
	
    public function signup($token) {
    	if(!$this->login->isLoggedIn()){
    	$condition = array('token'=>sha1($token));
    	$authid = $this->upload->select('oauth_token',$condition,'username')[0]['username'];
    	if($this->upload->select('oauth_token',$condition)){
    		if(isset($_POST['next'])){
    			$username = $this->input->post("stagename");
	    		$data = array('username'=>$username);
	    		$id = $this->login->isLoggedIn();
	    		if(!$this->users->read($data,'username')){
		    		$condition = array('username'=>$authid);
		    		$this->users->update($data,$condition);
		    		
		    		$data = array('token'=>sha1($token));
		    		$this->upload->del('oauth_token',$data);

		    		$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
					$selector = 'id';
					$condition = array('username'=>$username);
					$user_id = $this->users->read($condition,$selector)[0]['id'];
					$data = array('id'=>null,'token'=>sha1($token),'user_id'=>$user_id);
					$this->login_tokens->create($data);
						                   
					setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
					setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
		    		redirect('mimo');
	    		}
	    		else{
	    			 echo "<script type='text/javascript'>alert('username already taken');</script>";
	    		}
    		}
    	}
    	else{
    		redirect('/accounts');
    	}
    	$this->facebook->destroy_session();
					// Remove user data from session
					$this->session->unset_userdata('userData');
    	$data['user'] = $authid;
		$headerdata['title'] = "MimO | Sign up";
			$this->load->view('include/header',$headerdata);
			$this->load->view('mimo_v/aftersignup',$data);
			$this->load->view('include/footer');
		}
		else{
			redirect('error');
		}
    }
    public function createaccount(){
    	if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$firstname = $this->input->post("firstname");
			$lastname = $this->input->post("lastname");
			$username = $this->input->post("username");
			$email = $this->input->post("email");
			$password = $this->input->post("password");
			$rpassword = $this->input->post("rpassword");
			
			if($firstname!=''&&$lastname!=''&&$username!=''&&$email!=''&&$password!=''&&$rpassword!=''){
				$selector = 'username';
				$condition = array('username'=>$username);
				if (!$this->users->read($condition,$selector)) {
					if (preg_match("/^[a-zA-Z0-9_]*$/", $username)) {
						if (strlen($password) >= 6 && strlen($password) <= 60) {
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$selector = 'email';
					        	$condition = array('email'=>$email);
					            if (!$this->users->read($condition,$selector)) {
					            	if($password==$rpassword){
					            		$data = array(
													'id'=>null,
													'username'=>$username,
													'firstname'=>$firstname,
													'lastname'=>$lastname,
													'password'=>password_hash($password, PASSWORD_BCRYPT),
													'email'=>$email,
													'picture'=>'https://i.imgur.com/LQq63AL.jpg',
													'header'=>'https://i.imgur.com/Np6wf8U.jpg',
										);
					                    $this->users->create($data);
					                    $lastid = $this->users->c();
									    $data=array('id'=>null,'user_id'=>$lastid,'about'=>'','genre1'=>'','genre2'=>'','genre3'=>'','career'=>'');
									    $this->about->create($data);
					                   // Send to email your account has been created
					                    // $this->mail->sendMail('Welcome to Mimo!', 'Your account has been created!', $email);
					                    $cstrong = True;
							            $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
							            $selector = 'id';
							            $condition = array('username'=>$username);
							            $user_id = $this->users->read($condition,$selector)[0]['id'];
							            $data = array('id'=>null,'token'=>sha1($token),'user_id'=>$user_id);
							            $this->login_tokens->create($data);
							                   
							            setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
							            setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
					                    $err = "success";
		    							echo json_encode(array('status'=>"success",'eventid'=>$err));
					            	}
					            	else{
					            		$err = "Passwords dont match!";
		    							echo json_encode(array('status'=>"error",'eventid'=>$err));
					            	}
					            }
					            else{
					            	$err = "The email you have entered is already in use.";
		    						echo json_encode(array('status'=>"error",'eventid'=>$err));
					            }
							}
							else{
								$err = "Invalid Email";
		    					echo json_encode(array('status'=>"error",'eventid'=>$err));
							}
						}
						else{
							$err = "Invalid password length. Password must be atleast 6-character long.";
		    				echo json_encode(array('status'=>"error",'eventid'=>$err));
						}
					}
					else{
						$err = "Invalid Username (Accepted characters are: Letters, numbers, and _)";
		    			echo json_encode(array('status'=>"error",'eventid'=>$err));
					}
				}
				else{
					$err = "Stagename already taken.";
		    		echo json_encode(array('status'=>"error",'eventid'=>$err));
				}
			}
			else{
				$err = "Please fill out all required fields.";
		    	echo json_encode(array('status'=>"error",'eventid'=>$err));
			}
		}
		else{
			redirect('/accounts');
		}
    }
    public function signin(){
    	if(!$this->login->isLoggedIn()){
    	$data['authUrl'] =  $this->facebook->login_url();
        $headerdata['title'] = "MimO | Login/Sign up";
		$this->load->view('include/header',$headerdata);
		$this->load->view('mimo_v/signin',$data);
		$this->load->view('include/footer');
		}
		else{
			redirect('mimo');
		}
    }
    public function si(){
    	if ($_SERVER['REQUEST_METHOD'] == "POST") {
	 		$username = $this->input->post("username");
			$password = $this->input->post("password");
			$selector = 'username';
	    	$condition = array('username'=>$username);
		    if($this->users->read($condition,$selector)){
		    	$selector = 'password';
			    $query = $this->users->read($condition,$selector);
			    if($query[0]['password']!=null){
				    if(password_verify($password,$query[0]['password'])){
				    	$cstrong = True;
			            $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			            $selector = 'id';
			            $user_id = $this->users->read($condition,$selector)[0]['id'];
			            $data = array('id'=>null,'token'=>sha1($token),'user_id'=>$user_id);
			            $this->login_tokens->create($data);
			                   
			            setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
			            setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

			            $modified = date("Y-m-d H:i:s");
					    $data = array('modified'=>$modified);
					    $con = array('username'=>$username);
					    $this->users->update($data, $con);
				    
			            $err = "Success";
		    			echo json_encode(array('status'=>"success",'eventid'=>$err));
			        }
			        else{
			        	$err = "Wrong Password";
		    			echo json_encode(array('status'=>"error",'eventid'=>$err));
		        	}
		        }
		        else{
		        	$err = "Wrong Password";
		    		echo json_encode(array('status'=>"error",'eventid'=>$err));
		        }
		        
			}
		    else{
		    	// echo 'mali';
		    	$err = "Username does not exist";
		    	echo json_encode(array('status'=>"error",'eventid'=>$err));
		    }
		}
		else{
			redirect('/accounts');
		}
    }

    public function changepass(){
    	if ($_SERVER['REQUEST_METHOD'] == "POST") {
    		$cbox = $this->input->post("cbox");
    		$pass = $this->input->post("pass");
    		$rpass = $this->input->post("rpass");
    		$token = $this->input->post("token");
    		$selector = 'user_id';
    		$condition = array('token'=>sha1($token));
    		if($this->password_tokens->read($condition,$selector)){
    			$userid = $this->password_tokens->read($condition,$selector)[0]['user_id'];
    			$tokenIsValid = True;
    			if($cbox=='true'){
    				$data = array('user_id'=>$userid);
    				$this->login_tokens->del($data);
    			}
    			if($pass==$rpass){
    				if (strlen($rpass) >= 6 && strlen($rpass) <= 60) {
    					$data = array('password'=>password_hash($rpass, PASSWORD_BCRYPT));
    					$condition = array('id'=>$userid);
    					$this->users->update($data,$condition);
    					$data = array('user_id'=>$userid);
    					$this->password_tokens->del($data);
    					echo json_encode(array('status'=>"success",'error'=>"None"));
    				}
    				else{
    					echo json_encode(array('status'=>"error",'error'=>"Invalid Password Length"));
    				}
    			}
    			else{
    				echo json_encode(array('status'=>"error",'error'=>"password not match"));
    			}
			}
			else{
				redirect('/accounts');
			}
		}
		else{
			redirect('/accounts');
		}
    }
    public function change_password(){
    	if(!$this->login->isLoggedIn()){
	    	if(isset($_GET['token'])){
	    		$token = $_GET['token'];
	    		$selector = 'user_id';
	    		$condition = array('token'=>sha1($token));
	    		if($this->password_tokens->read($condition,$selector)){
		    		$data['token'] = $_GET['token'];
		    		$headerdata['title'] = "MimO | Login/Sign up";
					$this->load->view('include/header',$headerdata);
					$this->load->view('mimo_v/changepass',$data);
					$this->load->view('include/footer');
				}
				else{
					redirect('/accounts');
				}
	    	}
	    	else{
				redirect('/accounts');
			}
		}
		else{
			redirect('error');
		}
    }
    public function forgot_password(){
    	if(!$this->login->isLoggedIn()){
	    	$headerdata['title'] = "MimO | Recover Account";
			$this->load->view('include/header',$headerdata);
			$this->load->view('mimo_v/forgot-password');
			$this->load->view('include/footer');
		}
		else{
			redirect('error');
		}
    }
    public function fg(){
    	if ($_SERVER['REQUEST_METHOD'] == "POST") {
    		$email = $this->input->post("email");
    		$con = array('email'=>$email);
    		if($this->users->read($con,'email')){
    			$cstrong = True;
	       		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				$selector = 'id';
				$condition = array('email'=>$email);
				$userid = $this->users->read($condition,$selector)[0]['id'];
				$data = array('id'=>null,'token'=>sha1($token),'user_id'=>$userid);
				$this->password_tokens->create($data);
				$this->mail->sendMail('Forgot Password!', "<a href='http://localhost/mimo/accounts/change_password?token=$token'>Click here to change your password!</a>", $email);
    			echo json_encode(array('status'=>"success"));
    		}
    		else{
    			echo json_encode(array('status'=>"error"));
    		}
    	}
    	else{
    		redirect('error');
    	}
    }
   
}