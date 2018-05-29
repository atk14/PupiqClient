<?php
class TcPupiq extends TcBase{

	function test(){
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");

		$this->assertEquals(1300,$image->getOriginalWidth());
		$this->assertEquals(872,$image->getOriginalHeight());

		$this->assertEquals(800,$image->getWidth());
		$this->assertEquals(537,$image->getHeight());

		$this->assertEquals(123,$image->getUserId()); // !! TODO: Toto je podle PUPIQ_API_KEY a nikoli podle URL
		$this->assertEquals(30145,$image->getImageId()); // hexdec(75c1)

		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg","$image");

		// no image url given
		$image = new Pupiq();
		$this->assertEquals("","$image");

		// hostname is automatically corrected
		$image = new Pupiq("http://alpha.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg","$image");

		// non pupiq image given
		$image = new Pupiq();
		$image->setUrl("nice-image.jpg");
		$this->assertEquals(null,$image->getOriginalWidth());
		$this->assertEquals(null,$image->getOriginalHeight());
		$this->assertEquals(null,$image->getWidth());
		$this->assertEquals(null,$image->getHeight());
		$this->assertEquals(null,$image->getImageId());
		$this->assertEquals("nice-image.jpg","$image");
	}
}
