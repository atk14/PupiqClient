<?php
class TcLinter extends TcBase {

	function test_php(){
		$suffixes = array("php");
		$forbidden_folders = array(
			".git",
			"test",
			"vendor",
		);

		$files = Files::FindFiles(__DIR__ . "/../",array("pattern" => '/\.('.join('|',$suffixes).')$/'));

		foreach($files as $file){
			$_file = str_replace(__DIR__ . "/../","",$file);
			if(preg_match('#^('.join('|',$forbidden_folders).')/#',$_file)){
				continue;
			}
			system("php -l ".escapeshellarg($file),$ret_val);
			$this->assertEquals(0,$ret_val,"There is syntax error in file $_file");
		}
	}
}
