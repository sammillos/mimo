<body style="background-color: #d9d9d9">

	<!--This is below Top Navigation Section -->
<div class="container">
	<!-- Left Nav Section -->
<div class="row">
		<div id="leftnav" class="col-md-3 col-sm-5">
		
			<!-- Left Nav Profile Section -->
			<div class="row" style="margin-left: 10px; margin-right: 10px">
				<!-- Header -->
					<!-- Sa 'background-image: url('') mo ilagay yung mga header at profile pic -->
				<div class="header" style="background-image:url('<?php if ($users[0]['header'] == NULL){ echo 'http://localhost/mimo/assets/img/grey-background.png'; }
																else{ echo $users[0]['header'];} ?>'); width: 100%; height: 170px;
									margin-left: 0px; background-size: cover;">
					<!--DP-->
					<div class="nameBox" style="background: linear-gradient(transparent,rgba(0,0,0,0.2),rgba(0,0,0,0.7)); 
												background-size: cover; margin: 0 0; height: 162px">
						<div class="dpSection media" style="background-image:url('<?php if ($users[0]['picture'] == NULL){ echo 'http://localhost/mimo/assets/img/noimage.jpg'; }
																else{ echo $users[0]['picture'];} ?>');"></div>
							
						
						<!-- Full name yung sa h4 at username/stagename yung sa h6 -->
						<h4 style="color: white" class="text-center user"><?php echo $users[0]['firstname'].' '.$users[0]['lastname'];?></h4>
						<a href="http://localhost/mimo/mimo/myStudio?username=<?php echo $users[0]['username'];?> " style="color: white" class="text-center user"><h6><?php echo $users[0]['username'];?></h6></a>
						</div>
				</div>
			
			</div>
			
			<!-- End of Left Nav Profile Section -->

			<div class="row" style="margin-left: 10px; margin-right: 10px">
					<div id="listNav" class="col-md-12">
						<div class="btn-group btn-group-justified">
						<a  href="" class="listnav btn btn-group">
						<i class="material-icons">library_music</i>
						<p><small>Collection</small></p>
						</a>
			
						<a  href="" class="listnav btn btn-group">
						<i class="material-icons">album</i>
						<p><small>Audios</small></p>
						</a>
					
						<a  href="" class="listnav btn btn-group">
						<i class="material-icons">videocam</i>
						<p><small>Videos</small></p>
						</a>
		
					</div>
				</div>
			</div>
			
			
			<!--MP3 Player Section-->
		
			
			<!-- MP3 Player Section-->
			
			
			
			
			
		</div>
		<!--End of Left Nav Section -->
			<!--MP3 Player Section-->
			<!-- MP3 Player Section-->
		
		
	<!-- Content Section -->
	
	<!-- Post Body (Thoughts) -->
	<div class="col-md-6 belowtn col-xs-12 col-sm-7">
		<?php $this->load->view('templates/commentModal');?>
		<div class="postcont thoughts">
			
		<!--Start to place Here the Post/Thought Templates-->

		<!--End of Post Section -->	
		</div>
		<!-- End of Contents Divisions-->
	</div>
	
	
<!-- Third Column Div (Beside Contents)-->
	<div class="col-md-3">
	<br /> <br /> <br />
	
	<!--DONT MIND MUNA TONG AUDIO PLAYER-->
	<!--<div class="row hidden-sm hidden-xs" id="audplayer">
				<div class="col-md-12">
				<div class="wrapper">
					<div class="music music-cover">
						<img src="http://localhost/mimo/assets/img/bp.jpg" alt="" class="cover-img" />
					</div>
					
					<div class="music album-controls">
						<img src="http://localhost/mimo/assets/img/bp.jpg" alt="" class="album-img" />
							<div class="album-info">
								<span class="song-title">Track Title<span>
								<span class="album-artist">Singer</span>
								<span class="album-artist">Album: Into The Pink</span>
								<span class="song-utility">
									<i class="fa fa-home" aria-hidden="true"></i>
									<i class="fa fa-random" aria-hidden="true"></i>
									<i class="fa fa-retweet" aria-hidden="true"></i>
									<i class="fa fa-clock-o" aria-hidden="true"></i>
								</span>
							</div>
					</div>

					<div class="music music-controls">
						<span class="seek-bar">
							<span class="knot"></span>
						</span>
						<span class="song-buffer"></span>
						<span class="song-current-time">00:00</span><span class="play">
							<i class="fa fa-play" aria-hidden="false"></i>
							<span class="song-duration">00:00</span>
						</span>
					</div>
				
				</div>
			</div>
			</div>
	
			<audio controls id="music-player" preload="auto" >
				<source src="http://localhost/mimo/assets/audios/sample.mp3" type="audio/mpeg">
					Your browser does not support the audio element.
			</audio>
			
			
		
	<!-- Para sa POST AND AUDIO MODAL-->
	<?php $this->load->view('templates/addpostmodal');?>
	<?php $this->load->view('templates/addaudiomodal');?>
	<?php $this->load->view('templates/addvideomodal');?>
	
		<!--Para naman to dun sa fixed button ng release ng thoughts at audio-->
		<?php $this->load->view('include/releasebuttons');?>
			<?php $this->load->view('templates/commentModal');?>
	</div>
		
