<?php
/**
 * Uninstall script.
 *
 * @package LWEnable
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'lw_enable' );
