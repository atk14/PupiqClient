<?php
defined("PUPIQ_API_KEY") || define("PUPIQ_API_KEY","123.The_Secret_Should_Be_Here");
defined("PUPIQ_API_URL") || define("PUPIQ_API_URL","http://i.pupiq.net/api/");
defined("PUPIQ_LANG") || define("PUPIQ_LANG","cs");
defined("PUPIQ_IMG_HOSTNAME") || define("PUPIQ_IMG_HOSTNAME",preg_replace('/https?:\/\/([^\/]+)\/.*$/','\1',PUPIQ_API_URL)); // "http://i.pupiq.net/api/" -> "i.pupiq.net"
defined("PUPIQ_PROXY_HOSTNAME") || define("PUPIQ_PROXY_HOSTNAME","");
defined("PUPIQ_HTTPS") || define("PUPIQ_HTTPS",false);

class Pupiq {
	var $_api_key = "";
	var $_original_width = null;
	var $_original_height = null;
	var $_width = null;
	var $_height = null;
	var $_lang = PUPIQ_LANG;
	var $_transformation_string = null;

	function __construct($url_or_api_key = "",$api_key = null){
		$url = "";
		if(preg_match('/^http/',$url_or_api_key)){
			$url = $url_or_api_key;
		}elseif(strlen($url_or_api_key)){
			$api_key = $url_or_api_key;
		}
		if(!isset($api_key)){ $api_key = PUPIQ_API_KEY; }

		$this->_api_key = $api_key;

		if($url){
			$this->setUrl($url);
		}
	}

	static function CreateImage($url_or_filename,&$err_msg = ""){
		$pupiq = new Pupiq();

		$params = array(
			"auth_token" => $pupiq->getAuthToken(),
		);
		$options = array(
			"acceptable_error_codes" => array(400),
		);
		$file = null;

		$adf = new ApiDataFetcher(PUPIQ_API_URL);
		if(preg_match('/^https?:/',$url_or_filename)){
			$params["url"] = $url_or_filename;
			$data = $adf->post("images/create_new",$params,$options);
		}else{
			$data = $adf->postFile("images/create_new",$url_or_filename,$params,$options);
		}

		if(!$data){
			$err_msg = $pupiq->_extractApiDataFetcherError($adf);
			return;
		}

		$pupiq->setUrl($data["url"]);
		return $pupiq;
	}

	/**
	 * $err_msg = $this->_extractApiDataFetcherError($api_data_fetcher);
	 */
	protected function _extractApiDataFetcherError($adf){
		$err_msg = _("Unknown error");

		$errors = $adf->getErrors();
		if(isset($errors[0]) && strlen($_err = trim($errors[0]))){
			$err_msg = $_err;
		}
		$err_msg = preg_replace('/^(image|attachment|auth_token): /','',$err_msg); // "image: Tiff format is not supported" -> "Tiff format is not supported"; "attachment: A very strange file" -> "A very strange file"
		return $err_msg;
	}

	/**
	 * $attachment = Pupiq::CreateAttachment("/path/to/file.pdf","sample.pdf"); // an instance of PupiqAttachment
	 */
	static function CreateAttachment($path_to_file,$filename,&$err_msg = ""){
		$pupiq = new Pupiq();

		$adf = new ApiDataFetcher(PUPIQ_API_URL);
		$data = $adf->postFile("attachments/create_new",array(
			"path" => $path_to_file,
			"name" => $filename 
		),array(
			"auth_token" => $pupiq->getAuthToken()
		),array(
			"acceptable_error_codes" => array(400)
		));

		if(!$data){
			$err_msg = $pupiq->_extractApiDataFetcherError($adf);
			return;
		}

		return new PupiqAttachment($data["url"]);
	}

	function getLang(){ return "cs"; }

