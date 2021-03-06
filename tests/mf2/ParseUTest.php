<?php
/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace mf2\Parser\test;

// Include Parser.php
require_once dirname(__DIR__) . '/../mf2/Parser.php';

use mf2\Parser,
	PHPUnit_Framework_TestCase,
	DateTime;

class ParseUTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		date_default_timezone_set('Europe/London');
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesA() {
		$input = '<div class="h-card"><a class="u-url" href="http://example.com">Awesome example website</a></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('url', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com', $output['items'][0]['properties']['url'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesImg() {
		$input = '<div class="h-card"><img class="u-photo" src="http://example.com/someimage.png"></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesArea() {
		$input = '<div class="h-card"><area class="u-photo" href="http://example.com/someimage.png"></area></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesObject() {
		$input = '<div class="h-card"><object class="u-photo" data="http://example.com/someimage.png"></object></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesAbbr() {
		$input = '<div class="h-card"><abbr class="u-photo" title="http://example.com/someimage.png"></abbr></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseU
	 */
	public function testParseUHandlesData() {
		$input = '<div class="h-card"><data class="u-photo" value="http://example.com/someimage.png"></data></div>';
		$parser = new Parser($input);
		$output = $parser->parse();
		
		$this->assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this->assertEquals('http://example.com/someimage.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsFromDocumentUrl() {
		$input = '<div class="h-card"><img class="u-photo" src="../image.png" /></div>';
		$parser = new Parser($input, 'http://example.com/things/more/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsFromBaseUrl() {
		$input = '<head><base href="http://example.com/things/more/andmore/" /></head><body><div class="h-card"><img class="u-photo" src="../image.png" /></div></body>';
		$parser = new Parser($input, 'http://example.com/things/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/more/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeUrlsInImpliedMicroformats() {
		$input = '<a class="h-card"><img src="image.png" /></a>';
		$parser = new Parser($input, 'http://example.com/things/more.html');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group baseUrl
	 */
	public function testResolvesRelativeBaseRelativeUrlsInImpliedMicroformats() {
		$input = '<base href="things/"/><a class="h-card"><img src="image.png" /></a>';
		$parser = new Parser($input, 'http://example.com/');
		$output = $parser->parse();
		
		$this->assertEquals('http://example.com/things/image.png', $output['items'][0]['properties']['photo'][0]);
	}
}

// EOF