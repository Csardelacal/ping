<?php 

class MediaController extends AppController
{
	
	/**
	 * 
	 * @validate POST#type(string required)
	 * @validate POST#file(required)
	 */
	public function upload() {
		
		if ($_POST['file'] instanceof \spitfire\io\Upload) {
			#Store the file and process thumbs
			$local = $_POST['file']->store()->uri();
		}
		else {
			#Download the file first and process it aftwerwards
			#This prevents malicious sources from injecting potentially bad media into the user's computer
		}
		
		switch($_POST['type']) {
			case 'video':
				break;
			
			case 'image':
				break;
		}
		
	}
	
}