<?php
/**
 * SVG metadata handler.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Svg;

/**
 * Generates metadata for SVG attachments.
 */
final class Metadata {

	/**
	 * Generate SVG attachment metadata.
	 *
	 * @param array $metadata      Attachment metadata.
	 * @param int   $attachment_id Attachment ID.
	 * @return array
	 */
	public function generate( array $metadata, int $attachment_id ): array {
		$post = get_post( $attachment_id );
		if ( ! $post || ! str_ends_with( $post->post_mime_type, 'svg+xml' ) ) {
			return $metadata;
		}

		$path = get_attached_file( $attachment_id );
		if ( ! $path || ! file_exists( $path ) ) {
			return $metadata;
		}

		$dimensions = $this->get_dimensions( $path );
		if ( $dimensions ) {
			$metadata['width']  = $dimensions['width'];
			$metadata['height'] = $dimensions['height'];
		}

		return $metadata;
	}

	/**
	 * Get SVG dimensions via DOMDocument.
	 *
	 * @param string $path File path.
	 * @return array|null
	 */
	private function get_dimensions( string $path ): ?array {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $path );
		if ( false === $content ) {
			return null;
		}

		$errors = libxml_use_internal_errors( true );
		libxml_clear_errors();

		try {
			$dom = new \DOMDocument();
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->resolveExternals = false;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->substituteEntities = false;

			$flags = LIBXML_PARSEHUGE | LIBXML_NOERROR | LIBXML_NOWARNING;
			if ( ! $dom->loadXML( $content, $flags ) ) {
				return null;
			}

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$svg = $dom->documentElement;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( ! $svg || 'svg' !== $svg->nodeName ) {
				return null;
			}

			return $this->from_attributes( $svg ) ?? $this->from_viewbox( $svg );
		} catch ( \Exception $e ) {
			return null;
		} finally {
			libxml_use_internal_errors( $errors );
			libxml_clear_errors();
		}
	}

	/**
	 * Get dimensions from width/height attributes.
	 *
	 * @param \DOMElement $svg SVG element.
	 * @return array|null
	 */
	private function from_attributes( \DOMElement $svg ): ?array {
		$width  = $svg->getAttribute( 'width' );
		$height = $svg->getAttribute( 'height' );

		if ( ! $width || ! $height ) {
			return null;
		}

		$width  = (int) preg_replace( '/[^0-9.]/', '', $width );
		$height = (int) preg_replace( '/[^0-9.]/', '', $height );

		if ( $width > 0 && $height > 0 ) {
			return array(
				'width'  => $width,
				'height' => $height,
			);
		}

		return null;
	}

	/**
	 * Get dimensions from viewBox attribute.
	 *
	 * @param \DOMElement $svg SVG element.
	 * @return array|null
	 */
	private function from_viewbox( \DOMElement $svg ): ?array {
		$viewbox = $svg->getAttribute( 'viewBox' );
		if ( ! $viewbox ) {
			return null;
		}

		$values = preg_split( '/[\s,]+/', trim( $viewbox ) );
		if ( count( $values ) < 4 ) {
			return null;
		}

		$width  = (int) $values[2];
		$height = (int) $values[3];

		if ( $width > 0 && $height > 0 ) {
			return array(
				'width'  => $width,
				'height' => $height,
			);
		}

		return null;
	}
}
