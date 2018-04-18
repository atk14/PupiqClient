<?php
class TcPupiqUtils extends TcBase {

	function test(){
		$params = PupiqUtils::DecodeParams('alt=Image,title="Very nice image",data-clickable');
		$this->assertEquals(array("alt" => "Image", "title" => "Very nice image", "data-clickable" => true),$params);

		$params = PupiqUtils::DecodeParams(array("alt" => "Image", "title" => "Another very nice image", "data-clickable" => true));
		$this->assertEquals(array("alt" => "Image", "title" => "Another very nice image", "data-clickable" => true),$params);

		$this->assertEquals(array(),PupiqUtils::DecodeParams(''));
		$this->assertEquals(array(),PupiqUtils::DecodeParams(array()));
	}
}
