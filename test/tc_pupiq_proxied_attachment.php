<?php
class TcPupiqProxiedAttachment extends TcBase {

	function test(){
		$attachment = new PupiqAttachment("http://i.pupiq.net/a/c/c/2a1/f2a1/15257/user%20manual.pdf");
		$this->assertEquals("http://img.example.com/a/c/c/2a1/f2a1/15257/user%20manual.pdf",$attachment->getUrl());
		$this->assertEquals("http://i.pupiq.net/a/c/c/2a1/f2a1/15257/user%20manual.pdf",$attachment->getUrl(["direct" => true]));
	}
}
