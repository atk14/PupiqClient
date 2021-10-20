<?php
class TcPupiqAttachment extends TcBase{

	function test(){
		$attachment = new PupiqAttachment("http://i.pupiq.net/a/c/c/2a1/f2a1/15257/user%20manual.pdf");
		$this->assertEquals("user manual.pdf",$attachment->getFilename());
		$this->assertEquals("15257",$attachment->getFilesize());
		$this->assertEquals("pdf",$attachment->getSuffix());
		$this->assertEquals("application/pdf",$attachment->getMimeType());
		$this->assertEquals(62113,$attachment->getAttachmentId());


		$attachment = new PupiqAttachment("http://i.pupiq.net/a/65/65/cf4/cf4/2358/sample.webp");
		$this->assertEquals("image/webp",$attachment->getMimeType());
	}
}
