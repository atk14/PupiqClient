<?php
require(__DIR__."/../src/app/helpers/modifier.pupiq_img.php");

class TcPupiqImgHelper extends TcBase {

	function test() {
		$jpeg_image = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";
		$svg_image = "http://i.pupiq.net/i/2/2/50f/2f50f/240x115/kzWFGm_240x115_5c0089cc671a5ceb.svg";
		$png_image = "http://i.pupiq.net/i/2/2/18/18/929x662/r31HRT_800x570_f45c6d3a0a669f58.png";

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", "class='banner banner-small',title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", "class = ' banner banner-small', title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", 'class = " banner banner-small", title="sample image"');
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", 'class =banner-small, title=Image ');
		$this->assertEquals('<img class="banner-small" title="Image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", "title='Jane + Tarzan = Love',alt='Love Image',data-clickable");
		$this->assertEquals('<img title="Jane + Tarzan = Love" alt="Love Image" data-clickable src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600", array('class' => 'banner-small', 'title' => 'Obrazek', 'data-clickable' => true));
		$this->assertEquals('<img class="banner-small" title="Obrazek" data-clickable src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />', $out);


		$out = smarty_modifier_pupiq_img($jpeg_image, "2000x2000");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_1300x872_6d74f8b73731d8c3.jpg" alt="" width="1300" height="872" />',$out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "2000x2000,enable_enlargement=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_1300x872_6d74f8b73731d8c3.jpg" alt="" width="1300" height="872" />',$out);

		// enabling enlargement
		$out = smarty_modifier_pupiq_img($jpeg_image, "2000x2000,enable_enlargement");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_2000x1341_8c314017d1fd46aa.jpg" alt="" width="2000" height="1341" />',$out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "2000x2000,enable_enlargement=1");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_2000x1341_8c314017d1fd46aa.jpg" alt="" width="2000" height="1341" />',$out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "2000x2000,enable_enlargement=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_1300x872_6d74f8b73731d8c3.jpg" alt="" width="1300" height="872" />',$out);

		// for a svg image, the default value of enable_enlargement is true
		$out = smarty_modifier_pupiq_img($svg_image, "800x600");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/50f/2f50f/240x115/kzWFGm_800x383_dec9b82660290fd6.svg" alt="" width="800" height="383" />', $out);

		$out = smarty_modifier_pupiq_img($svg_image, "800x600,enable_enlargement");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/50f/2f50f/240x115/kzWFGm_800x383_dec9b82660290fd6.svg" alt="" width="800" height="383" />', $out);

		$out = smarty_modifier_pupiq_img($svg_image, "800x600,enable_enlargement=1");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/50f/2f50f/240x115/kzWFGm_800x383_dec9b82660290fd6.svg" alt="" width="800" height="383" />', $out);

		$out = smarty_modifier_pupiq_img($svg_image, "800x600,enable_enlargement=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/50f/2f50f/240x115/kzWFGm_240x115_5c0089cc671a5ceb.svg" alt="" width="240" height="115" />', $out);

		// watermarks
		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600,watermark");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/w/default/1/75c/75c1/1300x872/gQs7Nv_800x536_c5c9068b8d6da031.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600,watermark=logo");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/w/logo/1/75c/75c1/1300x872/gQs7Nv_800x536_2f675544f6ad03bb.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img($jpeg_image, "800x600,watermark=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img("http://i.pupiq.net/i/2/2/w/logo/1/75c/75c1/1300x872/gQs7Nv_800x536_2f675544f6ad03bb.jpg", "800x600,watermark=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_28caf7fe5e3a333b.jpg" alt="" width="800" height="536" />',$out);
	}
}
