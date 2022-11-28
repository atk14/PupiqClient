<?php
/**
 * Proxy handler for storing Pupiq files locally
 *
 * An experimental feature
 *
 * Usage:
 *
 * file: i/.htaccess
 *
 *	ErrorDocument 404 /i/error.php
 *
 * file: i/error.php
 *
 *	<?php
 *	require(__DIR__ . "/../atk14/load.php");
 *
 *	PupiqErrorHandler::HandleRequest($HTTP_REQUEST,$HTTP_RESPONSE);
 *	$HTTP_RESPONSE->flushAll();
 *
 */
class PupiqErrorHandler{

	/**
	 *
	 *
	 * @param HTTPRequest $request
	 * @param HTTPResponse $response
	 */
	static function HandleRequest($request,&$response){
		$directory = ATK14_DOCUMENT_ROOT;

		class_exists("Pupiq"); // autoload of the class Pupiq (in the class there are definitions of constants PUPIQ_*)

		$uri = $request->getRequestUri();
		$uri = preg_replace('/\/{2,}/','/',$uri); // /i/d///d/8ac///8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg
		$uri = preg_replace('/\?.*/','',$uri); // /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg?xxxx -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg
		$uri = preg_replace('/[^\/]+\/\.\.\//','/',$uri); // /i/../i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg

		if(!preg_match('/^\/(i|a)\//',$uri,$matches)){ throw new Exception("PupiqErrorHandler: Invalid URI"); } // takova bezp. pojistka
		$image_uri = $matches[1]=="i";

		$url = "http://".PUPIQ_IMG_HOSTNAME.$uri;
		$uf = new UrlFetcher($url);

		if(!$uf->found()){
			if(!$uf->getStatusCode()){
				// hmmm.. no status code? it looks like an error on the network
				$response->internalServerError();
			}else{
				self::_ExportUrlFetcher($response,$uf);
			}
			return;
		}

		// Just make sure that an image is an image
		if($image_uri && !preg_match('/^image\//',(string)$uf->getContentType())){
			self::_ExportUrlFetcher($response,$uf);
			return;
		}

		$ar = explode('/',$uri);

		$file = array_pop($ar); // ezaK3E_480x270_edd9bf26cbe7ce4e.jpg
		$dir = join("/",$ar);

		// TODO: vracet 500 v pripade, ze nejaka filesystemova operace selze

		Files::Mkdir("$directory/$dir");

		// pro zamezeni konfliktu v paralelnim zapisu je vytvoren soubor s unikatnim nazvem, ...
		$filename = "$directory/$dir/$file";
		$filename_to_write = "{$filename}_".uniqid();
		Files::WriteToFile($filename_to_write,$uf->getContent());
		// ... ktery je pak presunut
		Files::MoveFile($filename_to_write,$filename);

		if($time = strtotime($uf->getHeaderValue("Last-Modified"))){
			touch($filename, $time);
		}

		self::_ExportUrlFetcher($response,$uf);
	}

	protected static function _ExportUrlFetcher(&$response,$uf){
		$response->setStatusCode($uf->getStatusCode());
		$response->setContentType($uf->getContentType());
		foreach($uf->getResponseHeaders(array("as_hash" => true)) as $key => $value){
			if(in_array($key,array("ETag","Server","Connection"))){ continue; }
			$response->setHeader($key,$value);
		}
		$response->write($uf->getContent());
	}
}
