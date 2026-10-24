<?php
/**
 * Tests for the SVG DOM inspector.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit\Svg;

use LightweightPlugins\Enable\Svg\DomInspector;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LightweightPlugins\Enable\Svg\DomInspector
 */
final class DomInspectorTest extends TestCase {

	private const NS = 'xmlns="http://www.w3.org/2000/svg"';

	private static function load( string $xml ): \DOMDocument {
		$dom = new \DOMDocument();
		$dom->loadXML( $xml );
		return $dom;
	}

	/**
	 * @dataProvider provide_disallowed
	 */
	public function test_flags_disallowed_document( string $xml ): void {
		$this->assertFalse( DomInspector::is_safe( self::load( $xml ) ) );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public static function provide_disallowed(): array {
		$ns  = self::NS;
		$alt = 'xmlns:h="http://www.w3.org/1999/xhtml"';

		return array(
			'script element'               => array( "<svg {$ns}><script>1</script ></svg>" ),
			'uppercase script element'     => array( "<svg {$ns}><SCRIPT/></svg>" ),
			'script in other namespace'    => array( "<svg {$ns} {$alt}><h:script/></svg>" ),
			'foreignObject element'        => array( "<svg {$ns}><foreignObject/></svg>" ),
			'prefixed foreignObject'       => array( "<svg {$ns} xmlns:s=\"http://www.w3.org/2000/svg\"><s:foreignObject/></svg>" ),
			'on attribute'                 => array( "<svg {$ns}><rect onclick=\"1\"/></svg>" ),
			'control char in href scheme'  => array( "<svg {$ns}><a href=\"java&#x09;script:1\"/></svg>" ),
			'leading space in href scheme' => array( "<svg {$ns}><a href=\" &#x0A;JavaScript:1\"/></svg>" ),
			'vbscript href'                => array( "<svg {$ns}><a href=\"vbscript:1\"/></svg>" ),
			'xlink href scheme'            => array( "<svg {$ns} xmlns:xlink=\"http://www.w3.org/1999/xlink\"><a xlink:href=\"java&#x0D;script:1\"/></svg>" ),
			'non-image data href'          => array( "<svg {$ns}><a href=\"data:text/plain,1\"/></svg>" ),
			'animated href value'          => array( "<svg {$ns}><a><set attributeName=\"href\" to=\"javascript:1\"/></a></svg>" ),
			'animated values list'         => array( "<svg {$ns}><a><animate attributeName=\"href\" values=\"#a;javascript:1\"/></a></svg>" ),
			'prefixed iframe element'      => array( "<svg {$ns} {$alt}><h:iframe/></svg>" ),
			'prefixed object element'      => array( "<svg {$ns} {$alt}><h:object/></svg>" ),
			'prefixed embed element'       => array( "<svg {$ns} {$alt}><h:embed/></svg>" ),
			'frame element'                => array( "<svg {$ns}><frame/></svg>" ),
			'handler element'              => array( "<svg {$ns}><handler/></svg>" ),
			'control char in data scheme'  => array( "<svg {$ns}><rect data=\"java&#x09;script:1\"/></svg>" ),
			'srcdoc attribute'             => array( "<svg {$ns}><rect srcdoc=\"data:text/html,1\"/></svg>" ),
			'stylesheet instruction'       => array( "<?xml-stylesheet type=\"text/css\" href=\"s.css\"?><svg {$ns}/>" ),
			'instruction inside root'      => array( "<svg {$ns}><?x 1?></svg>" ),
			'non-svg root'                 => array( "<html {$alt}><!-- <svg></svg> --></html>" ),
			'svg root without namespace'   => array( '<svg><rect width="1" height="1"/></svg>' ),
			'svg root in other namespace'  => array( '<svg xmlns="http://www.w3.org/1999/xhtml"/>' ),
		);
	}

	/**
	 * @dataProvider provide_allowed
	 */
	public function test_allows_plain_document( string $xml ): void {
		$this->assertTrue( DomInspector::is_safe( self::load( $xml ) ) );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public static function provide_allowed(): array {
		$ns = self::NS;

		return array(
			'shapes'          => array( "<svg {$ns} viewBox=\"0 0 10 10\"><rect width=\"10\" height=\"10\" fill=\"#000\"/></svg>" ),
			'fragment link'   => array( "<svg {$ns}><a href=\"#r\"><rect id=\"r\" width=\"1\" height=\"1\"/></a></svg>" ),
			'https link'      => array( "<svg {$ns}><a href=\"https://example.com/\"><rect width=\"1\" height=\"1\"/></a></svg>" ),
			'png data image'  => array( "<svg {$ns}><image href=\"data:image/png;base64,AAAA\" width=\"1\" height=\"1\"/></svg>" ),
			'opacity animate' => array( "<svg {$ns}><rect width=\"1\" height=\"1\"><animate attributeName=\"opacity\" values=\"0;1\" dur=\"1s\"/></rect></svg>" ),
			'xml declaration' => array( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!-- export --><svg {$ns}><rect width=\"1\" height=\"1\"/></svg>" ),
			'prefixed root'   => array( '<s:svg xmlns:s="http://www.w3.org/2000/svg"><s:rect width="1" height="1"/></s:svg>' ),
			'editor metadata' => array( "<svg {$ns} xmlns:inkscape=\"http://www.inkscape.org/namespaces/inkscape\" xmlns:sodipodi=\"http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd\" inkscape:version=\"1.3\"><sodipodi:namedview id=\"v\"/><metadata/><g inkscape:label=\"L\"><rect width=\"1\" height=\"1\"/></g></svg>" ),
		);
	}
}
