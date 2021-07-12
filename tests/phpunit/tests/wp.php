<?php

/**
 * @group wp
 */
class Tests_WP extends WP_UnitTestCase {
	/**
	 * @var WP
	 */
	protected $wp;

	public function setUp() {
		parent::setUp();
		$this->wp = new WP();
	}

	/**
	 * @covers WP::add_query_var
	 */
	public function test_add_query_var() {
		$public_qv_count = count( $this->wp->public_query_vars );

		$this->wp->add_query_var( 'test' );
		$this->wp->add_query_var( 'test2' );
		$this->wp->add_query_var( 'test' );

		$this->assertCount( $public_qv_count + 2, $this->wp->public_query_vars );
		$this->assertContains( 'test', $this->wp->public_query_vars );
		$this->assertContains( 'test2', $this->wp->public_query_vars );
	}

	/**
	 * @covers WP::remove_query_var
	 */
	public function test_remove_query_var() {
		$public_qv_count = count( $this->wp->public_query_vars );

		$this->wp->add_query_var( 'test' );
		$this->assertContains( 'test', $this->wp->public_query_vars );
		$this->wp->remove_query_var( 'test' );

		$this->assertCount( $public_qv_count, $this->wp->public_query_vars );
	}
}