</div><!--End of the Whole Row (LeftNav, Contents, Third Column -->



<script type="text/javascript">
$(document).ready(function(){
	$('.postcont').html("")
	$.ajax({
		type: 'POST',
        url: '<?php echo base_url() ?>mimo/hallposts',
        data:{
        },
        success: function(s){
        	var posts = JSON.parse(s)
        	console.log(posts);
        	$.each(posts, function(index) {
        		if(posts[index].PostType==1){
        		$('.thoughts').html(
        						$('.thoughts').html()+'<div class="posttemp"><div class="posthead"><div class="media"><div class="media-left"><a href="#" ><div class="media-object postPic" style="background-image:url('+posts[index].PostUserPicture+');"></div></a></div><div class="media-body"><h4 class="media-heading"><a class="user" href="http://localhost/mimo/mimo/myStudio?username='+posts[index].PostUser+'">'+posts[index].PostUser+'</a><small> shared a thought!<br /><small>'+posts[index].PostDate+'</small></small></h4></div></div></div><div class="postbody"><div class="postbodycont">'+posts[index].PostBody+'</div></div><div id="likesection"><div class="btn-grp btn-group-justified"><a href="#" id="likeBtn" type="button" class="btn like" data-id="'+posts[index].PostId+'" aria-pressed="false" onclick="handleBtnClick(event)"><span class="fa fa-heart-o"></span> Like <small><small>('+posts[index].PostLikes+')</small></small></a><a class="commentBtn btn comment" data-did="'+posts[index].PostId+'" data-toggle="modal" data-target="#commentModal"><span class="fa fa-commenting-o"></span> Comment </a></div></div></div>'
        						);}
								$('[data-id]').click(function(e) {
									e.preventDefault();
									var buttonid = $(this).attr('data-id');
									$.ajax({
										type: 'POST',
										url: '<?php echo base_url() ?>mimo/likes',
										data:{
											postid:buttonid
										},
										success: function(s){
											var likes = JSON.parse(s);
											$("[data-id='"+buttonid+"']").html('<span class="fa fa-heart-o"></span> Like <small><small>('+likes.likes+')</small></small>');
										},
										error: function(e){
											console.log(e);
											alert('error');
										}
									});

								});

								$('[data-did]').click(function(e) {
									e.preventDefault();
									$('.commentatorDiv').html('')
									var buttonid = $(this).attr('data-did');
									$.ajax({
										type: 'POST',
										url: '<?php echo base_url() ?>mimo/getcomments',
										data:{
											postid:buttonid
										},
										success: function(s){
											var comments = JSON.parse(s)
        									console.log(comments);
        									$.each(comments, function(index) {
	        									$('.commentatorDiv').html(
					        						$('.commentatorDiv').html()+'<div class="media"><div class="media-left"><a href="http://localhost/mimo/mimo/myStudio?username='+comments[index].username+'"><div class="media-object commentPic" style="background-image:url('+comments[index].picture+');"></div></a></div><div class="media-body"><h5 class="media-heading"><a href="http://localhost/mimo/mimo/myStudio?username='+comments[index].username+'" style="font-weight:bold" class="user">'+comments[index].username+'</a><small><small> '+comments[index].posted_at+'</small></small></h5><h6>'+comments[index].comment+'</h6></div></div>'
					        						)
        									});
										},
										error: function(){
											console.log(e)
										}

									});
									$('.postComment').click(function() {
										e.preventDefault();
							            var txt = $("#commentBox").val();
							            $("#commentBox").val('');
							            $.ajax({
							                type:'POST',
							                url: '<?php echo base_url() ?>mimo/comment',
							                data:{
							                    comment:txt,
							                    postid:buttonid
							                },
							                success: function(r){
							                	if(r!=''){
							                    var comment = JSON.parse(r)
							                    console.log(comment)
							                    $( ".commentatorDiv" ).prepend( '<div class="media"><div class="media-left"><a href="http://localhost/mimo/mimo/myStudio?username='+comment[0].username+'"><div class="media-object commentPic" style="background-image:url('+comment[0].picture+');"></div></a></div><div class="media-body"><h5 class="media-heading"><a href="http://localhost/mimo/mimo/myStudio?username='+comment[0].username+'" style="font-weight:bold" class="user">'+comment[0].username+'</a><small><small> '+comment[0].posted_at+'</small></small></h5><h6>'+comment[0].comment+'</h6></div></div>' );}
							                },
							                error: function(xhr, ajaxOptions, thrownError){
							                    console.log(e);
							                }


							            });

							        });//end of postComment click()
								});

					

					
        	});
        },
        error: function(xhr, ajaxOptions, thrownError){
        	console.log(e);
        }
    });
});
</script>
<script type="text/javascript">
    if (window.location.hash && window.location.hash == '#_=_') {
        if (window.history && history.pushState) {
            window.history.pushState("", document.title, window.location.pathname);
        } else {
            // Prevent scrolling by storing the page's current scroll offset
            var scroll = {
                top: document.body.scrollTop,
                left: document.body.scrollLeft
            };
            window.location.hash = '';
            // Restore the scroll offset, should be flicker free
            document.body.scrollTop = scroll.top;
            document.body.scrollLeft = scroll.left;
        }
    }
</script>
</body>
