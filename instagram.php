<?php
	/*
		This class does most of the interacting with instagram
	*/
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	class instagram {
		private $accessToken = '', $endPointUrl = '', $instagramData = array(), $settings;

		public function __construct() {
			$this->loadSettings();
			$this->accessToken = $this->settings->access_token;
			$this->userId = $this->settings->user_id;
		}

		public function getFullFeed() {
			// Make endPoint
			$this->endPointUrl = 'https://api.instagram.com/v1/users/' . $this->userId . '/media/recent/?access_token=' . $this->accessToken . '&count=100';
			
			$curl = curl_init($this->endPointUrl); 
			curl_setopt($curl, CURLOPT_POST, false); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
			
			$response = json_decode($response);


			$this->instagramData = array_merge($this->instagramData, $response->data);

			// Check if there's more. If so, fetch it!
			while (isset($response->pagination->next_url)) {
				$pagedEndPoint = $response->pagination->next_url;
				unset($response);

				$curl = curl_init($pagedEndPoint); 
				curl_setopt($curl, CURLOPT_POST, false); 
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($curl);
				curl_close($curl);

				$response = json_decode($response);

				foreach ($response->data as $post) {
					array_push($this->instagramData, $post);	
				}
				
			}

			$this->instagramData = array_reverse($this->instagramData);

			return $this->instagramData;

		}

		/* Load the settings file */
		private function loadSettings() {
			$fh = fopen('instagram_settings.json', 'r');

			$this->settings = json_decode(fread($fh, filesize('instagram_settings.json')));
			return true;
		}

		/* Save image */
		public function saveImage($url, $save_to) {
			$ch = curl_init($url);
			$fp = fopen($save_to, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
		}
	}
?>
