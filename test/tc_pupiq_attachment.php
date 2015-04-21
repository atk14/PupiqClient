<?php
class TcPupiqAttachment extends TcBase{
	function test(){
		$attachment = new PupiqAttachment("http://i.pupiq.net/a/c/c/2/2/15257/user%20manual.pdf");

		$this->assertEquals("user manual.pdf",$attachment->getFilename());
		$this->assertEquals("15257",$attachment->getFilesize());
		$this->assertEquals("pdf",$attachment->getSuffix());
		$this->assertEquals("application/pdf",$attachment->getMimeType());
	}
}
