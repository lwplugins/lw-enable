<?php
/**
 * SVG MIME type registration.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Svg;

/**
 * Registers SVG MIME type support in WordPress.
 */
final class MimeType {

	/**
	 * Add SVG to allowed MIME types.
	 *
	 * @param array $mimes Allowed MIME types.
	 * @return array
	 */
	public function add_svg( array $mimes ): array {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Check SVG filetype and extension.
	 *
	 * @param array       $data      File data.
	 * @param string      $file      File path.
	 * @param string      $filename  Filename.
	 * @param array|null  $mimes     Allowed MIME types.
	 * @param string|null $real_mime Real MIME type.
	 * @return array
	 */
	public function check_filetype( array $data, string $file, string $filename, ?array $mimes, ?string $real_mime = null ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( 'svg' !== pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			return $data;
		}

		$data['ext']             = 'svg';
		$data['type']            = 'image/svg+xml';
		$data['proper_filename'] = $filename;

		return $data;
	}

	/**
	 * Add SVG to image size extensions.
	 *
	 * @param array $mimes Image size extensions.
	 * @return array
	 */
	public function add_image_size_ext( array $mimes ): array {
		$mimes['image/svg+xml'] = 'svg';
		return $mimes;
	}

	/**
	 * Allow SVG image MIME type matching.
	 *
	 * @param bool   $result   Current result.
	 * @param string $file     File path.
	 * @param string $filename Filename.
	 * @return bool
	 */
	public function allow_image_mime( bool $result, string $file, string $filename ): bool {
		return str_ends_with( $filename, '.svg' ) || $result;
	}

	/**
	 * Disable srcset for SVG images (not applicable).
	 *
	 * @param array  $image_meta    Image metadata.
	 * @param array  $size_array    Size array.
	 * @param string $image_src     Image source.
	 * @param int    $attachment_id Attachment ID.
	 * @return array
	 */
	public function disable_srcset( array $image_meta, array $size_array, string $image_src, int $attachment_id ): array {
		if ( 'image/svg+xml' === get_post_mime_type( $attachment_id ) && is_array( $image_meta ) ) {
			$image_meta['sizes'] = array();
		}

		return $image_meta;
	}
}
