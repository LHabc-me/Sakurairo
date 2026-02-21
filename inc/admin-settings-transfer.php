<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'iro_settings_transfer_allowed_keys' ) ) {
	function iro_settings_transfer_allowed_keys() {
		static $allowed_keys = null;

		if ( null !== $allowed_keys ) {
			return $allowed_keys;
		}

		$allowed_keys = array();

		$current_options = iro_get_options_store();
		if ( is_array( $current_options ) ) {
			$allowed_keys = array_merge( $allowed_keys, array_keys( $current_options ) );
		}

		$customizer_file = get_template_directory() . '/inc/customizer.php';
		if ( is_readable( $customizer_file ) ) {
			$content = file_get_contents( $customizer_file );
			if ( false !== $content && preg_match_all( "/['\"]iro_key['\"]\\s*=>\\s*['\"]([^'\"]+)['\"]/", $content, $matches ) ) {
				$allowed_keys = array_merge( $allowed_keys, $matches[1] );
			}
		}

		$legacy_file = get_template_directory() . '/inc/customizer-migrated-fields.php';
		if ( is_readable( $legacy_file ) ) {
			$legacy_keys = require $legacy_file;
			if ( is_array( $legacy_keys ) ) {
				$allowed_keys = array_merge( $allowed_keys, $legacy_keys );
			}
		}

		$option_schema_file = get_template_directory() . '/opt/options/theme-options.php';
		if ( is_readable( $option_schema_file ) ) {
			$content = file_get_contents( $option_schema_file );
			if ( false !== $content && preg_match_all( "/['\"]id['\"]\\s*=>\\s*['\"]([^'\"]+)['\"]/", $content, $matches ) ) {
				$allowed_keys = array_merge( $allowed_keys, $matches[1] );
			}
		}

		$allowed_keys = array_filter(
			array_unique( array_map( 'strval', $allowed_keys ) ),
			static function ( $key ) {
				return '' !== $key;
			}
		);

		return $allowed_keys;
	}
}

if ( ! function_exists( 'iro_settings_transfer_register_tools_page' ) ) {
	function iro_settings_transfer_register_tools_page() {
		add_management_page(
			esc_html__( 'Shinonomeiro Settings Import/Export', 'Shinonomeiro_C' ),
			esc_html__( 'Shinonomeiro Settings', 'Shinonomeiro_C' ),
			'manage_options',
			'iro-settings-transfer',
			'iro_settings_transfer_render_tools_page'
		);
	}
	add_action( 'admin_menu', 'iro_settings_transfer_register_tools_page' );
}

if ( ! function_exists( 'iro_settings_transfer_render_notice' ) ) {
	function iro_settings_transfer_render_notice() {
		if ( ! isset( $_GET['iro_ie_status'] ) ) {
			return;
		}

		$status  = sanitize_text_field( wp_unslash( $_GET['iro_ie_status'] ) );
		$message = isset( $_GET['iro_ie_message'] ) ? sanitize_text_field( wp_unslash( $_GET['iro_ie_message'] ) ) : '';

		$class = 'notice notice-info';
		if ( 'success' === $status ) {
			$class = 'notice notice-success';
		} elseif ( 'error' === $status ) {
			$class = 'notice notice-error';
		}

		echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $message ) . '</p></div>';
	}
}

