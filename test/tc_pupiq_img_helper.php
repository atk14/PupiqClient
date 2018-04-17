<?php
require(__DIR__."/../src/app/helpers/modifier.pupiq_img.php");

class TcPupiqImgHelper extends TcBase {

	function test() {
		$image = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";

		$out = smarty_modifier_pupiq_img($image, "800x600", "class='banner banner-small',title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", "class = ' banner banner-small', title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", 'class = " banner banner-small", title="sample image"');
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", 'class =banner-small, title=Image ');
		$this->assertEquals('<img class="banner-small" title="Image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", "title='Jane + Tarzan = Love',alt='Love Image',data-clickable");
		$this->assertEquals('<img title="Jane + Tarzan = Love" alt="Love Image" data-clickable src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", array('class' => 'banner-small', 'title' => 'Obrazek', 'data-clickable' => true));
		$this->assertEquals('<img class="banner-small" title="Obrazek" data-clickable src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />', $out);


		$out = smarty_modifier_pupiq_img($image, "2000x2000");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_1300x872_c6515a8e10b50ca9.jpg" alt="" width="1300" height="872" />',$out);

		$out = smarty_modifier_pupiq_img($image, "2000x2000,enable_enlargement=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_1300x872_c6515a8e10b50ca9.jpg" alt="" width="1300" height="872" />',$out);

		// enabling enlargement
		$out = smarty_modifier_pupiq_img($image, "2000x2000,enable_enlargement");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_2000x1341_2dbc5ca5509a7ce1.jpg" alt="" width="2000" height="1341" />',$out);

		$out = smarty_modifier_pupiq_img($image, "2000x2000,enable_enlargement=1");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_2000x1341_2dbc5ca5509a7ce1.jpg" alt="" width="2000" height="1341" />',$out);

		// watermarks
		$out = smarty_modifier_pupiq_img($image, "800x600,watermark");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/w/default/1/75c/75c1/1300x872/gQs7Nv_800x536_b16d624ce5698df7.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img($image, "800x600,watermark=logo");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/w/logo/1/75c/75c1/1300x872/gQs7Nv_800x536_03549f37bb6faf7f.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img($image, "800x600,watermark=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />',$out);

		$out = smarty_modifier_pupiq_img("http://i.pupiq.net/i/2/2/w/logo/1/75c/75c1/1300x872/gQs7Nv_800x536_6af91444713474bd.jpg", "800x600,watermark=0");
		$this->assertEquals('<img src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="" width="800" height="536" />',$out);
	}
}
