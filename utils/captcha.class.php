<?php
	/*
		File: captcha.class.php
		Author: Jamie McConnell
		Email: jamie[at]blue44.com
		URL: http://nodstrum.com/2007/09/23/captcha/
		Date: 22/09/2007
		License: GPL
		
		If you do use this script, please reference me: http://nodstrum.com
		
		Usage Instructions:
			1.	On your form page add the following lines:
				require_once('captcha.class.php');
				$captcha = new Captcha;
				$captchaImage = $captcha->create();
			
			2.	In your form, where you want the Captcha displayed add this:
				<?= $captchaImage; ?>
			
			3.	On the processing page add this:
				require_once('captcha.class.php');
				$captcha = new Captcha;
				$verified = $captcha->verify($_POST[$captcha->captchaInputName]);
				
			4.	In your processing check for $verified being true/false.
				if($verified) {
					// Captcha was correct
				} else {
					// Captcha was incorrect
				}
				
			5.	Thats it! :)
				Enjoy.
	*/
	class Captcha {
		/* Variables */
		var $imageDirectory = '/u/savtec/public_html/rso.cl/tmp/captcha/'; // No forward slash - must be writable, chmod 777
		var $imageURL = '/tmp/captcha'; // No forward slash.
		var $captchaLabel = 'Favor completar el captcha:'; // This is what is shown just above the captcha image.
		var $captchaBoxStyle = 'border: 1px solid #ccc; padding: 3px; margin: 3px; width: 210px; font-family: verdana; font-size: 11px;'; // This is the style for the captcah box.
		
		/* Advanced Users Variables */
		var $captchaLength = 5;
		var $imageWidth = 130;
		var $imageHeight = 30;
		var $imageTextColor = array(0,0,0); // RGB (Black)
		var $imageLineColor = array(255,20,147); // RGB (Black)
		var $imageBGColor = array(176,226,255); // RBG (Light Blue)
		var $captchaInputName = 'captcha_input';
		var $cookieName = 'NodstrumCaptcha';
		var $cookieTimeout = 300; // 5 mins.
		var $cleanupImages = true;
		
		// Check the directories exist and are writeable.
		
		function create() {
			// Cleanup before doing anything.
			$this->cleanup();
			
			// Define the dataset and set the key.
			$dataset = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','p','q','r','s','t','u','v','w','x','y','z',1,2,3,4,5,6,7,8,9);
			shuffle($dataset);
			$key = ''; // Initialise key.
			for($i=0; $i<$this->captchaLength; $i++) {

        $offset = rand(0, count($dataset));
        $myval  = isset($dataset[$offset]) ? $dataset[$offset] : 0 ;
				$key .= $this->strtoupper_modified(trim($myval));
			}
						
			// Prep and generate the key hash - sha1.
			$key = trim($key);
			$keyHash = sha1($key);
			
			// Generate the image.
			$imageName = $this->generateImage($key);
			if(!$imageName) {
				die('There was an error generating the Captcha - Check your directory path and permissions.');
			} else {
				// Set the cookie.
				$cookie = setcookie($this->cookieName, $keyHash, (time()+$this->cookieTimeout), '/');
				if(!$cookie) {
					die('Unable to set the cookie - Cookies must be enabled to use this form.');
				} else {
					// Return the captcha and input box.
					$captchaBox = '<div style="'.$this->captchaBoxStyle.'">
										'.$this->captchaLabel.'<br>
										<img src="'.$this->imageURL.'/'.$imageName.'" width="'.$this->imageWidth.'" height="'.$this->imageHeight.'" style="border: 1px solid #000; margin: 2px;" alt="CAPTCHA Image"><br>
										<input type="text" size="22" name="'.$this->captchaInputName.'" value="">
									</div>';
					return $captchaBox;	
				}
			}
		} // Create :: Function.
		
		function generateImage($key) {
			// Create the image.
			$imageName = 'CAPTCHA_'.(time()+$this->cookieTimeout).'_'.rand(1,101).'_'.rand(201,9007).'.png';
			$imageFilename = ''.$this->imageDirectory.'/'.$imageName.'';
			$image = imagecreate($this->imageWidth, $this->imageHeight);
			$background = imagecolorallocate($image, $this->imageBGColor[0], $this->imageBGColor[1], $this->imageBGColor[2]);
			
			// Set the text colour.
			$textColor = imagecolorallocate($image, $this->imageTextColor[0], $this->imageTextColor[1], $this->imageTextColor[2]);
			$lineColor = imagecolorallocate($image, $this->imageLineColor[0], $this->imageLineColor[1], $this->imageLineColor[2]);
			
			// Add some lines to screw over the form hackers.
			imageline($image, 0,0,120,20, $lineColor); 
			imageline($image, 1,0,121,21, $lineColor); 
			imageline($image, 40,0,80,50, $lineColor);
			imageline($image, 41,0,81,51, $lineColor);
			imageline($image, 90,0,80,50, $lineColor);
			
			// Put spaces in between the letters.
			$keySplit = $this->str_split_php4($key);
			$formattedKey = ''; // Initialise it.
			for($i=0; $i<$this->captchaLength; $i++) {
				$formattedKey .= ''.$keySplit[$i].' ';
			}
			
			// Add the text to the image.
			imagestring($image, 5, 20, 10, $formattedKey, $textColor); 
						
			// Save the image.
			$saveImage = imagegif($image, $imageFilename, 100);
			imagedestroy($image);
			if($saveImage) {
				return $imageName;
			} else {
				return false;
			}
		} // GenerateImage :: Function.
		
		function verify($userInput) {
			if(isset($_COOKIE[$this->cookieName])) {
				// Get the cookie and hash the user input.
				$cookieDataHash = trim($_COOKIE[$this->cookieName]);
				$userInputF = $this->strtoupper_modified(trim($userInput));
				$userInputHash = sha1($userInputF);
				
				// Cleanup - the form has been submitted.
				$this->cleanup();
				
				// Verfiy that the cookie data length is 40 characters long - SHA1 standard - it might have been tampered with.
				if(strlen($cookieDataHash) == 40) {
					if($cookieDataHash == $userInputHash) {
						return true;
					} else {
						return false;
					} // Cookie and user inputs match.
				} else {
					// The cookie data has been tampered with, dont verify.
					return false;
				} // CookieDataLength.
			} else {
				// No cookie was found, probably deleted, dont verify.
				return false;
			} // Cookie exists.
		} // Verify :: Function.
		
		function cleanup() {
			setcookie($this->cookieName, '', (time()-900000000), '/');
			if($openedDir = opendir($this->imageDirectory)) {
				while(($file = readdir($openedDir)) !== false) {
					if($file != "." && $file != "..") {
						$filenameE = explode('_', $file);
						$fileExpires = isset($filenameE[1]) ? $filenameE[1] : NULL  ;
						if($fileExpires <= time()) {
							unlink($this->imageDirectory.'/'.$file);
						} else {
							// Not expiring yet.
						}
					} else {
						// Skip it.
					}
				} // while - reading directory.
			} // Directory opened.
		} // Cleanup :: Function.
		
		function str_split_php4($text, $split = 1) {
			if (!is_string($text)) return false;
			if (!is_numeric($split) && $split < 1) return false;
			$len = strlen($text);
			$array = array();
			$s = 0;
			$e=$split;
			while ($s <$len) {
				$e=($e <$len)?$e:$len;
			    $array[] = substr($text, $s,$e);
			    $s = $s+$e;
			}
			return $array;
		} // str_split_php4 :: Function - php.net (kjensen - http://uk3.php.net/str_split)
		
		function strtoupper_modified($string) {
			$splitString = $this->str_split_php4($string);
			$fString = ''; // Initialise it.
			foreach($splitString as $value) {
				if(is_numeric($value)) {
					$fString .= $value;
				} else {
					$fString .= strtoupper($value);
				}
			}
			
			return $fString;
		} // strtoupper_modified :: Function
		
	} // Captcha :: Class.

?>
