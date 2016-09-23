<?php
// Experimentalni lokalni proxy!
class PupiqErrorHandler{
	static function HandleRequest($request,&$response){
		$directory = ATK14_DOCUMENT_ROOT;

		class_exists("Pupiq"); // autoload tridy Pupiq (tam jsou totiz definovany potrebne konstanty PUPIQ_*)
		
		$uri = $request->getRequestUri();
		$uri = preg_replace('/\/{2,}/','/',$uri); // /i/d///d/8ac///8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg
		$uri = preg_replace('/\?.*/','',$uri); // /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg?xxxx -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg
		$uri = preg_replace('/[^\/]+\/\.\.\//','/',$uri); // /i/../i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg -> /i/d/d/8ac/8ac/800x450/ezaK3E_480x270_edd9bf26cbe7ce4e.jpg

		if(!preg_match('/^\/(i|a)\//',$uri)){ throw new Exception("PupiqErrorHandler: Invalid URI"); } // takova bezp. pojistka

		$url = "http://".PUPIQ_IMG_HOSTNAME.$uri;
		$uf = new UrlFetcher($url);

		if(!$uf->found()){
			if(!$uf->getStatusCode()){
				// hmmm.. nemame ani status_code, tak to vypada jako chyba na siti
				$response->internalServerError();
			}else{
				$response->setStatusCode($uf->getStatusCode());
				$response->setContentType($uf->getContentType());
				$response->write($uf->getContent());
			}
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
		
		$response->setStatusCode($uf->getStatusCode());
		$response->setContentType($uf->getContentType());
		$response->write($uf->getContent());
	}
}
