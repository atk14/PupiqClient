<?php
class PupiqAttachment{

	var $_url = "";

	function __construct($url){
		// pokud to vypada jako zname URL, vrazime tam spravny hostname
		if(preg_match('/^https?:\/\/[^\/]+(\/a\/.+)$/',$url,$matches)){
			class_exists("Pupiq"); // autoload tridy Pupiq (tam jsou totiz definovany potrebne konstanty PUPIQ_*)
			$hostname = PUPIQ_PROXY_HOSTNAME ? PUPIQ_PROXY_HOSTNAME : PUPIQ_IMG_HOSTNAME;
			$url = "http".(PUPIQ_HTTPS ? "s" : "")."://$hostname$matches[1]";
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
	 * echo $attachment->getMimeType(); // "application/pdf"
	 */
	function getMimeType(){
		static $mime_types = array(
			"jpg" => "image/jpeg",
			"png" => "image/png",
			"tiff" => "image/tiff",
			"gif" => "image/gif",
			"svg" => "image/svg+xml",
			"bmp" => "image/bmp",
			"webp" => "image/webp",

			"doc" => "application/msword",
			"xls" => "application/vnd.ms-excel",
			"ppt" => "application/vnd.ms-powerpoint",
			"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",

			"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",

			"odt" => "application/vnd.oasis.opendocument.text",
			"ods" => "application/vnd.oasis.opendocument.spreadsheet",

			"eps" => "application/postscript",
			"ai" => "application/postscript",
			"pdf" => "application/pdf",

			"zip" => "application/zip",

			"mp3" => "audio/mpeg",

			"apk" => "application/vnd.android.package-archive",
			"jar" => "application/java-archive",

			"avi" => "video/x-msvideo",
			"mkv" => "video/x-matroska",
			"mp4" => "video/mp4",
			"mov" => "video/quicktime",

			"ics" => "text/calendar",
			"ifb" => "text/calendar",
		);
		$suffix = strtolower($this->getSuffix());
		if(isset($mime_types[$suffix])){
			return $mime_types[$suffix];
		}
	}

	/**
	 * Returns ID of the attachment stored in the Pupiq database
	 *
	 * @return int
	 */
	function getAttachmentId(){
		if(preg_match('/https?:\/\/[^\/]+\/a\/[0-9a-f]+\/[0-9a-f]+\/[0-9a-f]+\/(?<attachment_id>[0-9a-f]+)\//',$this->getUrl(),$matches)){
			return (int)hexdec($matches["attachment_id"]);
		}
	}

	function toString(){ return $this->getUrl(); }
	function __toString(){ return $this->toString(); }
}
