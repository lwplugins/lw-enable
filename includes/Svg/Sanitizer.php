<?php
/**
 * SVG sanitizer.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Svg;

/**
 * Validates and sanitizes SVG content.
 */
final class Sanitizer {

	/**
	 * Dangerous patterns to detect in SVG files.
	 *
	 * @var array<string>
	 */
	private const PATTERNS = array(
		// Script tags and JavaScript.
		'/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
		'/<[\w:]+:script\b/i',
		'/<\w+\s[^>]*script\w*\s*=/i',
		'/javascript:/i',

		// Event handlers.
		'/\bon\w+\s*=/i',

		// Dangerous elements.
		'/<iframe\b/i',
		'/<object\b/i',
		'/<embed\b/i',
		'/<[\w:]*foreignobject\b/i',

		// External references.
		'/<use\s+href\s*=\s*["\']?https?:/i',
		'/<image\s+href\s*=\s*["\']?https?:/i',
		'/xlink:href\s*=\s*["\']?https?:/i',

		// Style-related threats.
		'/<style\b[^>]*>.*?<\/style>/is',
		'/<[\w:]+:style\b[^>]*>.*?<\/[\w:]+:style>/is',
		'/expression\s*\(/i',
		'/@import/i',
		'/url\s*\(\s*["\']?javascript:/i',
		'/style\s*=\s*["\'][^"\']*expression\s*\(/i',
		'/style\s*=\s*["\'][^"\']*javascript:/i',
		'/behavior\s*:/i',

		// Data URL threats.
		'/data:\s*[^,]*(script|javascript)/i',
		'/data:\s*text\/html/i',
		'/href\s*=\s*["\']?data:.*base64/i',

		// XML/DTD threats.
		'/<!DOCTYPE\b/i',
		'/<!ENTITY\b/i',
		'/&\w+;/',
		'/%\w+;/',

		// CDATA with malicious content.
		'/<\!\[CDATA\[.*?(script|javascript).*?\]\]>/is',

		// Obfuscation patterns.
		'/\\\\u[0-9a-f]{4}/i',
		'/\\\\x[0-9a-f]{2}/i',
		'/eval\s*\(/i',
		'/setTimeout\s*\(/i',
		'/setInterval\s*\(/i',
		'/Function\s*\(/i',
		'/\\[0-9]{1,3}/',
	);

	/**
	 * Validate SVG content.
	 *
	 * @param string $content SVG content.
	 * @return bool
	 */
	public static function is_valid( string $content ): bool {
		if ( empty( $content ) ) {
			return false;
		}

		if ( ! str_contains( $content, '<svg' ) || ! str_contains( $content, '</svg>' ) ) {
			return false;
		}

		if ( self::has_dangerous_patterns( $content ) ) {
			return false;
		}

		$decoded = self::decode_entities( $content );
		if ( self::has_dangerous_patterns( $decoded ) ) {
			return false;
		}

		return self::is_valid_xml( $content );
	}

	/**
	 * Check content for dangerous patterns.
	 *
	 * @param string $content Content to check.
	 * @return bool
	 */
	private static function has_dangerous_patterns( string $content ): bool {
		foreach ( self::PATTERNS as $pattern ) {
			if ( preg_match( $pattern, $content ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Decode HTML entities to reveal obfuscated content.
	 *
	 * @param string $content Content to decode.
	 * @return string
	 */
	private static function decode_entities( string $content ): string {
		$content = html_entity_decode( $content, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		$content = preg_replace_callback(
			'/&#x([0-9a-f]+);/i',
			static function ( $matches ) {
				$codepoint = hexdec( $matches[1] );
				return $codepoint <= 0x10FFFF ? mb_chr( $codepoint, 'UTF-8' ) : '';
			},
			$content
		);

		$content = preg_replace_callback(
			'/&#([0-9]+);/i',
			static function ( $matches ) {
				$codepoint = (int) $matches[1];
				return $codepoint <= 0x10FFFF ? mb_chr( $codepoint, 'UTF-8' ) : '';
			},
			$content
		);

		return $content;
	}

	/**
	 * Validate XML structure with XXE protection.
	 *
	 * @param string $content XML content.
	 * @return bool
	 */
	private static function is_valid_xml( string $content ): bool {
		$errors = libxml_use_internal_errors( true );
		libxml_clear_errors();

		try {
			$dom = new \DOMDocument();
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->resolveExternals = false;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->substituteEntities = false;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->recover = false;

			$flags  = LIBXML_PARSEHUGE | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOCDATA;
			$result = $dom->loadXML( $content, $flags );

			return false !== $result && empty( libxml_get_errors() );
		} catch ( \Exception $e ) {
			return false;
		} finally {
			libxml_use_internal_errors( $errors );
			libxml_clear_errors();
		}
	}
}
