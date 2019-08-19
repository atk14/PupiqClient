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

	function test_formatting_image(){
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");

		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg",$image->getUrl("800x800"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.png",$image->getUrl("800x800,format=png"));

		// cropping
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800xc_442ea0bf0c2cd3c9.jpg",$image->getUrl("800x800xcrop"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xc_ac756ea1866ff62f.jpg",$image->getUrl("800x400xcrop"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xct_6fcbe211b5f8fad3.jpg",$image->getUrl("800x400xcrop,top"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xcb_cb6aa1d62e976200.jpg",$image->getUrl("800x400xcrop,bottom"));

		// colored background
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800x112233_d6b32b5f565ccbd5.jpg",$image->getUrl("800x800x#112233"));


		// transparent or colored background
		// jpg -> colored border
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800x002233_23ea47c40ef1c59a.jpg",$image->getUrl("800x800xtransparent_or_#002233"));
		// png -> transparent border
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.png");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800xt_e4493381151ea408.png",$image->getUrl("800x800xtransparent_or_#002233"));
	}

	function test_ToObject(){
		$obj = Pupiq::ToObject("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertTrue(is_object($obj));

		$this->assertEquals(null,Pupiq::ToObject(""));
		$this->assertEquals(null,Pupiq::ToObject(null));
	}
}
