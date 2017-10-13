<?php
class Image {
        //pakigawang public function lang pag nilagay na sa library  wala ng static mahirap tawagin
        public function uploadImage($formname) {
               if ($formname['size'] > 10240000) {
                        die('Image too big, must be 10MB or less!');
                } 
				else
				{
				$image = base64_encode(file_get_contents($formname['tmp_name']));

                $options = array('http'=>array(
                        'method'=>"POST",
                        'header'=>"Authorization: Bearer fbec2d0f2c259944907341aeedb0a387f85fae12\n".
                        "Content-Type: application/x-www-form-urlencoded",
                        'content'=>$image
                ));

                $context = stream_context_create($options);

                $imgurURL = "https://api.imgur.com/3/image";

                

                $response = file_get_contents($imgurURL, false, $context);
                $response = json_decode($response);	
				
				$link=$response->data->link;
				return $link;
				}
        }

}
?>