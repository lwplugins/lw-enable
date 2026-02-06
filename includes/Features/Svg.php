<?php
/**
 * SVG upload feature.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Features;

use LightweightPlugins\Enable\Options;
use LightweightPlugins\Enable\Svg\Metadata;
use LightweightPlugins\Enable\Svg\MimeType;
use LightweightPlugins\Enable\Svg\Upload;

/**
 * Enables SVG file uploads with security sanitization.
 */
final class Svg {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! Options::get( 'svg' ) ) {
			return;
		}

		$this->init_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		$mime     = new MimeType();
		$upload   = new Upload();
		$metadata = new Metadata();

		// MIME type registration.
		add_filter( 'upload_mimes', array( $mime, 'add_svg' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $mime, 'check_filetype' ), 10, 5 );
		add_filter( 'getimagesize_mimes_to_exts', array( $mime, 'add_image_size_ext' ) );
		add_filter( 'wp_image_file_matches_image_mime', array( $mime, 'allow_image_mime' ), 10, 3 );
		add_filter( 'wp_calculate_image_srcset_meta', array( $mime, 'disable_srcset' ), 10, 4 );

		// Upload validation.
		add_filter( 'wp_handle_upload_prefilter', array( $upload, 'prefilter' ) );
		add_filter( 'wp_handle_sideload_prefilter', array( $upload, 'prefilter' ) );
		add_filter( 'wp_handle_upload', array( $upload, 'handle_result' ), 10, 2 );
		add_action( 'add_attachment', array( $upload, 'validate_attachment' ) );

		// Metadata.
		add_filter( 'wp_generate_attachment_metadata', array( $metadata, 'generate' ), 10, 2 );
	}
}
