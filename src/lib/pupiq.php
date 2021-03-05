<?php
defined("PUPIQ_API_KEY") || define("PUPIQ_API_KEY","123.The_Secret_Should_Be_Here");
defined("PUPIQ_API_URL") || define("PUPIQ_API_URL","https://i.pupiq.net/api/");
defined("PUPIQ_LANG") || define("PUPIQ_LANG","cs");
defined("PUPIQ_IMG_HOSTNAME") || define("PUPIQ_IMG_HOSTNAME",preg_replace('/https?:\/\/([^\/]+)\/.*$/','\1',PUPIQ_API_URL)); // "http://i.pupiq.net/api/" -> "i.pupiq.net"
defined("PUPIQ_PROXY_HOSTNAME") || define("PUPIQ_PROXY_HOSTNAME",""); // "www.example.com"
defined("PUPIQ_HTTPS") || define("PUPIQ_HTTPS",(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") || (!empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443));
defined("PUPIQ_DEFAULT_WATERMARK_DEFINITION") || define("PUPIQ_DEFAULT_WATERMARK_DEFINITION","default");

class Pupiq {

	const VERSION = "1.13";

	protected $_api_key = "";

	protected $_url = "";

	protected $_original_width;
	protected $_original_height;
	protected $_width;
	protected $_height;
	protected $_transformation_string;
	protected $_suffix;
	protected $_format;
	protected $_code;
	protected $_user_id;
	protected $_image_id;
	protected $_watermark; // "default", "logo", "text"..., default value is according to the PUPIQ_DEFAULT_WATERMARK_DEFINITION
	protected $_watermark_revision; // 1, 2, 3...

	protected $_lang = PUPIQ_LANG;

	static protected $_SupportedImageFormats = array("jpg","png","webp","svg");
	static protected $_ImageFormatsSupportingTransparency = array("png","svg","webp");

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

	/**
	 * Just another way to instantiate a Pupiq object
	 *
	 *	$pupiq = Pupiq::ToObject("http://i.pupiq.net/i/65/65/a53/1a53/756x1233/WSIRgf_756x1233_597a23f2092de822.jpg");
	 */
	static function ToObject($image_url){
		if($image_url){
			return new Pupiq("$image_url");
		}
	}

	static function CreateImage($url_or_filename,&$err_msg = ""){
		$pupiq = new Pupiq();

		$params = array(
			"auth_token" => $pupiq->getAuthToken(),
		);
		$options = array(
			"acceptable_error_codes" => array(400,403),
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
			"acceptable_error_codes" => array(400,403)
		));

		if(!$data){
			$err_msg = $pupiq->_extractApiDataFetcherError($adf);
			return;
		}

		return new PupiqAttachment($data["url"]);
	}

	function getLang(){ return "cs"; }

	function setUrl($url){

		$this->_resetObjectState();
		$this->_url = $url;

		// http://pupiq_srv.localhost/i/1/1/9/9/2000x1600/xlfbAz_800x600_94256fa57005d815.jpg
		// http://pupiq_srv.localhost/i/1/1/3f/3f/3008x2000/0HuQGW_800x800xc_0779b21d95f9ac08.jpg
		//
		// watermarks:
		// http://pupiq_srv.localhost/i/1/1/w/default/8/3f/3f/3008x2000/0HuQGW_800x800xc_0779b21d95f9ac08.jpg
		// "w/default/8/" stands for "watermark usage flag / watermark name / watermark revision"
		$base_uri = 'https?:\/\/(?<hostname>[^\/]+)\/i\/';
		$user_id = '(?<user_id>([0-9a-f]+\/){2})';
		$watermark = '(?<watermark>w\/(?<watermark_name>[a-z][a-z0-9._-]{0,49})\/(?<watermark_revision>[1-9][0-9]{0,3})\/|)';
		$image_id = '(?<image_id>([0-9a-f]+\/){2})';
		$original_geometry = '(?<original_geometry>(?<original_width>\d+)x(?<original_height>\d+)\/)';
		$border = '(?<border>(|xc|xt|x[0-9a-f]{6}))';
		$suffix = '(?<suffix>'.join('|',self::$_SupportedImageFormats).')';
		if(preg_match('/^'.$base_uri.$user_id.$watermark.$image_id.$original_geometry.'(?<code>[a-zA-Z0-9]+)_(?<width>\d+)x(?<height>\d+)'.$border.'_[0-9a-f]{16}\.'.$suffix.'$/',$url,$matches)){
			$this->_original_width = (int)$matches["original_width"];
			$this->_original_height = (int)$matches["original_height"];
			$this->_width = $matches["width"];
			$this->_height = $matches["height"];
			$this->_transformation_string = "$matches[width]x$matches[height]$matches[border]";
			$this->_suffix = $matches["suffix"];
			$this->_code = $matches["code"];
			$this->_user_id = $matches["user_id"]; // "1/1/"
			$this->_image_id = $matches["image_id"]; // "3f/2f/"
			if($matches["watermark"]){
				$this->_watermark = $matches["watermark_name"];
				$this->_watermark_revision = $matches["watermark_revision"];
			}
			return true;
		}
		return false;
	}

	/**
	 * echo $pimage->getImgTag("100",array("attrs" => array("class" => "logo")));
	 */
	function getImgTag($geometry = null,$options = array()){
		$options += array(
			"alt" => "",
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
			$_attrs[] = $v===true ? htmlspecialchars($k) : htmlspecialchars($k).'="'.htmlspecialchars($v).'"';
		}

		return '<img '.join(" ",$_attrs).' />';
	}

	function getUrl($tranformation_string = null){
		$tranformation_string && $this->setTransformation($tranformation_string);

		if(!$this->_code){
			return $this->_url;
		}

		$base_href = $this->_getBaseHref();
		$code = $this->_code;
		$suffix = null;

		$transformation_string = $this->getTransformation($suffix);
		$token = $this->_calcToken($transformation_string);

		return "$base_href{$code}_".urlencode($transformation_string)."_$token.$suffix";
	}

	protected function _getBaseHref(){
		$hostname = PUPIQ_PROXY_HOSTNAME ? PUPIQ_PROXY_HOSTNAME : PUPIQ_IMG_HOSTNAME;
		$watermark = $this->_watermark ? "w/$this->_watermark/$this->_watermark_revision/" : "";
		$original_geometry = "{$this->_original_width}x$this->_original_height/";
		return 'http'.(PUPIQ_HTTPS ? "s" : "")."://$hostname/i/$this->_user_id$watermark$this->_image_id$original_geometry";
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
	 * $pupiq->setTransformation("1600x1600,enable_enlargement"); // enabling enlargement (disabled by default)
	 *
	 * // watermarks
	 * $pupiq->setTransformation("1600x1600,watermark"); // default watermark will be applied
	 * $pupiq->setTransformation("1600x1600,watermark=default"); // the same, default watermark will be applied
	 * $pupiq->setTransformation("1600x1600,watermark=logo"); // special watermark named "logo" wull be applied
	 */
	function setTransformation($transformation_string){
		$transformation_string = trim($transformation_string);
		if(!$transformation_string){ return $this->getTransformation(); }

		$options = array();
		if(preg_match('/^(.+?),(.+)/',$transformation_string,$matches)){
			$transformation_string = $matches[1];
			$options = PupiqUtils::DecodeParams($matches[2]);
		}

		$options += array(
			"enable_enlargement" => null, // true or false; the default value is dependent on the image format, see below
			"watermark" => null,
			"format" => null, // "png", "jpg"
		);

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

		// Format: jpg, png or svg
		$this->_format = null;
		if($options["format"]){
			if(!in_array($options["format"],self::$_SupportedImageFormats)){
				throw new Exception("Pupiq: Invalid format $options[format], expecting ".join(" or ",self::$_SupportedImageFormats));
			}
			$this->_format = $options["format"];
		}
		$format = $this->_format ? $this->_format : preg_replace('/^.*\./','',$this->getUrl());

		if(is_null($options["enable_enlargement"])){
			$options["enable_enlargement"] = $format=="svg" ? true : false;
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
			if($this->getOriginalWidth() && $this->getOriginalHeight() && !$options["enable_enlargement"]){
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

		// "80x80xtransparent", "80x80xffffff", "80x80xcrop", "80x80xtransparent_or_ffffff"
		}elseif(preg_match('/^(\d+)x(\d+)x(c|crop|t|transparent|#?[0-9a-fA-F]{6}|transparent_or_#?[0-9a-fA-F]{6})$/',$transformation_string,$matches)){
			$this->setWidth($matches[1]);
			$this->setHeight($matches[2]);
			$border = $matches[3]; // transparent, #ffffff, transparent_or_#ffffff
			
			if(preg_match('/^transparent_or_(.*)/',$border,$matches)){
				$border = in_array($format,self::$_ImageFormatsSupportingTransparency) ? "transparent" : $matches[1]; // "transparent" or "#ffffff"
			}

			if($border=="crop"){
				$border = "c";
				if(isset($options["top"]) && $options["top"]){
					$border = "ct";
				}elseif(isset($options["bottom"]) && $options["bottom"]){
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

		// watermark
		$this->_watermark = null;
		$this->_watermark_revision = null;
		if($options["watermark"]){
			$this->_watermark = $options["watermark"]===true ? PUPIQ_DEFAULT_WATERMARK_DEFINITION : (string)$options["watermark"];
			$this->_watermark_revision = 1; // TODO:
		}

		return $this->getTransformation();
	}

	/**
	 * echo $pupiq->getTransformation($force_suffix); // "800x600", $force_suffix=="jpg"
	 * echo $pupiq->getTransformation($force_suffix); // "800x600,transparent", $force_suffix=="png"
	 */
	function getTransformation(&$force_suffix = null){
		$force_suffix = $this->_suffix;
		if($this->_format && in_array($this->_format,array("jpg","png","webp")) && in_array($this->_suffix,array("jpg","png","webp"))){
			$force_suffix = $this->_format;
		}
		if(!$this->_transformation_string){
			return $this->getWidth()."x".$this->getHeight();
		}
		if(preg_match('/xt$/',$this->_transformation_string)){
			// Transparent -> suffix needs to be png
			$force_suffix = in_array($force_suffix,self::$_ImageFormatsSupportingTransparency) ?  $force_suffix : self::$_ImageFormatsSupportingTransparency[0];
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

	/**
	 * Returns significant colors used in the image
	 *
	 * This method is considered as non-critical and informative.
	 * The process doesn't end when an error occurs during getColors() execution.
	 *
	 * Correct result is being cached for a long time.
	 *
	 *	$colors = $pupiq->getColors();
	 *	echo $colors["vibrant"]; // e.g. "#F9DB30"
	 *
	 * @return array
	 */
	function getColors(){
		$adf = new ApiDataFetcher(PUPIQ_API_URL);

		try {
			$colors = $adf->get("image_colors/detail",array(
				"url" => $this->getUrl(),
				"auth_token" => $this->getAuthToken(),
			),array(
				"acceptable_error_codes" => array("404","403"),
				"cache" => 60 * 60 * 24 * 30, // 30 days
			));
		}catch(Exception $e){
			trigger_error("Catched Pupiq exception: ".$e->getMessage());
			$colors = array();
		}

		if(!$colors || !array_key_exists("vibrant",$colors)){
			$colors = array(
				"vibrant" => null,
				"light_vibrant" => null,
				"dark_vibrant" => null,
				"muted" => null,
				"light_muted" => null,
				"dark_muted" => null,
			);
		}

		return $colors;
	}

	/**
	 * Returns information about the original
	 *
	 *	$data = $pupiq->getOriginalInfo(); // ["id" => 8043, "filename" => "2_1a5e_1f6b.jpg", "filesize" => 19841366, "mime_type" => "image/jpeg", "created_at" => "2017-07-14 11:27:22"]
	 */
	function getOriginalInfo(){
		$adf = new ApiDataFetcher(PUPIQ_API_URL);

		return $adf->get("originals/detail",array(
			"url" => $this->getUrl(),
			"auth_token" => $this->getAuthToken(),
		));
	}

	/**
	 * Returns the content of the original file
	 *
	 *	$original_content = $pupiq->downloadOriginal($headers);
	 *  //
	 *	$response->setHeaders($headers); // ['Content-Type' => 'image/jpeg', 'Content-Disposition' => 'attachment; filename="2_29910_29f57.jpg"', 'Content-Length' => '208331', ...]
	 *	$response->write($original_content);
	 */
	function downloadOriginal(&$http_headers = array()){
		$original_d = $this->getOriginalInfo();
		$http_headers = array();
		$http_headers["Content-Type"] = $original_d["mime_type"];
		$http_headers["Content-Disposition"] = sprintf('attachment; filename="%s"',$original_d["filename"]);
		$http_headers["Content-Length"] = $original_d["filesize"];

		$adf = new ApiDataFetcher(PUPIQ_API_URL);

		return $adf->post("originals/download",array(
			"url" => $this->getUrl(),
			"auth_token" => $this->getAuthToken(),
		),array(
			"return_raw_content" => true,
		));
	}

	function getCode(){ return $this->_code; }

	function getApiKey(){ return $this->_api_key; }

	function _calcToken($tranformation_string){
		return substr(md5($tranformation_string.$this->_watermark.$this->_watermark_revision.$this->getCode().$this->getApiKey()),0,16);
	}

	function getUserId(){
		preg_match('/^(\d+)./',$this->getApiKey(),$matches);
		return (int)$matches[1];
	}

	/**
	 * Returns ID of the image stored in the Pupiq database
	 *
	 * @return int
	 */
	function getImageId(){
		$ary = explode('/',$this->_image_id); // "75c/75c1/" -> ["75c","75c1",""]
		if(isset($ary[1])){
			return (int)hexdec($ary[1]);
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
	function toString(){ return (string)$this->getUrl(); }
	function __toString(){ return $this->toString(); }

	protected function _resetObjectState(){
		$this->_url = null;
		$this->_original_width = null;
		$this->_original_height = null;
		$this->_width = null;
		$this->_height = null;
		$this->_transformation_string = null;
		$this->_suffix = null;
		$this->_code = null;
		$this->_user_id = null;
		$this->_image_id = null;
		$this->_watermark = null;
		$this->_watermark_revision = null;
	}
}
