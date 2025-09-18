<?php
class TcPupiqUtils extends TcBase {

	function test(){
		$params = PupiqUtils::DecodeParams('alt=Image,title="Very nice image",data-clickable');
		$this->assertEquals(array("alt" => "Image", "title" => "Very nice image", "data-clickable" => true),$params);

		$params = PupiqUtils::DecodeParams('alt=Image,title="Very nice image\\, isn\'t it?",data-clickable');
		$this->assertEquals(array("alt" => "Image", "title" => "Very nice image, isn't it?", "data-clickable" => true),$params);

		$this->assertEquals(array(
			"equals" => "=",
			"comma" => ",",
			"both" => "=,",
		),PupiqUtils::DecodeParams("equals=\\=,comma=\\,,both=\\=\\,"));

		$params = PupiqUtils::DecodeParams("alt='',title=");
		$this->assertEquals(array("alt" => "", "title" => ""),$params);

		$params = PupiqUtils::DecodeParams(array("alt" => "Image", "title" => "Another very nice image", "data-clickable" => true));
		$this->assertEquals(array("alt" => "Image", "title" => "Another very nice image", "data-clickable" => true),$params);

		$this->assertEquals(array(),PupiqUtils::DecodeParams(''));
		$this->assertEquals(array(),PupiqUtils::DecodeParams(array()));
	}
}