	function setUrl($url){
		$this->_url = $url;

		$this->_base_href = null;
		$this->_original_width = null;
		$this->_original_height = null;
		$this->_width = null;
		$this->_height = null;
		$this->_transformation_string = null;
		$this->_suffix = null;
		$this->_code = null;

		// http://pupiq_srv.localhost/i/1/1/9/9/2000x1600/xlfbAz_800x600_94256fa57005d815.jpg
		// http://pupiq_srv.localhost/i/1/1/3f/3f/3008x2000/0HuQGW_800x800xc_0779b21d95f9ac08.jpg
		if(preg_match('/^http:\/\/[^\/]+(?<base_uri>\/i\/([0-9a-f]+\/){4}(?<original_width>\d+)x(?<original_height>\d+)\/)(?<code>[a-zA-Z0-9]+)_(?<width>\d+)x(?<height>\d+)(?<border>(|xc|xt|x[0-9a-f]{6}))_[0-9a-f]{16}\.(?<suffix>jpg|png)$/',$url,$matches)){
			$hostname = PUPIQ_PROXY_HOSTNAME ? PUPIQ_PROXY_HOSTNAME : PUPIQ_IMG_HOSTNAME;
			$this->_base_href = 'http'.(PUPIQ_HTTPS ? "s" : "").'://'.$hostname.$matches["base_uri"];
			$this->_original_width = (int)$matches["original_width"];
			$this->_original_height = (int)$matches["original_height"];
			$this->_width = $matches["width"];
			$this->_height = $matches["height"];
			$this->_transformation_string = "$matches[width]x$matches[height]$matches[border]";
			$this->_suffix = $matches["suffix"];
			$this->_code = $matches["code"];
			return true;
		}
		return false;
	}

	/**
	 * echo $pimage->getImgTag("100",array("attrs" => array("class" => "logo")));
	 */
	function getImgTag($geometry = null,$options = array()){
		$options += array(
			"alt" => "Image",
			"attrs" => array()
		);

		$this->setGeometry($geometry);
		$url = $this->getUrl();

		$attrs = $options["attrs"];
		$attrs += array(
			"src" => $url,
			"alt" => $options["alt"]
		);

		if($w = $this->getWidth()){ $attrs["width"] = $w; }
		if($h = $this->getHeight()){ $attrs["height"] = $h; }

		$_attrs = array();
		foreach($attrs as $k => $v){
			$_attrs[] = $k.'="'.htmlspecialchars($v).'"';
		}

		return '<img '.join(" ",$_attrs).' />';
	}

	function getUrl($tranformation_string = null){
		$tranformation_string && $this->setTransformation($tranformation_string);

		if(!$this->_code){
			return $this->_url;
		}

		$base_href = $this->_base_href;
		$code = $this->_code;
		$suffix = null;

		$transformation_string = $this->getTransformation($suffix);
		$token = $this->_calcToken($transformation_string);

		return "$base_href{$code}_".urlencode($transformation_string)."_$token.$suffix";
	}

	/**
	 * $pupiq->setGeometry("800x600");
	 * $pupiq->setGeometry("800");
	 * $pupiq->setGeometry("x600");
	 */
	function setGeometry($geometry){
		$this->setTransformation($geometry);
		return $this->getUrl();
	}