if ( ! function_exists( 'iro_settings_transfer_render_tools_page' ) ) {
	function iro_settings_transfer_render_tools_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Shinonomeiro Settings Import / Export', 'Shinonomeiro_C' ); ?></h1>
			<?php iro_settings_transfer_render_notice(); ?>
			<p><?php echo esc_html__( 'Export current theme settings as JSON, or import from a JSON backup file.', 'Shinonomeiro_C' ); ?></p>

			<h2><?php echo esc_html__( 'Export Settings', 'Shinonomeiro_C' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'iro_settings_export', 'iro_settings_export_nonce' ); ?>
				<input type="hidden" name="action" value="iro_settings_export">
				<?php submit_button( esc_html__( 'Download JSON', 'Shinonomeiro_C' ) ); ?>
			</form>

			<hr>

			<h2><?php echo esc_html__( 'Import Settings', 'Shinonomeiro_C' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( 'iro_settings_import', 'iro_settings_import_nonce' ); ?>
				<input type="hidden" name="action" value="iro_settings_import">
				<input type="file" name="iro_settings_file" accept="application/json,.json" required>
				<?php submit_button( esc_html__( 'Upload and Import', 'Shinonomeiro_C' ), 'primary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}
}

if ( ! function_exists( 'iro_settings_transfer_redirect_with_notice' ) ) {
	function iro_settings_transfer_redirect_with_notice( $status, $message ) {
		$url = add_query_arg(
			array(
				'page'           => 'iro-settings-transfer',
				'iro_ie_status'  => $status,
				'iro_ie_message' => $message,
			),
			admin_url( 'tools.php' )
		);

		wp_safe_redirect( $url );
		exit;
	}
}

if ( ! function_exists( 'iro_settings_transfer_export_handler' ) ) {
	function iro_settings_transfer_export_handler() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'Shinonomeiro_C' ) );
		}

		check_admin_referer( 'iro_settings_export', 'iro_settings_export_nonce' );

		$options = iro_get_options_store();
		$json    = wp_json_encode( $options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

		if ( false === $json ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Export failed: JSON encode error.', 'Shinonomeiro_C' ) );
		}

		$filename = 'shinonomeiro-options-' . gmdate( 'Ymd-His' ) . '.json';
		nocache_headers();
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'X-Content-Type-Options: nosniff' );
		echo $json;
		exit;
	}
	add_action( 'admin_post_iro_settings_export', 'iro_settings_transfer_export_handler' );
}

if ( ! function_exists( 'iro_settings_transfer_filter_import_data' ) ) {
	function iro_settings_transfer_filter_import_data( $decoded ) {
		$allowed = array_flip( iro_settings_transfer_allowed_keys() );
		$result  = array();

		foreach ( $decoded as $key => $value ) {
			if ( ! is_string( $key ) ) {
				continue;
			}
			if ( isset( $allowed[ $key ] ) ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'iro_settings_transfer_import_handler' ) ) {
	function iro_settings_transfer_import_handler() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'Shinonomeiro_C' ) );
		}

		check_admin_referer( 'iro_settings_import', 'iro_settings_import_nonce' );

		if ( empty( $_FILES['iro_settings_file']['tmp_name'] ) ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: file missing.', 'Shinonomeiro_C' ) );
		}

		$file = $_FILES['iro_settings_file'];
		if ( ! empty( $file['error'] ) || UPLOAD_ERR_OK !== $file['error'] ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: upload error.', 'Shinonomeiro_C' ) );
		}

		$raw = file_get_contents( $file['tmp_name'] );
		if ( false === $raw || '' === trim( (string) $raw ) ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: empty file.', 'Shinonomeiro_C' ) );
		}

		$decoded = json_decode( $raw, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: invalid JSON.', 'Shinonomeiro_C' ) );
		}

		if ( ! is_array( $decoded ) || array_values( $decoded ) === $decoded ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: JSON root must be an object.', 'Shinonomeiro_C' ) );
		}

		$filtered = iro_settings_transfer_filter_import_data( $decoded );
		if ( empty( $filtered ) ) {
			iro_settings_transfer_redirect_with_notice( 'error', esc_html__( 'Import failed: no allowed settings keys found.', 'Shinonomeiro_C' ) );
		}

		$current = iro_get_options_store();
		$merged  = array_replace( is_array( $current ) ? $current : array(), $filtered );

		update_option( IRO_OPTIONS_KEY, $merged );
		$GLOBALS['iro_options'] = $merged;

		if ( defined( 'IRO_OPTIONS_THEME_MOD_KEY' ) && IRO_OPTIONS_THEME_MOD_KEY ) {
			set_theme_mod( IRO_OPTIONS_THEME_MOD_KEY, $merged );
		}

		$message = sprintf(
			/* translators: 1: imported key count */
			esc_html__( 'Import completed. %d settings keys were updated.', 'Shinonomeiro_C' ),
			count( $filtered )
		);

		iro_settings_transfer_redirect_with_notice( 'success', $message );
	}
	add_action( 'admin_post_iro_settings_import', 'iro_settings_transfer_import_handler' );
}
