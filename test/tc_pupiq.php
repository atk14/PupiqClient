<?php
class TcPupiq extends TcBase{
	function test(){
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg");

		$this->assertEquals(1300,$image->getOriginalWidth());
		$this->assertEquals(872,$image->getOriginalHeight());

		$this->assertEquals(800,$image->getWidth());
		$this->assertEquals(537,$image->getHeight());

		$this->assertEquals(123,$image->getUserId()); // !! TODO: Toto je podle PUPIQ_API_KEY a nikoli podle URL
		$this->assertEquals(30145,$image->getImageId()); // hexdec(75c1)
	}
}