	/**
	 * $pupiq->setTransformation("80"); // width
	 * $pupiq->setTransformation("x60"); // height
	 * $pupiq->setTransformation("80x80"); // max width x max height (final geometry may be something like 80x60, 60x80...)
	 * $pupiq->setTransformation("!80x80"); // crop
	 * $pupiq->setTransformation("80x80x#ffffff"); // max width x max height, final geometry will be 80x80 and the background will be filled with the specified color
	 *
	 * $pupiq->setTransformation("1600x1600,enable_enlargement"); // max. vyska se zapnutym zvetsovanim
	 */
	function setTransformation($transformation_string){
		$transformation_string = trim($transformation_string);
		if(!$transformation_string){ return $this->getTransformation(); }

		$options = array();
		if(preg_match('/^(.+?),(.+)/',$transformation_string,$matches)){
			$transformation_string = $matches[1];
			$options = explode(',',$matches[2]); // array("enable_enlargement")
		}


		$this->_width = null;
		$this->_height = null;

		// zjednoduseny zapis orezu: "!80x80" -> "80x80xc"
		if(preg_match('/^!(\d+x\d+)$/',$transformation_string,$matches)){
			$transformation_string = "$matches[1]xc";
		}

		// "original" -> %original_width%x%original_height%
		if(in_array($transformation_string,array("original","orig"))){
			$transformation_string = $this->getOriginalWidth()."x".$this->getOriginalHeight();
		}

		// 3. zapisy, ktere zachovavaji pomer stran
		if(
			preg_match('/^(\d+)x?$/',$transformation_string,$matches) || // "80", "80x"
			preg_match('/^x(\d+)$/',$transformation_string,$matches) || // "x60"
			preg_match('/^(\d+)x(\d+)$/',$transformation_string,$matches) // "80x60"
		){

			// "80", "80x"
			if(preg_match('/^(\d+)x?$/',$transformation_string,$matches)){
				$this->setWidth($matches[1],true);

			// "x60"
			}elseif(preg_match('/^x(\d+)$/',$transformation_string,$matches)){
				$this->setHeight($matches[1],true);

			// "80x60"
			}elseif(preg_match('/^(\d+)x(\d+)$/',$transformation_string,$matches)){
				$this->setWidth($matches[1],true);
				if(!$this->getHeight() || $this->getHeight()>$matches[2]){
					$this->setHeight($matches[2],true);
				}
			}

			// zde je kontrola max. rozmeru
			// nesmi dojit k automatickemu zvetsovani, pokud to neni explicitne uvedeno (v $options "enable_enlargement")
			if($this->getOriginalWidth() && $this->getOriginalHeight() && !in_array('enable_enlargement',$options)){
				if($this->getWidth()>$this->getOriginalWidth()){
					$ratio = (float)$this->getOriginalWidth() / (float)$this->getWidth(); // mensi nez 1
					$new_width = $this->getOriginalWidth();
					$new_height = round($this->getHeight() * $ratio);
					$this->setWidth($new_width,false);
					$this->setHeight($new_height,false);

				}
				if($this->getHeight()>$this->getOriginalHeight()){
					// toto se stane pouze v pripade, ze $height == $original_height + 1;
					$this->setHeight($this->getOriginalHeight(),false);
				}
			}

			$transformation_string = $this->getWidth()."x".$this->getHeight();

		// "80x80xtransparent", "80x80xffffff", "80x80xcrop"
		}elseif(preg_match('/^(\d+)x(\d+)x(c|crop|t|transparent|#?[0-9a-fA-F]{6})$/',$transformation_string,$matches)){
			$this->setWidth($matches[1]);
			$this->setHeight($matches[2]);
			$border = $matches[3]; // transparent, #ffffff...
			if($border=="crop"){
				$border = "c";
				if(in_array("top",$options)){
					$border = "ct";
				}
				if(in_array("bottom",$options)){
					$border = "cb";
				}
			}elseif($border=="transparent"){
				$border = "t";
			}else{
				$border = strtolower(preg_replace('/^#/','',$border)); // "#FFFFFF" -> "ffffff"
			}
			$transformation_string = $this->getWidth()."x".$this->getHeight()."x$border";

		}else{
			throw new Exception("Pupiq: Invalid image transformation: $transformation_string");
		}

		$this->_transformation_string = $transformation_string;

		return $this->getTransformation();
	}

