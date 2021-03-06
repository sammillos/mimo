<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mimo extends CI_Controller {

	public function __construct(){
		parent::__construct();
	
		$this->load->model('users','users');
		$this->load->model('login_tokens','login_tokens');
		$this->load->model('posts','posts');
		$this->load->model('thoughts','thoughts');
		$this->load->model('followers','followers');
		$this->load->model('post_likes','post_likes');
		$this->load->model('getposts');

		$this->load->model('comments');
		$this->load->model('upload');
		$this->load->model('notif');
		$this->load->model('password_tokens');

		$this->load->model('genre','genre');
		$this->load->model('about','about');

		$this->load->library('notify');
		$this->load->library('login');
		$this->load->library('mail');
		$this->load->library('topics');
		$this->load->library('facebook');
		$this->load->library('image');

	}//end of __contruct

	public function index()
	{
		if($this->login->isLoggedIn()){
			$id = $this->login->isLoggedIn();
			$condition = array('id'=>$id);
			$data['users'] = $this->users->read($condition);
			$headerdata['title'] = "MimO | Music Hall";
			$this->load->view('include/header',$headerdata);
			$this->load->view('include/topnav', $data);
			$this->load->view('mimo_v/musichall');
			$this->load->view('include/footer');
		}
		else{
			redirect('home');
		}
		// echo $this->login->isLoggedIn();

	}//end of index
	
	public function settings()
	{
		//check if user is logged in
		if($this->login->isLoggedIn()){
			if(isset($_POST['account'])){
			$id = $this->login->isLoggedIn();
			$selector= 'username';
			$condition = array('id'=>$id);
			$previoususername= $this->users->read($condition,$selector)[0]['username'];
			$selector= 'lastname';
			$previouslastname= $this->users->read($condition,$selector)[0]['lastname'];
			$selector= 'firstname';
			$previousfirstname= $this->users->read($condition,$selector)[0]['firstname'];
			$selector= 'picture';
			$previousprofile= $this->users->read($condition,$selector)[0]['picture'];
			$selector= 'header';
			$previousheader= $this->users->read($condition,$selector)[0]['header'];
			
			$username = $this->input->post("username", TRUE);
			$lastname = $this->input->post("lastname", TRUE);
			$firstname= $this->input->post("firstname", TRUE);
			
			$profileimage= $_FILES['imgProfile'];
			$headerimage= $_FILES['imgHeader'];
			if($profileimage['name']=='') {
					//echo "<h2>An Image Please.</h2>";
					$profilelink=$previousprofile;
			}
			else{
			//print_r ($image);
			$profilelink=$this->image->uploadImage($profileimage); 
				if($profilelink==NULL)
				{
					$profilelink=$previousprofile;
					echo "<script type='text/javascript'>alert('Connection Error');</script>";
				}
			}
			
			if($headerimage['name']=='') {  
					//echo "<h2>An Image Please.</h2>";
					$headerlink=$previousheader;
			}
			else{
			//print_r ($image);
			$headerlink=$this->image->uploadImage($headerimage); 
			}
			
			if ($username==NULL){
				$username=$previoususername;
			}
			if ($lastname==NULL){
				$lastname=$previouslastname;
			}
			if ($firstname==NULL){
				$firstname=$previousfirstname;
			}
			
			$data = array(
					'username'=>$username,
					'lastname'=>$lastname,
					'firstname'=>$firstname,
					'picture'=>$profilelink,
					'header'=>$headerlink
					);
			$this->users->update($data,$condition);
			
			}
			if(isset($_POST['mymusic'])){
			$id = $this->login->isLoggedIn();
			$selector= 'career';
			$condition = array('user_id'=>$id);
			$previouscareer= $this->about->read($condition,$selector)[0]['career'];
			$genre1 = $this->input->post("genre1", TRUE);
			$genre2 = $this->input->post("genre2", TRUE);
			$genre3 = $this->input->post("genre3", TRUE);
			$mcareer =$this->input->post("mcareer", TRUE);
			$career="";
			if($mcareer==""){
				$career=$previouscareer;
			}
			else{
				foreach($mcareer as $car)
				{
					$career .= $car. ",";
						
				}
			}
			if($genre1=="None")
				{ $genre1=NULL;
				}
			if($genre2=="None")
				{ $genre2=NULL;
				}
			if($genre3=="None")
				{ $genre3=NULL;
				}
			
			$data = array(
					'genre1'=>$genre1,
					'genre2'=>$genre2,
					'genre3'=>$genre3,
					'career'=>$career
					);
			
			$this->about->update($data,$condition);
			}
			$id = $this->login->isLoggedIn();
			$condition = array('id'=>$id);
			$data['users'] = $this->users->read($condition);
			$condition = array('user_id'=>$id);
			$data['about'] = $this->about->read($condition);
			$data['genre'] = $this->genre->read();
			$headerdata['title'] = "MimO | Settings";
			$this->load->view('include/header',$headerdata);
			$this->load->view('include/topnav', $data);
			$this->load->view('mimo_v/settings', $data);
			$this->load->view('include/footer');
		}
		else{
			redirect('home');
		}

	}//end of settings
	
	public function artist()
	{
		if($this->login->isLoggedIn()){
			$id = $this->login->isLoggedIn();
			$condition = array('id'=>$id);
			$data['users'] = $this->users->read($condition);
			$headerdata['title'] = "MimO | Artist";
			$this->load->view('include/header',$headerdata);
			$this->load->view('include/topnav', $data);
			$this->load->view('mimo_v/artist');
			$this->load->view('include/footer');
		}
		else{
			redirect('home');
		}
	}//end of artist

	public function genre()
	{
		if($this->login->isLoggedIn()){
			$id = $this->login->isLoggedIn();
			$condition = array('id'=>$id);
			$data['users'] = $this->users->read($condition);
			$headerdata['title'] = "MimO | Genres";
			$this->load->view('include/header',$headerdata);
			$this->load->view('include/topnav', $data);
			$this->load->view('mimo_v/genre');
			$this->load->view('include/footer');
		}
		else{
			redirect('home');
		}
	}//end of genre
	
	public function myStudio()
	{
		if($this->login->isLoggedIn()){
			if(isset($_GET['username'])){
				$username = $_GET['username'];
				//check if user exists
				$condition = array('username'=>$username);
				if($this->users->read($condition)){
					$user = $this->users->read($condition);
					$data['user'] = $user;
					$user_id=$user[0]['id'];
					$condition = array('user_id'=>$user_id);
					$data['about'] = $this->about->read($condition);
					
					//get collection lists
					$collectionList = $this->getposts->getcollections($user_id);
					$data['collectionList'] =$collectionList;
					
				}
				else{
					redirect('mimo');
				}

			}
			else{
				redirect('mimo');
			}
			$id = $this->login->isLoggedIn();
			$condition = array('id'=>$id);
			$data['users'] = $this->users->read($condition);
			
			$headerdata['title'] = "MimO | My Studio";
			$this->load->view('include/header',$headerdata);
			$this->load->view('include/topnav', $data);
			$this->load->view('mimo_v/mystudio');
			$this->load->view('include/footer');
		}
		else{
			redirect('home');
		}
	
	}//end of myStudio
	public function deletecollection(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$colid = $this->input->post("collectionid");
			$data = array('id'=>$colid);
			$this->upload->del('collections',$data);
			echo 'deleted';
		}
		else{
          	redirect('error');
        }
	}
	public function deletecollectionsong(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$colid = $this->input->post("collectionid");
			$data = array('id'=>$colid);
			$this->upload->del('collection_songs',$data);
			echo 'deleted';
		}
		else{
          	redirect('error');
        }
	}
	public function posts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$browseUserid = $this->input->post("browseuser");
			if($this->getposts->readthoughts($browseUserid)){
				$posts = $this->getposts->readthoughts($browseUserid);
				$result = array();
	                foreach($posts as $post) {

	                      $p=array('PostId'=>$post['id'],
	                      			'PostUserPicture'=>$post['picture'],
	                      			'PostBody'=>$this->topics->link_add($post['body']),
	                      			'PostUser'=>$post['username'],
	                      			'PostLikes'=>$post['likes'],
	                      			'PostComments'=>$post['comments'],
	                      			'PostDate'=>$post['posted_at']
	                      	);
	                      array_push($result,$p);
	                }
	              echo json_encode($result);
          }
          else{
          	echo json_encode(array('PostId'=>"error"));
          }
		}
		else{
			redirect('error');
		}
	}//end of posts function
	public function audioposts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$browseUserid = $this->input->post("browseuser");
			if($this->getposts->readaudios($browseUserid)){
				$posts = $this->getposts->readaudios($browseUserid);
	            $res = array();
	                foreach($posts as $results) {
	                	$p=array(
	                			'id'=>$results['id'],
	                			'username'=>$results['username'],
	                			'picture'=>$results['picture'],
	                			'about'=>$this->topics->link_add($results['about']),
	                			'posted_at'=>$results['posted_at'],
	                			'likes'=>$results['likes'],
	                			'comments'=>$results['comments'],
	                			'cover'=>$results['cover'],
	                			'title'=>$results['title'],
	                			'path'=>$results['path'],
	                			'genre'=>$results['genre'],
	                			'views'=>$results['views']
	                		);
	                	array_push($res,$p);

	                }
			echo json_encode($res);
            }else{
        		echo json_encode(array('id'=>"error"));
        	}
		}
		else{
			redirect('error');
		}
	}//end of audioposts function
	public function videoposts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$browseUserid = $this->input->post("browseuser");
			if($this->getposts->readvideos($browseUserid)){
				$posts = $this->getposts->readvideos($browseUserid);
				$res = array();
	                foreach($posts as $results) {
	                	$p=array(
	                			'id'=>$results['id'],
	                			'username'=>$results['username'],
	                			'picture'=>$results['picture'],
	                			'description'=>$this->topics->link_add($results['description']),
	                			'posted_at'=>$results['posted_at'],
	                			'likes'=>$results['likes'],
	                			'comments'=>$results['comments'],
	                			'name'=>$results['name'],
	                			'url'=>$results['url'],
	                			'plays'=>$results['plays']
	                		);
	                	array_push($res,$p);

	                }
			echo json_encode($res);
        	}
        	else{
        		echo json_encode(array('id'=>"error"));
        	}
		}
		else{
			redirect('error');
		}
	}//end of videoposts function
	public function likes(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$postid = $this->input->post("postid");
			$likerid = $this->login->isLoggedIn();
			$selector = 'likes';
			$condition = array('id'=>$postid);
			$numlikes = $this->posts->read($condition,$selector)[0]['likes'];
			$condition = array('post_id'=>$postid,'user_id'=>$likerid);
			if(!$this->post_likes->read($condition)){
				$data = array('likes'=>$numlikes+1);
				$condition = array('id'=>$postid);
				$this->posts->update($data,$condition);
				$data = array('id'=>null,'post_id'=>$postid,'user_id'=>$likerid);
				$this->post_likes->create($data);
				$this->notify->createNotify('',$postid,'2');

				$selector = 'likes';
				$condition = array('id'=>$postid);
				$likes = $this->posts->read($condition,$selector)[0]['likes'];
				echo json_encode(array('likes'=>$likes,'stats'=>'like'));
			}
			else{
				$data = array('likes'=>$numlikes-1);
				$condition = array('id'=>$postid);
				$this->posts->update($data,$condition);
				$data = array('post_id'=>$postid,'user_id'=>$likerid);
				$this->post_likes->del($data);

				$selector = 'likes';
				$condition = array('id'=>$postid);
				$likes = $this->posts->read($condition,$selector)[0]['likes'];
				echo json_encode(array('likes'=>$likes,'stats'=>'unlike'));
			}
			
		}
		else{
			redirect('error');
		}
	}//end of likes function
	public function checklikes(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$postid = $this->input->post("postid");
			$userid = $this->input->post("userid");

			$con = array('post_id'=>$postid,'user_id'=>$userid);
			if($this->upload->select('post_likes',$con)){
				echo json_encode(array('stat'=>'like'));
			}
			else{
				echo json_encode(array('stat'=>'notlike'));
			}
		}	
		else{
			redirect('error');
		}
	}

	public function delete(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$postid = $this->input->post("postid");
			$selector = 'type';
			$condition = array('id'=>$postid);
			$type = $this->posts->read($condition,$selector)[0]['type'];
			$this->posts->del($condition);
			if($type==1){
				$data = array('post_id'=>$postid);
				$this->thoughts->del($data,'thoughts');
				echo $type;
			}
			else if($type==2){
				$data = array('post_id'=>$postid);
				$this->thoughts->del($data,'audios');
				echo $type;
			}
			else if($type==3){
				$data = array('post_id'=>$postid);
				$this->thoughts->del($data,'videos');
				echo $type;
			}
			
		}
		else{
			redirect('error');
		}
	}
	public function addnewcol(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$postid = $this->input->post("colid");
			$collectionList = $this->input->post("option");
			$newCollection = $this->input->post("newcol");
			$userid = $this->input->post("userid");
			if($newCollection!=''||$collectionList!=''){
				if($newCollection!=''){
						$data = array(
										'id'=>null,
										'user_id'=>$userid,
										'name'=>$newCollection,
										'count'=>13
							);
						$this->upload->insert('collections',$data);
						$lastCollectionId = $this->upload->c();
						$data = array(
										'id'=>null,
										'collection_id'=>$lastCollectionId,
										'post_id'=>$postid
							);
						$this->upload->insert('collection_songs',$data);
						echo json_encode(array('status'=>"Audio successfully added to the new collection!"));

				}
				else{
					$condition = array('collection_id'=>$collectionList,'post_id'=>$postid);
					if(!$this->upload->select('collection_songs',$condition)){
						$data = array(
										'id'=>null,
										'collection_id'=>$collectionList,
										'post_id'=>$postid
							);
						$this->upload->insert('collection_songs',$data);

						$condition = array('id'=>$collectionList);
						$count = $this->upload->select('collections',$condition,'count')[0]['count'];
						$data = array('count'=>$count+1);
						$this->upload->update('collections',$data,$condition);
						echo json_encode(array('status'=>"Added Successfully"));
					}
					else{
						echo json_encode(array('status'=>"This audio already exists in this collection list!"));
					}
				}
			}
			else{
				echo json_encode(array('status'=>""));
			}
		}
		else{
			redirect('error');
		}
	}
	public function getcollectionlist(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$userid = $this->input->post("userid");
			$condition = array('user_id'=>$userid);
			$list = $this->upload->select('collections',$condition);
			echo json_encode($list);
		}
		else{
			redirect('error');
		}
	}
	public function hallposts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$start = $this->input->post("start");
			$userid = $this->login->isLoggedIn();
			if($this->getposts->allposts($userid)){
				$posts = $this->getposts->allposts($userid);
				$data = array();
				$result = array();
	                foreach($posts as $post) {
	                	$phpdate = strtotime( $post['posted_at'] );
	                      $p=array('PostType'=>$post['type'],
	                      			'PostId'=>$post['id'],
	                      			'PostUserPicture'=>$post['picture'],
	                      			'PostUser'=>$post['username'],
	                      			'PostLikes'=>$post['likes'],
	                      			'PostComments'=>$post['comments'],
	                      			'PostDate'=>date( 'M d Y h:i a', $phpdate ),
	                      			'thoughtBody'=>$this->topics->link_add($post['body']),
	                      			'audioAbout'=>$this->topics->link_add($post['about']),
	                      			'videoAbout'=>$this->topics->link_add($post['description']),
	                      			'audioPath'=>$post['path'],
	                      			'videoPath'=>$post['url'],
	                      			'audioTitle'=>$post['title'],
	                      			'videoTitle'=>$post['name'],
	                      			'audioGenre'=>$post['genre'],
	                      			'audioCover'=>$post['cover'],
	                      			'audioviews'=>$post['views'],
	                      			'videoviews'=>$post['plays'],
	                      	);
	                      array_push($result,$p);
	                }
	                for ($i = $start; $i < $start+5; $i++) {
				        if ($i < count($result)) {
				                array_push($data, $result[$i]);
				        }
					}
	              echo json_encode($data);
        }
        else{
          	echo json_encode(array('PostId'=>"error"));
        }
	}
	else{
		redirect('error');
	}
	}//end of hallposts

	public function thoughts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$thoughts = $this->input->post("thoughts");
			$id = $this->login->isLoggedIn();
			if($thoughts!=''){
				$data = array(
						'id'=>null,
						'user_id'=>$id,
						'likes'=>0,
						'comments'=>0,
						'type'=>1
						);
				$this->posts->create($data);
				$topics = $this->topics->getTopics($thoughts);
				$post_id = $this->posts->c();
				if($this->notify->createNotify($thoughts,0,'1')){
					foreach ($this->notify->createNotify($thoughts,0,'1') as $key => $n) {
	                    $s = $id;
	                    $condition = array('username'=>$key);
	                    if($this->users->read($condition,'id')){
		                    $r = $this->users->read($condition,'id')[0]['id'];
		                    if($r!=$s){
			                    if ($r != 0) {
			                    	$data = array('id'=>null,
			                    				  'type'=>$n["type"],
			                    				  'receiver'=>$r,
			                    				  'sender'=>$s,
			                    				  'post_id'=>$post_id,
			                    				  'notifurl'=>'http://localhost/mimo/notification?pid='.$post_id.''

			                    				  );
			                    	$this->notif->create($data);
			                    }
		                	}
		                }
	                }
            	}
				
				$data = array(
						'id'=>null,
						'post_id'=>$post_id,
						'body'=>$thoughts,
						'topics'=>$topics
						);
				$this->thoughts->create($data);
				$query = $this->getposts->newthoughts($post_id);
				echo json_encode($query);

			}
			else{
				echo 'error';
			}
		}
		else{
			redirect('error');
		}
	}//end of thoughts

	public function audios(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$title = $_POST['title'];
			$desc = $_POST['audDescInput'];
			$genre = $_POST['genre'];
			if($title==''){
				$title = 'Untitled';
			}
			$type = explode('.', $_FILES["file"]["name"]);
			$type = strtolower($type[count($type)-1]);
			$noover = uniqid(rand()).'.'.$type;
			$url = "C:\wamp64\www\mimo\assets\uploads\audios/".$noover;
		    move_uploaded_file($_FILES['file']['tmp_name'], $url);
		    $path = "http://localhost/mimo/assets/uploads/audios/".$noover;
			$image= $_FILES['uploadAudioImg'];
			if($image['name']=='') {
					$audioart= "https://i.imgur.com/rtZYgdC.jpg";
			}
			else{
				$audioart=$this->image->uploadImage($image); 
				}
				
		    $id = $this->login->isLoggedIn();
				$data = array(
						'id'=>null,
						'user_id'=>$id,
						'likes'=>0,
						'comments'=>0,
						'type'=>2
						);
				$this->posts->create($data);
				$topics = $this->topics->getTopics($desc);
				$post_id = $this->posts->c();
				if($this->notify->createNotify($desc,0,'1')){
					foreach ($this->notify->createNotify($desc,0,'1') as $key => $n) {
	                    $s = $id;
	                    $condition = array('username'=>$key);
	                    if($this->users->read($condition,'id')){
		                    $r = $this->users->read($condition,'id')[0]['id'];
		                    if($r!=$s){
			                    if ($r != 0) {
			                    	$data = array('id'=>null,
			                    				  'type'=>$n["type"],
			                    				  'receiver'=>$r,
			                    				  'sender'=>$s,
			                    				  'post_id'=>$post_id,
			                    				  'notifurl'=>'http://localhost/mimo/notification?pid='.$post_id.''

			                    				  );
			                    	$this->notif->create($data);
			                    }
		                	}
		                }
	                }
            	}
				$data = array(
							'id'=>null,
							'post_id'=>$post_id,
							'title'=>strip_tags($title),
							'genre'=>$genre,
							'about'=>$desc,
							'path'=>$path,
							'cover'=>$audioart,
							'topics'=>$topics

					);
				$this->upload->insert('audios',$data);
				$query = $this->getposts->newaudios($post_id);
				echo json_encode($query);
		    
		}
		else{
			redirect('error');
		}
	}//end of audios()
	public function videos(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$title = $_POST['title'];
			$desc = $_POST['vidDescInput'];
			if($title==''){
				$title = 'Untitled';
			}
			$types = explode('.', $_FILES["vidUpload"]["name"]);
			$types = strtolower($types[count($types)-1]);
			$noover = uniqid(rand()).'.'.$types;
			$url = "C:\wamp64\www\mimo\assets\uploads/videos/".$noover;
		    move_uploaded_file($_FILES['vidUpload']['tmp_name'], $url);
		    $path = "http://localhost/mimo/assets/uploads/videos/".$noover;

		    $id = $this->login->isLoggedIn();
				$data = array(
						'id'=>null,
						'user_id'=>$id,
						'likes'=>0,
						'comments'=>0,
						'type'=>3
						);
				$this->posts->create($data);
				$topics = $this->topics->getTopics($desc);
				$post_id = $this->posts->c();
				if($this->notify->createNotify($desc,0,'1')){
					foreach ($this->notify->createNotify($desc,0,'1') as $key => $n) {
	                    $s = $id;
	                    $condition = array('username'=>$key);
	                    if($this->users->read($condition,'id')){
		                    $r = $this->users->read($condition,'id')[0]['id'];
		                    if($r!=$s){
			                    if ($r != 0) {
			                    	$data = array('id'=>null,
			                    				  'type'=>$n["type"],
			                    				  'receiver'=>$r,
			                    				  'sender'=>$s,
			                    				  'post_id'=>$post_id,
			                    				  'notifurl'=>'http://localhost/mimo/notification?pid='.$post_id.''

			                    				  );
			                    	$this->notif->create($data);
			                    }
		                	}
		                }
	                }
            	}
				$data = array(
							'id'=>null,
							'post_id'=>$post_id,
							'name'=>strip_tags($title),
							'description'=>$desc,
							'url'=>$path,
							'topics'=>$topics

					);
				$this->upload->insert('videos',$data);
		    	$query = $this->getposts->newvideos($post_id);
				echo json_encode($query);
		}
		else{
			redirect('error');
		}
	}//end of videos()

	public function comment(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$comment = $this->input->post("comment");
			$postid = $this->input->post("postid");
			$comment = strip_tags($comment);
			$id = $this->login->isLoggedIn();
			if($comment!=''){
				//get current comment number
				$selector = 'comments';
				$condition = array('id'=>$postid);
				$numcom = $this->posts->read($condition,$selector)[0]['comments'];
				
				//update number of comments in posts table
				$data = array('comments'=>$numcom+1);
				$condition = array('id'=>$postid);
				$this->posts->update($data,$condition);

				//insert comment data in comment table
				$data = array(
						'id'=>null,
						'post_id'=>$postid,
						'user_id'=>$id,
						'comment'=>$comment
						);
				$this->comments->create($data);
				$id = $this->comments->c();
				$datas = $this->comments->aftercom($id);
				$datas[0]['comment'] = $this->topics->link_add($datas[0]['comment']);
				$phpdate = strtotime( $datas[0]['posted_at'] );
				$datas[0]['posted_at'] = date( 'M d Y h:i a', $phpdate );
				echo json_encode($datas);

				//create notification comment
				$id = $this->login->isLoggedIn();
				$this->notify->createNotify('',$postid,'3');
				if($this->notify->createNotify($comment,0,'1')){
					foreach ($this->notify->createNotify($comment,0,'4') as $key => $n) {
	                    $s = $id;
	                    $condition = array('username'=>$key);
	                    if($this->users->read($condition,'id')){
		                    $r = $this->users->read($condition,'id')[0]['id'];
		                    if($r!=$s){
			                    if ($r != 0) {
			                    	$data = array('id'=>null,
			                    				  'type'=>$n["type"],
			                    				  'receiver'=>$r,
			                    				  'sender'=>$s,
			                    				  'post_id'=>$postid,
			                    				  'notifurl'=>'http://localhost/mimo/notification?pid='.$postid.''

			                    				  );
			                    	$this->notif->create($data);
			                    }
		                	}
		                }
	                }
            	}
			}
			else{
				
			}
			
		}
		else{
			redirect('error');
		}
	}//end of comment()
	public function getcomments(){
		$postid = $this->input->post("postid");
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$query = $this->comments->getcom($postid);
			$result = array();
                foreach($query as $post) {
                	$phpdate = strtotime( $post['posted_at'] );
                      $p=array('comment'=>$this->topics->link_add($post['comment']),
                      			'username'=>$post['username'],
                      			'picture'=>$post['picture'],
                      			'posted_at'=>date( 'M d Y h:i a', $phpdate )
                      	);
                      array_push($result,$p);
                }
              echo json_encode($result);
		}
		else{
			redirect('error');
		}
	}//end of getcomments()
	public function checkfollow(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$userid = $this->input->post("userid");
			$followerid = $this->input->post("followerid");
			if($this->followers->read($userid,$followerid)){
				$isFollowing = true;
				echo json_encode(array('status'=>$isFollowing));
			}
			else{
				$isFollowing = false;
				echo json_encode(array('status'=>$isFollowing));
			}
		}
		else{
			redirect('error');
		}
	}//end of checkfollow()
	public function follow(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$userid = $this->input->post("userid");
			$followerid = $this->input->post("followerid");
			//get current number of followers in about table
				$selector = 'followers';
				$condition = array('user_id'=>$userid);
				$cf = $this->about->read($condition,$selector)[0]['followers'];

			if(!$this->followers->read($userid,$followerid)){
				$data = array('id'=>null, 'user_id'=>$userid,'follower_id'=>$followerid);
				$this->followers->create($data);
				$this->notify->createNotify('','','5',$userid);
				
				//update number of followers
				$data = array('followers'=>$cf+1);
				$this->about->update($data,$condition);
				$cf=$cf+1;
			}
			else{
				$data = array('user_id'=>$userid,'follower_id'=>$followerid);
				$this->followers->del($data);
				$this->notify->createNotify('','','6',$userid);

				//update number of followers
				$data = array('followers'=>$cf-1);
				$this->about->update($data,$condition);
				$cf=$cf-1;
			}
			echo json_encode(array('followers'=>$cf));
		}
		else{
			redirect('error');
		}
	}//end of follow
	public function changepass(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$email = $this->input->post("email");
			$cstrong = True;
       		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			$selector = 'id';
			$condition = array('email'=>$email);
			$userid = $this->users->read($condition,$selector)[0]['id'];
			$data = array('id'=>null,'token'=>sha1($token),'user_id'=>$userid);
			$this->password_tokens->create($data);
			$this->mail->sendMail('Forgot Password!', "<a href='http://localhost/mimo/accounts/change_password?token=$token'>Click here to change your password!</a>", $email);
		}
		else{
			redirect('error');
		}
	}

	public function logout() {
		
		if (isset($_COOKIE['SNID'])) {
			    	$data = array('token'=>sha1($_COOKIE['SNID']));
			        $this->login_tokens->del($data);
			    }
			    setcookie("SNID", '', time() - 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
			    setcookie("SNID_", '1', time() - 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
        redirect('home');
    }//end of logout
	
	
	public function collectionlist()
	{
		if($this->login->isLoggedIn()){
			$colid = $_GET['name'];
			if(isset($_GET['name'])){
				$con = array('id'=>$colid);
				if($this->upload->select('collections',$con)){
				$name = $this->upload->select('collections',$con)[0]['name'];
				$cdata['name'] = $name;
				$user_id = $this->upload->select('collections',$con,'user_id')[0]['user_id'];
				$cdata['colid'] = $colid;
				$cdata['user_id'] = $user_id;
				$cdata['id']=$colid;



				$con = array('user_id'=>$user_id);
				$colist = $this->upload->select('collections',$con);
				$cdata['colist'] = $colist;

				$id = $this->login->isLoggedIn();
				$condition = array('id'=>$id);
				$data['users'] = $this->users->read($condition);
				
				$headerdata['title'] = "MimO | My Studio";
				$this->load->view('include/header',$headerdata);
				$this->load->view('include/topnav', $data);
				$this->load->view('mimo_v/collectionlist',$cdata);
				$this->load->view('include/footer');
				}
				else{
					redirect('mimo');
				}
			}
			else{
				redirect('mimo');
			}
		}
		else{
			redirect('home');
		}
	}
	public function audioview(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$audioid = $this->input->post("audioid");
			$condition = array('post_id'=>$audioid);
			$currentnumview = $this->upload->select('audios',$condition,'views')[0]['views'];
			$updatednumview = $currentnumview+1;
			$data = array('views'=>$updatednumview);
			$condition = array('post_id'=>$audioid);
			$this->upload->update('audios',$data,$condition);
			echo json_encode(array('views'=>$updatednumview));
		}
		else{
			redirect('error');
		}
	}
	public function videoview(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$videoid = $this->input->post("videoid");
			$condition = array('post_id'=>$videoid);
			$currentnumview = $this->upload->select('videos',$condition,'plays')[0]['plays'];
			$updatednumview = $currentnumview+1;
			$data = array('plays'=>$updatednumview);
			$condition = array('post_id'=>$videoid);
			$this->upload->update('videos',$data,$condition);
			echo json_encode(array('views'=>$updatednumview));
		}
		else{
			redirect('error');
		}
	}
	public function reportpost(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$reportid = $this->input->post("reportid");
			$userid = $this->input->post("userid");
			$con = array('post_id'=>$reportid,'user_id'=>$userid);
			if(!$this->upload->select('report',$con)){

				$con = array('id'=>$reportid);
				$reportnum = $this->upload->select('posts',$con,'reports')[0]['reports'];
				$data = array('id'=>null,'post_id'=>$reportid,'user_id'=>$userid);
				$this->upload->insert('report',$data);
				$data = array('reports'=>$reportnum+1);
				$con = array('id'=>$reportid);
				$this->upload->update('posts',$data,$con);
				echo json_encode(array('status'=>'The post is now held for review.'));
			}
			else{
				echo json_encode(array('status'=>'You have already reported this post.'));
			}
		}
		else{
			redirect('error');
		}
	}
	public function reviewposts(){
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$posts = $this->upload->select('posts');
			foreach ($posts as $p) {
				if($p['reports']>=20){
					$data = array('id'=>$p['id']);
					$this->upload->del('posts',$data);
					echo 'success';
				}
			}
		}
		else{
			redirect('error');
		}
	}
}

