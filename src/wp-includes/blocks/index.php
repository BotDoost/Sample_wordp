<?php
/**
 * Used to set up all core blocks used with the block editor.
 *
 * @package WordPress
 */

define( 'BLOCKS_PATH', ABSPATH . WPINC . '/blocks/' );

// Include files required for core blocks registration.
require BLOCKS_PATH . 'legacy-widget.php';
require BLOCKS_PATH . 'widget-group.php';
require BLOCKS_PATH . 'require-dynamic-blocks.php';

/**
 * Registers core block style handles.
 *
 * While {@see register_block_style_handle()} is typically used for that, the way it is
 * implemented is inefficient for core block styles. Registering those style handles here
 * avoids unnecessary logic and filesystem lookups in the other function.
 *
 * @since 6.3.0
 */
function register_core_block_style_handles() {
	if ( ! wp_should_load_separate_core_block_assets() ) {
		return;
	}

	static $core_blocks_meta;
	if ( ! $core_blocks_meta ) {
		$core_blocks_meta = require ABSPATH . WPINC . '/blocks/blocks-json.php';
	}

	$includes_url   = includes_url();
	$suffix         = wp_scripts_get_suffix();
	$wp_styles      = wp_styles();
	$style_fields   = array(
		'style'       => 'style',
		'editorStyle' => 'editor',
	);
	$transient_name = 'block_styles__';
	$files          = get_transient( $transient_name );
	if ( ! $files ) {
		$files = glob( __DIR__ . "/**/**{$suffix}.css" );
		set_transient( $transient_name, $files );
	}

	$callback = static function( $name, $filename, $style_handle ) use ( $files, $suffix, $wp_styles, $includes_url ) {
		$style_path = "blocks/{$name}/{$filename}{$suffix}.css";
		$path       = ABSPATH . WPINC . '/' . $style_path;

		if ( ! in_array( $path, $files, true ) ) {
			$wp_styles->add(
				$style_handle,
				false
			);
		} else {
			$wp_styles->add( $style_handle, $includes_url . $style_path );
			$wp_styles->add_data( $style_handle, 'path', $path );

			$rtl_file = str_replace( "{$suffix}.css", "-rtl{$suffix}.css", $path );
			if ( is_rtl() && in_array( $rtl_file, $files, true ) ) {
				$wp_styles->add_data( $style_handle, 'rtl', 'replace' );
				$wp_styles->add_data( $style_handle, 'suffix', $suffix );
				$wp_styles->add_data( $style_handle, 'path', $rtl_file );
			}
		}
	};

	$supports_blocks_styles = current_theme_supports( 'wp-block-styles' );

	foreach ( $core_blocks_meta as $name => $schema ) {
		/** This filter is documented in wp-includes/blocks.php */
		$schema = apply_filters( 'block_type_metadata', $schema );

		// Backfill these properties similar to `register_block_type_from_metadata()`.
		if ( ! isset( $schema['style'] ) ) {
			$schema['style'] = "wp-block-$name";
		}
		if ( ! isset( $schema['editorStyle'] ) ) {
			$schema['editorStyle'] = "wp-block-{$name}-editor";
		}

		if ( $supports_blocks_styles ) {
			$callback( $name, 'theme', "wp-block-{$name}-theme" );
		}
		foreach ( $style_fields as $style_field => $filename ) {
			$style_handle = $schema[ $style_field ];
			if ( is_array( $style_handle ) ) {
				continue;
			}

			$callback( $name, $filename, $style_handle );
		}
	}
}
add_action( 'init', 'register_core_block_style_handles', 9 );

/**
 * Registers core block types using metadata files.
 * Dynamic core blocks are registered separately.
 *
 * @since 5.5.0
 */
function register_core_block_types_from_metadata() {
	$block_folders = require BLOCKS_PATH . 'require-static-blocks.php';
	foreach ( $block_folders as $block_folder ) {
		register_block_type_from_metadata(
			BLOCKS_PATH . $block_folder
		);
	}
}
add_action( 'init', 'register_core_block_types_from_metadata' );
