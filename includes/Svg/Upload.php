<?php
/**
 * SVG upload handler.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Svg;

/**
 * Handles SVG file upload validation.
 */
final class Upload {

	/**
	 * Maximum SVG file size in bytes (5MB).
	 */
	private const MAX_SIZE = 5242880;

	/**
	 * Pre-filter SVG upload.
	 *
	 * @param array $file File data.
	 * @return array
	 */
	public function prefilter( array $file ): array {
		if ( ! isset( $file['type'] ) || 'image/svg+xml' !== $file['type'] ) {
			return $file;
		}

		if ( ! $this->validate_extension( $file ) ) {
			$file['error'] = __( 'Invalid file type. Only SVG files are allowed.', 'lw-enable' );
			return $file;
		}

		if ( isset( $file['size'] ) && $file['size'] > self::MAX_SIZE ) {
			$file['error'] = __( 'SVG file exceeds maximum size limit of 5MB.', 'lw-enable' );
			return $file;
		}

		if ( ! $this->validate_content( $file['tmp_name'] ) ) {
			$file['error'] = __( 'SVG file contains dangerous content.', 'lw-enable' );
			return $file;
		}

		return $file;
	}

	/**
	 * Handle upload result validation.
	 *
	 * @param array $upload  Upload data.
	 * @param mixed $context Upload context.
	 * @return array
	 */
	public function handle_result( array $upload, $context = null ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! isset( $upload['type'] ) || 'image/svg+xml' !== $upload['type'] ) {
			return $upload;
		}

		$path = realpath( $upload['file'] );
		if ( false === $path || ! is_file( $path ) ) {
			return array( 'error' => __( 'Invalid file path.', 'lw-enable' ) );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $path );
		if ( false !== $content && ! Sanitizer::is_valid( $content ) ) {
			wp_delete_file( $path );
			return array( 'error' => __( 'SVG file contains dangerous content.', 'lw-enable' ) );
		}

		return $upload;
	}

	/**
	 * Validate attachment after insert.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return void
	 */
	public function validate_attachment( int $attachment_id ): void {
		$post = get_post( $attachment_id );
		if ( ! $post || ! str_ends_with( $post->post_mime_type, 'svg+xml' ) ) {
			return;
		}

		$path = get_attached_file( $attachment_id );
		if ( ! $path || ! file_exists( $path ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $path );
		if ( false !== $content && ! Sanitizer::is_valid( $content ) ) {
			wp_delete_file( $path );
			wp_delete_attachment( $attachment_id, true );
			wp_die(
				esc_html__( 'SVG file contains dangerous content.', 'lw-enable' ),
				'',
				array( 'response' => 403 )
			);
		}
	}

	/**
	 * Validate file extension.
	 *
	 * @param array $file File data.
	 * @return bool
	 */
	private function validate_extension( array $file ): bool {
		if ( ! isset( $file['name'] ) ) {
			return true;
		}

		return 'svg' === strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
	}

	/**
	 * Validate file content.
	 *
	 * @param string $tmp_name Temporary file path.
	 * @return bool
	 */
	private function validate_content( string $tmp_name ): bool {
		$path = realpath( $tmp_name );
		if ( false === $path || ! is_file( $path ) ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $path );
		if ( false === $content ) {
			return false;
		}

		return Sanitizer::is_valid( $content );
	}
}
