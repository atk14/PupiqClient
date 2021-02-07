<?php
require_once(__DIR__ . "/../src/app/helpers/modifier.img_url.php");
require_once(__DIR__ . "/../src/app/helpers/modifier.img_width.php");
require_once(__DIR__ . "/../src/app/helpers/modifier.img_height.php");
require_once(__DIR__ . "/../src/app/helpers/modifier.img_color.php");

class TcHelpers extends TcBase {

	function test_modifier_image_url(){
		$image_url = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";

		$url1 = smarty_modifier_img_url($image_url);
		$url2 = smarty_modifier_img_url($image_url,"50x50");

		$this->assertTrue(!!strlen($url1));
		$this->assertTrue(!!strlen($url2));

		$this->assertNotEquals($url1,$url2);

		$this->assertContains("_800x537_",$url1);
		$this->assertContains("_50x33_",$url2);
	}

	function test_modifier_img_width_and_height(){
		$image_url = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";
		
		$this->assertEquals(800,smarty_modifier_img_width($image_url));
		$this->assertEquals(537,smarty_modifier_img_height($image_url));

		$this->assertEquals(400,smarty_modifier_img_width($image_url,"400"));
		$this->assertEquals(268,smarty_modifier_img_height($image_url,"400"));
	}

	function test_modifier_img_color(){
		$image_url = "https://i.pupiq.net/i/65/65/27e/2927e/1272x920/9cUpr1_800x800xc_6c2a983e5ac4792b.jpg";
		//
		$this->assertEquals("#E7505B",smarty_modifier_img_color($image_url));
		$this->assertEquals("#E7505B",smarty_modifier_img_color($image_url,"vibrant"));
		$this->assertEquals("#E7505B",smarty_modifier_img_color($image_url,"vibrant or light_vibrant"));
		$this->assertEquals("#D67E89",smarty_modifier_img_color($image_url,"light_vibrant"));
		$this->assertEquals("#D67E89",smarty_modifier_img_color($image_url,"light_vibrant or vibrant"));
		$this->assertEquals("#2CAC95",smarty_modifier_img_color($image_url,"dark_vibrant"));
		$this->assertEquals("#6CB1AE",smarty_modifier_img_color($image_url,"muted"));
		$this->assertEquals("#C69F9C",smarty_modifier_img_color($image_url,"light_muted"));
		$this->assertEquals("#573031",smarty_modifier_img_color($image_url,"dark_muted"));
		$this->assertEquals("",smarty_modifier_img_color($image_url,"nonsence"));

		// This image has no light_vibrant color
		$image_url = "https://i.pupiq.net/i/65/65/27c/2927c/1272x920/JuSG6C_800x800xc_648c5aa725d85c4b.jpg";
		//
		$this->assertEquals("#9E6A0B",smarty_modifier_img_color($image_url,"vibrant"));
		$this->assertEquals("",smarty_modifier_img_color($image_url,"light_vibrant"));
		$this->assertEquals("#9E6A0B",smarty_modifier_img_color($image_url,"light_vibrant or vibrant"));
	}
}