	/**
	 * echo $pupiq->getTransformation($force_suffix); // "800x600", $force_suffix=="jpeg"
	 * echo $pupiq->getTransformation($force_suffix); // "800x600,transparent", $force_suffix=="png"
	 */
	function getTransformation(&$force_suffix = null){
		$force_suffix = $this->_suffix; // TODO: kdyz tak o tom premyslim, prijde mi, ze bez transperentnosti by mohl byt obrazky vzdy jpeg
		if(!$this->_transformation_string){
			return $this->getWidth()."x".$this->getHeight();
		}
		if(preg_match('/xt$/',$this->_transformation_string)){
			$force_suffix = "png";
		}
		return $this->_transformation_string;
	}

	function getAscpectRation(){
		if(!$this->_original_width || !$this->_original_height){
			return null;
		}
		return $this->_original_width / $this->_original_height;
	}

	function getWidth(){ return (int)$this->_width; }
	function setWidth($width,$optimize_height = false){
		$width = (int)$width;
		$out = $this->_width;
		if($width>0 && $width<9999){
			$this->_width = $width;
		}
		if($optimize_height && $this->getAscpectRation()){
			$this->setHeight($this->getWidth()/$this->getAscpectRation());
		}
		return $out;
	}


	function getHeight(){ return (int)$this->_height; }
	function setHeight($height,$optimize_width = false){
		$height = (int)$height;
		$out = $this->_height;
		if($height>0 && $height<9999){
			$this->_height = $height;
		}
		if($optimize_width && $this->getAscpectRation()){
			$this->setWidth($this->getHeight() * $this->getAscpectRation());
		}
		return $out;
	}

	function getOriginalWidth(){ return $this->_original_width; }
	function getOriginalHeight(){ return $this->_original_height; }

	function getCode(){ return $this->_code; }

	function getApiKey(){ return $this->_api_key; }

	function _calcToken($tranformation_string){
		return substr(md5($tranformation_string.$this->getCode().$this->getApiKey()),0,16);
	}

	function getUserId(){
		preg_match('/^(\d+)./',$this->getApiKey(),$matches);
		return (int)$matches[1];
	}

	/**
	 * Id, pod kterym je obrazek ulozen v Pupiqove db
	 */
	function getImageId(){
		// http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg -> 75c1 -> 30145
		if(preg_match('/^https?:\/\/[^\/]+\/i\/[0-9a-f]+\/[0-9a-f]+\/[0-9a-f]+\/([0-9a-f]+)\//',$this->getUrl(),$matches)){
			return (int)hexdec($matches[1]);
		}
	}

	/**
	 * echo $pupiq->getAuthToken(); // "1.27dcf4b58864a6b44336df95d76681e3af4193a8297923b5ac193e5fa5ffc2b5"
	 */
	function getAuthToken($options = array()){
		$options += array(
			"current_time" => time()
		);
		$t = $options["current_time"] - ($options["current_time"] % (60 * 10)); // kazdych 10 minut jiny token
		return $this->getUserId().".".hash("sha256",$this->getApiKey().$t);
	}

	/**
	 * $tokens = $pupiq->getAllowedAuthTokens();
	 * print_r($tokens);
	 *
	 *		Array
	 *		(
	 *			[0] => 1.27dcf4b58864a6b44336df95d76681e3af4193a8297923b5ac193e5fa5ffc2b5
	 *			[1] => 1.6744af678d25319312c89857327fb8c3cc1791bc0d1751f2c74ca2675bf1c427
	 *			[2] => 1.7854beac75daa5c20c1af90774f8a578ba59f6204bf237d180e0ea779853e198
	 *		)

	 */
	function getAllowedAuthTokens(){
		$time = time();
		return array_unique(array(
			$this->getAuthToken(array("current_time" => $time)),
			$this->getAuthToken(array("current_time" => $time + (5 * 60))),
			$this->getAuthToken(array("current_time" => $time - (5 * 60))),
		));
	}

	function getId(){ return $this->toString(); } // pro TableRecord & DbMole, TODO: to be removed
	function toString(){ return $this->getUrl(); }
	function __toString(){ return $this->toString(); }
}
