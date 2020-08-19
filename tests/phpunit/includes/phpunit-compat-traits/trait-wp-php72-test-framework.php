<?php

trait WP_PHP72_Test_Framework {

    /**
     * This method is called before the first test of this test class is run.
	 * 
	 * Note that `wpSetUpBeforeClass()` also exists.
     */
    public static function setUpBeforeClass(): void {
		if ( is_callable( 'static::_setUpBeforeClass' ) ) {
			static::_setUpBeforeClass();
		}
    }

    /**
     * This method is called after the last test of this test class is run.
	 *
	 * Note that `wpTearDownAfterClass()` also exists.
     */
    public static function tearDownAfterClass(): void {
		if ( is_callable( 'static::_tearDownAfterClass' ) ) {
			static::_tearDownAfterClass();
		}
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void {
		if ( method_exists( $this, '_setUp' ) ) {
			$this->_setUp();
		}
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void {
		if ( method_exists( $this, '_tearDown' ) ) {
			$this->_tearDown();
		}
	}

	/**
     * Performs assertions shared by all tests of a test case.
     *
     * This method is called between setUp() and test.
     */
    protected function assertPreConditions(): void {
		if ( method_exists( $this, '_assertPreConditions' ) ) {
			$this->_assertPreConditions();
		}
    }

    /**
     * Performs assertions shared by all tests of a test case.
     *
     * This method is called between test and tearDown().
     */
    protected function assertPostConditions(): void {
		if ( method_exists( $this, '_assertPostConditions' ) ) {
			$this->_assertPostConditions();
		}
    }

}