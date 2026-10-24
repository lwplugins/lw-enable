<?php
/**
 * SVG DOM inspector.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Svg;

/**
 * Checks a parsed SVG document for disallowed elements and attributes.
 */
final class DomInspector {

	/**
	 * Element local names that are not allowed (lowercase).
	 *
	 * @var array<string>
	 */
	private const BLOCKED_ELEMENTS = array( 'script', 'foreignobject' );

	/**
	 * Attribute local names that carry a URL or an animated value (lowercase).
	 *
	 * @var array<string>
	 */
	private const URL_ATTRIBUTES = array( 'href', 'src', 'action', 'formaction', 'from', 'to', 'by', 'values' );

	/**
	 * Allowed data: URL media types (lowercase).
	 *
	 * @var array<string>
	 */
	private const DATA_IMAGE_TYPES = array( 'image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/avif', 'image/bmp' );

	/**
	 * Whether the document contains only allowed elements and attributes.
	 *
	 * @param \DOMDocument $dom Parsed document.
	 * @return bool
	 */
	public static function is_safe( \DOMDocument $dom ): bool {
		foreach ( $dom->getElementsByTagName( '*' ) as $element ) {
			if ( ! self::is_element_allowed( $element ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check one element and its attributes.
	 *
	 * @param \DOMElement $element Element.
	 * @return bool
	 */
	private static function is_element_allowed( \DOMElement $element ): bool {
		$name = strtolower( (string) ( $element->localName ?? $element->nodeName ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOM API property.
		if ( in_array( $name, self::BLOCKED_ELEMENTS, true ) ) {
			return false;
		}

		foreach ( $element->attributes as $attribute ) {
			if ( ! self::is_attribute_allowed( $attribute ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check one attribute.
	 *
	 * @param \DOMAttr $attribute Attribute.
	 * @return bool
	 */
	private static function is_attribute_allowed( \DOMAttr $attribute ): bool {
		$name  = strtolower( (string) ( $attribute->localName ?? $attribute->nodeName ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOM API property.
		$value = (string) $attribute->value;

		if ( str_starts_with( $name, 'on' ) ) {
			return false;
		}

		if ( 'attributename' === $name ) {
			return ! str_starts_with( self::normalize( $value ), 'on' );
		}

		if ( ! in_array( $name, self::URL_ATTRIBUTES, true ) ) {
			return true;
		}

		foreach ( explode( ';', $value ) as $part ) {
			if ( ! self::is_url_allowed( $part ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check the scheme of a URL value.
	 *
	 * @param string $url URL value.
	 * @return bool
	 */
	private static function is_url_allowed( string $url ): bool {
		$url = self::normalize( $url );

		if ( str_starts_with( $url, 'javascript:' ) || str_starts_with( $url, 'vbscript:' ) ) {
			return false;
		}

		if ( ! str_starts_with( $url, 'data:' ) ) {
			return true;
		}

		$type = strtok( substr( $url, 5 ), ',;' );

		return false !== $type && in_array( $type, self::DATA_IMAGE_TYPES, true );
	}

	/**
	 * Lowercase and remove control and whitespace characters.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function normalize( string $value ): string {
		return strtolower( (string) preg_replace( '/[\x00-\x20\x7F\p{Z}]+/u', '', $value ) );
	}
}
