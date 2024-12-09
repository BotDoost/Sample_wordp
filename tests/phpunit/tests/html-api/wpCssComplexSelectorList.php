<?php
/**
 * Unit tests covering WP_CSS_Complex_Selector_List functionality.
 *
 * @package WordPress
 *
 * @subpackage HTML-API
 *
 * @since TBD
 *
 * @group html-api
 */
class Tests_HtmlApi_WpCssComplexSelectorList extends WP_UnitTestCase {
	private $test_class;

	public function set_up(): void {
		parent::set_up();
		$this->test_class = new class() extends WP_CSS_Complex_Selector_List {
			public function __construct() {
				parent::__construct( array() );
			}

			public static function test_parse_complex_selector( string $input, int &$offset ): ?WP_CSS_Complex_Selector {
				return self::parse_complex_selector( $input, $offset );
			}
		};
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_complex_selector() {
		$input  = 'el1 el2 > .child#bar[baz=quux] , rest';
		$offset = 0;

		/** @var WP_CSS_Complex_Selector|null */
		$sel = $this->test_class::test_parse_complex_selector( $input, $offset );

		$this->assertSame( 2, count( $sel->relative_selectors ) );

		// Relative selectors should be reverse ordered.
		$this->assertSame( 'el2', $sel->relative_selectors[0][0]->ident );
		$this->assertSame( WP_CSS_Complex_Selector::COMBINATOR_CHILD, $sel->relative_selectors[0][1] );

		$this->assertSame( 'el1', $sel->relative_selectors[1][0]->ident );
		$this->assertSame( WP_CSS_Complex_Selector::COMBINATOR_DESCENDANT, $sel->relative_selectors[1][1] );

		$this->assertSame( 3, count( $sel->self_selector->subclass_selectors ) );
		$this->assertNull( $sel->self_selector->type_selector );
		$this->assertSame( 'child', $sel->self_selector->subclass_selectors[0]->ident );

		$this->assertSame( ', rest', substr( $input, $offset ) );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_invalid_complex_selector() {
		$input  = 'el.foo#bar[baz=quux] > , rest';
		$offset = 0;
		$result = $this->test_class::test_parse_complex_selector( $input, $offset );
		$this->assertNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_invalid_complex_selector_nonfinal_subclass() {
		$input  = 'el.foo#bar[baz=quux] > final, rest';
		$offset = 0;
		$result = $this->test_class::test_parse_complex_selector( $input, $offset );
		$this->assertNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_empty_complex_selector() {
		$input  = '';
		$offset = 0;
		$result = $this->test_class::test_parse_complex_selector( $input, $offset );
		$this->assertNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_complex_selector_list() {
		$input  = 'el1 el2 el.foo#bar[baz=quux], second > selector';
		$result = WP_CSS_Complex_Selector_List::from_selectors( $input );
		$this->assertNotNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_invalid_selector_list() {
		$input  = 'el,,';
		$result = WP_CSS_Complex_Selector_List::from_selectors( $input );
		$this->assertNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_invalid_selector_list2() {
		$input  = 'el!';
		$result = WP_CSS_Complex_Selector_List::from_selectors( $input );
		$this->assertNull( $result );
	}

	/**
	 * @ticket 62653
	 */
	public function test_parse_empty_selector_list() {
		$input  = " \t   \t\n\r\f";
		$result = WP_CSS_Complex_Selector_List::from_selectors( $input );
		$this->assertNull( $result );
	}
}
