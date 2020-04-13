<?php
class TcPupiq extends TcBase{

	function test(){
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");

		$this->assertEquals(1300,$image->getOriginalWidth());
		$this->assertEquals(872,$image->getOriginalHeight());

		$this->assertEquals(800,$image->getWidth());
		$this->assertEquals(537,$image->getHeight());

		$this->assertEquals(101,$image->getUserId()); // !! TODO: Toto je podle PUPIQ_API_KEY a nikoli podle URL
		$this->assertEquals(30145,$image->getImageId()); // hexdec(75c1)

		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_246234d94216d836.jpg","$image");

		// svg image
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.svg");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_246234d94216d836.svg","$image");

		// no image url given
		$image = new Pupiq();
		$this->assertEquals("","$image");

		// hostname is automatically corrected
		$image = new Pupiq("http://alpha.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_246234d94216d836.jpg","$image");

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

		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg",$image->getUrl("800x800"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.png",$image->getUrl("800x800,format=png"));

		// cropping
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800xc_d3d44c33f84756c0.jpg",$image->getUrl("800x800xcrop"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xc_a451a51e48dceb38.jpg",$image->getUrl("800x400xcrop"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xct_937564d9e86e192f.jpg",$image->getUrl("800x400xcrop,top"));
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x400xcb_d1a736c9bf38b8b1.jpg",$image->getUrl("800x400xcrop,bottom"));

		// colored background
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800x112233_a3ea991200aa9d92.jpg",$image->getUrl("800x800x#112233"));


		// transparent or colored background
		// jpg -> colored border
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800x002233_943d5488db5aa953.jpg",$image->getUrl("800x800xtransparent_or_#002233"));
		// png -> transparent border
		$image = new Pupiq("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.png");
		$this->assertEquals("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x800xt_e62352d612057e07.png",$image->getUrl("800x800xtransparent_or_#002233"));
	}

	function test_ToObject(){
		$obj = Pupiq::ToObject("http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_f6dd0e0882712ab1.jpg");
		$this->assertTrue(is_object($obj));

		$this->assertEquals(null,Pupiq::ToObject(""));
		$this->assertEquals(null,Pupiq::ToObject(null));
	}

	function test_getAllowedAuthTokens(){
		$pupiq = new Pupiq();

		$tokens = $pupiq->getAllowedAuthTokens(array("current_time" => strtotime("2020-03-12 15:30:00")));
		$this->assertEquals(array("101.5d41a0d152a07deef4e06cde9b7bba8fe836482b940bf1623513c654d3904156","101.e6aa5740355b714ed53434636c30c399a5ef8a016d275006aaad3964717b2972"),$tokens);

		$tokens = $pupiq->getAllowedAuthTokens(array("current_time" => strtotime("2020-03-12 15:31:00")));
		$this->assertEquals(array("101.5d41a0d152a07deef4e06cde9b7bba8fe836482b940bf1623513c654d3904156","101.e6aa5740355b714ed53434636c30c399a5ef8a016d275006aaad3964717b2972"),$tokens);

		$tokens = $pupiq->getAllowedAuthTokens(array("current_time" => strtotime("2020-03-12 15:40:00")));
		$this->assertEquals(array("101.921027304a702c0b149eb220ab43fe48d0f44efbf82d093a5c7059eb3d2b4fd8","101.5d41a0d152a07deef4e06cde9b7bba8fe836482b940bf1623513c654d3904156"),$tokens);

		// -- salting

		$tokens = $pupiq->getAllowedAuthTokens(array("current_time" => strtotime("2020-03-12 15:30:00"), "salt" => ""));
		$this->assertEquals(array("101.5d41a0d152a07deef4e06cde9b7bba8fe836482b940bf1623513c654d3904156","101.e6aa5740355b714ed53434636c30c399a5ef8a016d275006aaad3964717b2972"),$tokens);

		$tokens = $pupiq->getAllowedAuthTokens(array("current_time" => strtotime("2020-03-12 15:30:00"), "salt" => "somethingExtra"));
		$this->assertEquals(array("101.6be61939394107966f049b800b52ba1e776d48e3bb8b78ab10bc528e7cdd0484","101.3baa559b223697577e9bce8ade1b388e4eebcf0388f2edb2bc4cea1111250d3d"),$tokens);
	}
}
