<?php
/**
 * Settings REST controller.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Rest\Admin;

use LightweightPlugins\Enable\Admin\Settings\FieldsData;
use LightweightPlugins\Enable\Admin\SettingsStore;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * GET/POST lw-enable/v1/admin/settings.
 */
final class SettingsController {

	/**
	 * Register the routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			Routes::NAMESPACE,
			'/admin/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( Routes::class, 'can_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( Routes::class, 'can_manage' ),
				),
			)
		);
	}

	/**
	 * Current options plus the screen structure.
	 *
	 * @return WP_REST_Response
	 */
	public function get_settings(): WP_REST_Response {
		return $this->response( SettingsStore::current() );
	}

	/**
	 * Partial update: only the submitted keys change. Non-option params
	 * (e.g. `_locale`) are dropped by the merge.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function save_settings( WP_REST_Request $request ): WP_REST_Response {
		return $this->response( SettingsStore::save( $request->get_params() ) );
	}

	/**
	 * Response shape shared by GET and POST.
	 *
	 * @param array<string, bool> $options Typed options.
	 * @return WP_REST_Response
	 */
	private function response( array $options ): WP_REST_Response {
		return new WP_REST_Response(
			array(
				'options' => $options,
				'meta'    => array(
					'sections' => SettingsMeta::sections( FieldsData::get_sections(), FieldsData::get_descriptions() ),
				),
			)
		);
	}
}
