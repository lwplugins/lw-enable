<?php
/**
 * Settings Page class.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Admin;

use LightweightPlugins\Enable\Admin\Settings\FieldsData;
use LightweightPlugins\Enable\Options;

/**
 * Handles the settings page.
 */
final class SettingsPage {

	/**
	 * Page slug.
	 */
	public const SLUG = 'lw-enable';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		ParentPage::maybe_register();

		add_submenu_page(
			ParentPage::SLUG,
			__( 'Enable', 'lw-enable' ),
			__( 'Enable', 'lw-enable' ),
			'manage_options',
			self::SLUG,
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		$valid_hooks = array(
			'toplevel_page_' . ParentPage::SLUG,
			ParentPage::SLUG . '_page_' . self::SLUG,
		);

		if ( ! in_array( $hook, $valid_hooks, true ) ) {
			return;
		}

		wp_enqueue_style(
			'lw-enable-admin',
			LW_ENABLE_URL . 'assets/css/admin.css',
			array(),
			LW_ENABLE_VERSION
		);

		wp_enqueue_script(
			'lw-enable-admin',
			LW_ENABLE_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			LW_ENABLE_VERSION,
			true
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->maybe_save();
		$options      = Options::get_all();
		$sections     = FieldsData::get_sections();
		$descriptions = FieldsData::get_descriptions();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'LW Enable', 'lw-enable' ); ?></h1>

			<?php if ( isset( $_GET['saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved.', 'lw-enable' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'lw_enable_save', 'lw_enable_nonce' ); ?>
				<input type="hidden" name="lw_enable_active_tab" value="">

				<div class="lw-enable-settings">
					<?php $this->render_tabs_nav( $sections ); ?>

					<div class="lw-enable-tab-content">
						<?php $this->render_tabs_content( $sections, $options, $descriptions ); ?>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render tab navigation.
	 *
	 * @param array $sections Sections data.
	 * @return void
	 */
	private function render_tabs_nav( array $sections ): void {
		$first = true;
		?>
		<ul class="lw-enable-tabs">
			<?php foreach ( $sections as $slug => $section ) : ?>
				<li>
					<a href="#<?php echo esc_attr( $slug ); ?>" <?php echo $first ? 'class="active"' : ''; ?>>
						<span class="dashicons <?php echo esc_attr( $section['icon'] ); ?>"></span>
						<?php echo esc_html( $section['title'] ); ?>
					</a>
				</li>
				<?php $first = false; ?>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Render tab content panels.
	 *
	 * @param array $sections     Sections data.
	 * @param array $options      Current options.
	 * @param array $descriptions Field descriptions.
	 * @return void
	 */
	private function render_tabs_content( array $sections, array $options, array $descriptions ): void {
		$first = true;

		foreach ( $sections as $slug => $section ) {
			$active = $first ? ' active' : '';
			printf( '<div id="tab-%s" class="lw-enable-tab-panel%s">', esc_attr( $slug ), esc_attr( $active ) );
			printf( '<h2>%s</h2>', esc_html( $section['title'] ) );
			echo '<table class="form-table">';

			foreach ( $section['fields'] as $key => $label ) {
				printf(
					'<tr><th scope="row">%s</th><td><label><input type="checkbox" name="lw_enable[%s]" value="1" %s> %s</label></td></tr>',
					esc_html( $label ),
					esc_attr( $key ),
					checked( $options[ $key ] ?? false, true, false ),
					esc_html( $descriptions[ $key ] ?? '' )
				);
			}

			echo '</table></div>';
			$first = false;
		}
	}

	/**
	 * Save settings if posted.
	 *
	 * @return void
	 */
	private function maybe_save(): void {
		if ( ! isset( $_POST['lw_enable_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['lw_enable_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'lw_enable_save' ) ) {
			return;
		}

		$defaults = Options::get_defaults();
		$options  = array();

		foreach ( array_keys( $defaults ) as $key ) {
			$options[ $key ] = ! empty( $_POST['lw_enable'][ $key ] );
		}

		Options::save( $options );

		$tab  = isset( $_POST['lw_enable_active_tab'] ) ? sanitize_text_field( wp_unslash( $_POST['lw_enable_active_tab'] ) ) : '';
		$hash = $tab ? '#' . $tab : '';

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::SLUG . '&saved=1' . $hash ) );
		exit;
	}
}
