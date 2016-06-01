<?php
require(__DIR__."/../src/app/helpers/modifier.pupiq_img.php");
class TcPupiqImgHelper extends TcBase {
	function test() {
		$image = "http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x537_52fa2ef3361053ff.jpg";

		$out = smarty_modifier_pupiq_img($image, "800x600", "class='banner banner-small',title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="Image" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", "class = ' banner banner-small', title='sample image'");
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="Image" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", 'class = " banner banner-small", title="sample image"');
		$this->assertEquals('<img class="banner banner-small" title="sample image" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="Image" width="800" height="536" />', $out);

		$out = smarty_modifier_pupiq_img($image, "800x600", 'class =banner-small, title=Obrazek ');
		$this->assertEquals('<img class="banner-small" title="Obrazek" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="Image" width="800" height="536" />', $out);


		$out = smarty_modifier_pupiq_img($image, "800x600", array('class' => 'banner-small', 'title' => 'Obrazek'));
		$this->assertEquals('<img class="banner-small" title="Obrazek" src="http://i.pupiq.net/i/2/2/75c/75c1/1300x872/gQs7Nv_800x536_b1ae08e7e3612505.jpg" alt="Image" width="800" height="536" />', $out);
	}
}
