<?php

/**
 * Admin Ajax functions to be tested.
 */
require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';

/**
 * Testing Add Meta AJAX functionality.
 *
 * @group ajax
 */
class Tests_Ajax_AddMeta extends WP_Ajax_UnitTestCase {
	/**
	 * @ticket 43559
	 *
	 * @covers ::add_post_meta
	 * @covers ::wp_ajax_add_meta
	 */
	public function test_post_add_meta_empty_is_allowed_ajax() {
		$post = self::factory()->post->create();

		// Become an administrator.
		$this->_setRole( 'administrator' );

		$_POST = array(
			'post_id'              => $post,
			'metakeyinput'         => 'testkey',
			'metavalue'            => '',
			'_ajax_nonce-add-meta' => wp_create_nonce( 'add-meta' ),
		);

		// Make the request.
		try {
			$this->_handleAjax( 'add-meta' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$this->assertSame( '', get_post_meta( $post, 'testkey', true ) );
	}

	/**
	 * @ticket 43559
	 *
	 * @covers ::update_metadata_by_mid
	 * @covers ::wp_ajax_add_meta
	 */
	public function test_update_metadata_by_mid_allows_empty_values_ajax() {
		$post = self::factory()->post->create();

		$meta_id = add_post_meta( $post, 'testkey', 'hello' );

		// Become an administrator.
		$this->_setRole( 'administrator' );

		$_POST = array(
			'_ajax_nonce-add-meta' => wp_create_nonce( 'add-meta' ),
			'post_id'              => $post,
			'meta'                 => array(
				$meta_id => array(
					'key'   => 'testkey',
					'value' => '',
				),
			),
		);

		// Make the request.
		try {
			$this->_handleAjax( 'add-meta' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$this->assertSame( '', get_post_meta( $post, 'testkey', true ) );
	}
}
