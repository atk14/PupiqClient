<?php
class PupiqAttachment{
	var $_url = "";

	function __construct($url){
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
			"pdf" => "application/pdf",
		);
		$suffix = strtolower($this->getSuffix());
		if(isset($mime_types[$suffix])){
			return $mime_types[$suffix];
		}
	}

	function toString(){ return $this->getUrl(); }
	function __toString(){ return $this->toString(); }
}
