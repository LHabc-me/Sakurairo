<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.

if ( ! function_exists( 'csf_sakurairo_ajax_guard' ) ) {
  function csf_sakurairo_ajax_guard( $args = array() ) {
    if ( ! function_exists( 'sakurairo_ajax_guard' ) ) {
      return true;
    }

    return sakurairo_ajax_guard( $args );
  }
}
/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_get_icons' ) ) {
  function csf_get_icons() {

    if ( ! csf_sakurairo_ajax_guard( array(
      'action' => 'csf-get-icons',
      'nonce_action' => 'csf_icon_nonce',
      'nonce_field' => 'nonce',
      'capability' => 'manage_options',
      'rate_limit' => 60,
      'rate_window' => 60,
    ) ) ) {
      return;
    }

    ob_start();

    $icon_library = ( apply_filters( 'csf_fa4', false ) ) ? 'fa4' : 'fa5';

    Shinonomeiro_CSF::include_plugin_file( 'fields/icon/'. $icon_library .'-icons.php' );

    $icon_lists = apply_filters( 'csf_field_icon_add_icons', csf_get_default_icons() );

    if ( ! empty( $icon_lists ) ) {

      foreach ( $icon_lists as $list ) {

        echo ( count( $icon_lists ) >= 2 ) ? '<div class="csf-icon-title">'. esc_attr( $list['title'] ) .'</div>' : '';

        foreach ( $list['icons'] as $icon ) {
          echo '<i title="'. esc_attr( $icon ) .'" class="'. esc_attr( $icon ) .'"></i>';
        }

      }

    } else {

      echo '<div class="csf-error-text">'. esc_html__( 'No data available.', 'sakurairo_csf' ) .'</div>';

    }

    $content = ob_get_clean();

    wp_send_json_success( array( 'content' => $content ) );

  }
  add_action( 'wp_ajax_csf-get-icons', 'csf_get_icons' );
}

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_export' ) ) {
  function csf_export() {

    $unique = ( ! empty( $_GET[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'unique' ] ) ) : '';

    if ( ! csf_sakurairo_ajax_guard( array(
      'action' => 'csf-export',
      'nonce_action' => 'csf_backup_nonce',
      'nonce_field' => 'nonce',
      'capability' => 'manage_options',
      'rate_limit' => 20,
      'rate_window' => 60,
    ) ) ) {
      return;
    }

    if ( empty( $unique ) ) {
      die( esc_html__( 'Error: Invalid key.', 'sakurairo_csf' ) );
    }

    // Export
    header('Content-Type: application/json');
    header('Content-disposition: attachment; filename=backup-'. gmdate( 'd-m-Y' ) .'.json');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo json_encode( get_option( $unique ) );

    die();

  }
  add_action( 'wp_ajax_csf-export', 'csf_export' );
}

/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_import_ajax' ) ) {
  function csf_import_ajax() {

    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';
    $data   = ( ! empty( $_POST[ 'data' ] ) ) ? wp_kses_post_deep( json_decode( wp_unslash( trim( $_POST[ 'data' ] ) ), true ) ) : array();

    if ( ! csf_sakurairo_ajax_guard( array(
      'action' => 'csf-import',
      'nonce_action' => 'csf_backup_nonce',
      'nonce_field' => 'nonce',
      'capability' => 'manage_options',
      'rate_limit' => 20,
      'rate_window' => 60,
    ) ) ) {
      return;
    }

    if ( empty( $unique ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid key.', 'sakurairo_csf' ) ) );
    }

    if ( empty( $data ) || ! is_array( $data ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: The response is not a valid JSON response.', 'sakurairo_csf' ) ) );
    }

    // Success
    update_option( $unique, $data );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_csf-import', 'csf_import_ajax' );
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_reset_ajax' ) ) {
  function csf_reset_ajax() {

    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';

    if ( ! csf_sakurairo_ajax_guard( array(
      'action' => 'csf-reset',
      'nonce_action' => 'csf_backup_nonce',
      'nonce_field' => 'nonce',
      'capability' => 'manage_options',
      'rate_limit' => 20,
      'rate_window' => 60,
    ) ) ) {
      return;
    }

    // Success
    delete_option( $unique );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_csf-reset', 'csf_reset_ajax' );
}

/**
 *
 * Chosen Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'csf_chosen_ajax' ) ) {
  function csf_chosen_ajax() {

    $type  = ( ! empty( $_POST[ 'type' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'type' ] ) ) : '';
    $term  = ( ! empty( $_POST[ 'term' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'term' ] ) ) : '';
    $query = ( ! empty( $_POST[ 'query_args' ] ) ) ? wp_kses_post_deep( $_POST[ 'query_args' ] ) : array();
    $capability = apply_filters( 'csf_chosen_ajax_capability', 'manage_options' );

    if ( ! csf_sakurairo_ajax_guard( array(
      'action' => 'csf-chosen',
      'nonce_action' => 'csf_chosen_ajax_nonce',
      'nonce_field' => 'nonce',
      'capability_callback' => function () use ( $capability ) {
        return current_user_can( $capability );
      },
      'rate_limit' => 60,
      'rate_window' => 60,
    ) ) ) {
      return;
    }

    if ( empty( $type ) || empty( $term ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid term ID.', 'sakurairo_csf' ) ) );
    }

    // Success
    $options = CSF_Fields::field_data( $type, $term, $query );

    wp_send_json_success( $options );

  }
  add_action( 'wp_ajax_csf-chosen', 'csf_chosen_ajax' );
}
