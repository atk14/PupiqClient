<?php
class PupiqAttachment{
	var $_url = "";

	function __construct($url){
		// pokud to vypada jako zname URL, vrazime tam spravny hostname
		if(preg_match('/^https?:\/\/[^\/]+(\/a\/.+)$/',$url,$matches)){
			$hostname = PUPIQ_PROXY_HOSTNAME ? PUPIQ_PROXY_HOSTNAME : PUPIQ_IMG_HOSTNAME;
			$url = "http://$hostname$matches[1]";
		}

		$this->_url = $url;
	}

	function getUrl(){ return $this->_url; }

	/**
	 * $attachment = new PupiqAttachment("http://.../sample.pdf");
	 * echo $attachment->getSuffix(); // "sample.pdf"
	 */
	function getFilename(){
		$filename = preg_replace('/^.+\/([^\/]+)$/','\1',$this->getUrl());
		return urldecode($filename);
	}

	/**
	 * $attachment = new PupiqAttachment("http://.../sample.pdf");
	 * echo $attachment->getSuffix(); // "pdf"
	 */
	function getSuffix(){
		if(preg_match('/\.([^\.]+)$/',$this->getUrl(),$matches)){
			return $matches[1];
		}
	}

	/**
	 * echo $attachment->getFilesize(); // 123454
	 */
	function getFilesize(){
		// "http://i.pupiq.net/a/c/c/2/2/15257/sample.pdf" -> 15257
		if(preg_match('/\/(\d+)\/[^\/]+$/',$this->getUrl(),$matches)){
			return $matches[1];
		}
	}

	/**
	 * eco $attachment->getMimeType(); // "application/pdf"
	 */
	function getMimeType(){
		static $mime_types = array(
			"jpg" => "image/jpeg",
			"png" => "image/png",
			"tiff" => "image/tiff",
			"giff" => "image/giff",
			"svg" => "image/svg+xml",
			"bmp" => "image/bmp",

			"doc" => "application/msword",
			"xls" => "application/vnd.ms-excel",

			"odt" => "application/vnd.oasis.opendocument.text",
			"ods" => "application/vnd.oasis.opendocument.spreadsheet",

			"eps" => "application/postscript",
			"ai" => "application/postscript",
			"pdf" => "application/pdf",

			"zip" => "application/zip",
		);
		$suffix = strtolower($this->getSuffix());
		if(isset($mime_types[$suffix])){
			return $mime_types[$suffix];
		}
	}

	function toString(){ return $this->getUrl(); }
	function __toString(){ return $this->toString(); }
}
