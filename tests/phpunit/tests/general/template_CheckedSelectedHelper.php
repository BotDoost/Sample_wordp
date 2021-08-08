<?php
/**
 * A set of unit tests for the __checked_selected_helper() and associated functions in wp-includes/general-template.php.
 *
 * @group general
 */

class Tests_General_Template_CheckedSelectedHelper extends WP_UnitTestCase {

	/**
	 * Tests that the return value for selected() is as expected with equal values.
	 *
	 * @covers ::selected
	 */
	public function test_selected_with_equal_values() {
		$this->assertSame( " selected='selected'", selected( 'foo', 'foo', false ) );
	}

	/**
	 * Tests that the return value for checked() is as expected with equal values.
	 *
	 * @covers ::checked
	 */
	public function test_checked_with_equal_values() {
		$this->assertSame( " checked='checked'", checked( 'foo', 'foo', false ) );
	}

	/**
	 * Tests that the return value for disabled() is as expected with equal values.
	 *
	 * @covers ::disabled
	 */
	public function test_disabled_with_equal_values() {
		$this->assertSame( " disabled='disabled'", disabled( 'foo', 'foo', false ) );
	}

	/**
	 * Tests that the return value for readonly() is as expected with equal values.
	 *
	 * @covers ::readonly
	 */
	public function test_readonly_with_equal_values() {
		if ( ! function_exists( 'readonly' ) ) {
			$this->markTestSkipped( 'readonly() function is not available on PHP 8.1' );
		}

		$this->setExpectedDeprecated( 'readonly' );

		// Call the function via a variable to prevent a parse error for this file on PHP 8.1.
		$fn = 'readonly';
		$this->assertSame( " readonly='readonly'", $fn( 'foo', 'foo', false ) );
	}

	/**
	 * Tests that the return value for wp_readonly() is as expected with equal values.
	 *
	 * @covers ::wp_readonly
	 */
	public function test_wp_readonly_with_equal_values() {
		$this->assertSame( " readonly='readonly'", wp_readonly( 'foo', 'foo', false ) );
	}

	/**
	 * @ticket 9862
	 * @ticket 51166
	 *
	 * @dataProvider data_equal_values
	 *
	 * @covers ::__checked_selected_helper
	 *
	 * @param mixed $helper  One of the values to compare.
	 * @param mixed $current The other value to compare.
	 */
	public function test_checked_selected_helper_with_equal_values( $helper, $current ) {
		$this->assertSame( " test='test'", __checked_selected_helper( $helper, $current, false, 'test' ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_equal_values() {
		return array(
			'same value, "foo"; 1: string; 2: string'     => array( 'foo', 'foo' ),
			'same value, 1; 1: string; 2: int'            => array( '1', 1 ),
			'same value, 1; 1: string; 2: bool true'      => array( '1', true ),
			'same value, 1; 1: int; 2: int'               => array( 1, 1 ),
			'same value, 1; 1: int; 2: bool true'         => array( 1, true ),
			'same value, 1; 1: bool true; 2: bool true'   => array( true, true ),
			'same value, 0; 1: string; 2: int'            => array( '0', 0 ),
			'same value, 0; 1: int; 2: int'               => array( 0, 0 ),
			'same value, 0; 1: empty string; 2: bool false' => array( '', false ),
			'same value, 0; 1: bool false; 2: bool false' => array( false, false ),
		);
	}

	/**
	 * @ticket 9862
	 * @ticket 51166
	 *
	 * @dataProvider data_non_equal_values
	 *
	 * @covers ::__checked_selected_helper
	 *
	 * @param mixed $helper  One of the values to compare.
	 * @param mixed $current The other value to compare.
	 */
	public function test_checked_selected_helper_with_non_equal_values( $helper, $current ) {
		$this->assertSame( '', __checked_selected_helper( $helper, $current, false, 'test' ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_non_equal_values() {
		return array(
			'1: string 0; 2: empty string' => array( '0', '' ),
			'1: int 0; 2: empty string'    => array( 0, '' ),
			'1: int 0; 2: bool false'      => array( 0, false ),
		);
	}
}
