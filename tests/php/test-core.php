<?php
/**
 * Test core plugin functionality
 *
 * @package safe-redirect-manager
 */

class SRMTestCore extends WP_UnitTestCase {

	/**
	 * Test root redirect
	 *
	 * @since 1.7.3
	 */
	public function testRootRedirect() {
		$_SERVER['REQUEST_URI'] = '/';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Test redirect with cases
	 *
	 * @since 1.7.4
	 */
	public function testCaseInsensitiveRedirect() {
		$_SERVER['REQUEST_URI'] = '/ONE';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		$_SERVER['REQUEST_URI'] = '/one';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/ONE/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Try a redirect after filtering case sensitivity
	 *
	 * @since 1.7.4
	 */
	public function testCaseSensitiveRedirect() {
		$_SERVER['REQUEST_URI'] = '/ONE';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one/', $redirect_to );

		add_filter(
			'srm_case_insensitive_redirects', function( $value ) {
				return false;
			}, 10, 1
		);

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertFalse( $redirected );
	}

	/**
	 * Test case sensitive redirect to
	 *
	 * @since 1.7.4
	 */
	public function testCaseSensitiveRedirectTo() {
		$_SERVER['REQUEST_URI'] = '/ONE';
		$redirected             = false;
		$redirect_to            = '/goHERE';
		srm_create_redirect( '/one/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Test basic wildcards
	 *
	 * @since 1.7.4
	 */
	public function testBasicWildcard() {
		$_SERVER['REQUEST_URI'] = '/one/dfsdf';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one*', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Test replace wildcards
	 *
	 * @since 1.7.4
	 */
	public function testReplaceWildcard() {
		$_SERVER['REQUEST_URI'] = '/one/two';
		$redirected             = false;
		$redirect_to            = '/gohere/*';
		srm_create_redirect( '/one/*', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === '/gohere/two' ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Test lots of permutations of URL trailing slashes with and without regex
	 *
	 * @since 1.7.3
	 */
	public function testTrailingSlashes() {
		/**
		 * First without regex
		 */

		$_SERVER['REQUEST_URI'] = '/one';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		$_SERVER['REQUEST_URI'] = '/one/';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		$_SERVER['REQUEST_URI'] = '/one/two';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one/two/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		$_SERVER['REQUEST_URI'] = '/one/two/';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/one/two', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		/**
		 * Now with regex
		 */

		$_SERVER['REQUEST_URI'] = '/one/two';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/.*/', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		$_SERVER['REQUEST_URI'] = '/one/two/';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/.*', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Test some simple redirections
	 *
	 * @since 1.7.3
	 */
	public function testSimplePath() {
		$_SERVER['REQUEST_URI'] = '/test';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/test', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		/**
		 * Test longer path with no trailing slash
		 */

		$_SERVER['REQUEST_URI'] = '/test/this/path';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/test/this/path/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		/**
		 * Test a redirect miss
		 */

		$_SERVER['REQUEST_URI'] = '/test/wrong/path';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/test/right/path/', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( ! $redirected );
	}

	/**
	 * Test regex redirections
	 *
	 * @since 1.7.3
	 */
	public function testSimplePathRegex() {
		$_SERVER['REQUEST_URI'] = '/tet/555/path/sdfsfsdf';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/tes?t/[0-9]+/path/[^/]+/?', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		/**
		 * Test regex replacement
		 */

		$_SERVER['REQUEST_URI'] = '/well/everything-else/strip';
		$redirected             = false;
		$redirect_to            = '/$1';
		srm_create_redirect( '/([a-z]+)/.*', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === '/well' ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		/**
		 * Test regex miss
		 */

		$_SERVER['REQUEST_URI'] = '/another/test';
		$redirected             = false;
		$redirect_to            = '/gohere';
		srm_create_redirect( '/[0-9]+', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( ! $redirected );
	}

	/**
	 * Test that replace (both wildcard and regex) doesn't change the casing on the matched part
	 *
	 * @since 1.7.5
	 */
	public function testReplaceCasing() {
		// with wildcard
		$_SERVER['REQUEST_URI'] = '/myfiles1/FooBar.JPEG';
		$redirected             = false;
		$redirect_to            = '/images1/*';
		srm_create_redirect( '/myfiles1/*', $redirect_to );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === '/images1/FooBar.JPEG' ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );

		// with regex
		$_SERVER['REQUEST_URI'] = '/myfiles2/FooBar.JPEG';
		$redirected             = false;
		$redirect_to            = '/images2/$1';
		srm_create_redirect( '/myfiles2/(.*\.jpe?g)', $redirect_to, 301, true );

		add_action(
			'srm_do_redirect', function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === '/images2/FooBar.JPEG' ) {
					$redirected = true;
				}
			}, 10, 3
		);

		SRM_Redirect::factory()->maybe_redirect();

		$this->assertTrue( $redirected );
	}

	/**
	 * Tests import redirects from file.
	 *
	 * @since 1.7.6
	 *
	 * @access public
	 */
	public function testFileImport() {
		// create temp file and fill up it with redirects
		$tmp_file = tmpfile();

		$redirects = array(
			// headers
			array( 'http code', 'legacy url', 'new url', 'is_regex', 'order', 'notes' ),
			// redirects
			array( 302, '/some-url', '/new-url', 0, 0, 'Note about new URL' ),
			array( 301, '/broken-url', '/fixed-url', 0, 0, 'Note about fixed url' ),
			array( 301, '/reg?ex/\d+/path', '/go/here', 1, 0, 'Note about regex url' ),
		);

		foreach ( $redirects as $row ) {
			fputcsv( $tmp_file, $row );
		}

		// let's import it
		fseek( $tmp_file, 0 );
		$processed = srm_import_file(
			$tmp_file, array(
				'source' => 'legacy url',
				'target' => 'new url',
				'regex'  => 'is_regex',
				'code'   => 'http code',
				'order'  => 'order',
				'notes'  => 'notes',
			)
		);

		// assert results
		$this->assertTrue( is_array( $processed ) && ! empty( $processed['created'] ) );
		$this->assertEquals( count( $redirects ) - 1, $processed['created'] );

		// close temp file
		fclose( $tmp_file );
	}

	/**
	 * Test a redirect rule that ends with a trailing slash followed by an asterisk.
	 *
	 * @since 1.9.3
	 */
	public function testWildcardRedirectWithSlash() {
		$_SERVER['REQUEST_URI'] = '/one/';
		$redirected			    = false;
		$redirect_to			= '/gohere/';

		// Create two redirects for testing.
		srm_create_redirect( '/one/*', $redirect_to );
		srm_create_redirect( '/two/', $redirect_to );
		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected ) {
				if ( $redirected_to === $redirect_to ) {
					$redirected = true;
				}
			},
			10,
			3
		);
		SRM_Redirect::factory()->maybe_redirect();
		$this->assertTrue( $redirected, 'Expected that /one/ would redirect to /gohere/' );
	}

	/**
	 * Test that the query params are attached to the new redirect.
	 */
	public function testWildcardRedirectWithQueryParams() {
		$_SERVER['REQUEST_URI'] = '/one/?test=true';
		$redirected             = false;
		$redirect_to            = '/gohere/*';

		// Create two redirects for testing.
		srm_create_redirect( '/one/*', $redirect_to );
		srm_create_redirect( '/two/', $redirect_to );
		$expected = '/gohere/?test=true';
		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected, &$expected ) {
				if ( $redirected_to === '/gohere/?test=true' ) {
					$redirected = true;
				}
				$expected = $redirect_to;
			},
			10,
			3
		);
		SRM_Redirect::factory()->maybe_redirect();
		$this->assertTrue( $redirected, 'Expected that /one/?test=true would redirect to /gohere/?test=true but instead redirected to ' . $redirect_to );
	}

	/**
	 * Test a redirect rule with a wildcard that shouldn't match.
	 */
	public function testNoRedirectWildcard() {
		$_SERVER['REQUEST_URI'] = '/one-page/';
		$redirected             = false;
		$redirect_to            = '/gohere';

		// Create redirect for testing.
		srm_create_redirect( '/one/*', $redirect_to );
		add_action(
			'srm_do_redirect',
			function() use ( &$redirected ) {
				$redirected = true;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertFalse( $redirected, 'Expected that /one-page/ would not redirect, but instead redirected to ' . $redirect_to );
	}

	/**
	 * Test a URL that shouldn't redirect.
	 */
	public function testNoRedirect() {
		$_SERVER['REQUEST_URI'] = '/noredirect/';
		$redirected             = false;
		$redirect_to            = '/gohere/*';

		// Create two redirects for testing.
		srm_create_redirect( '/one/*', $redirect_to );
		srm_create_redirect( '/two/', $redirect_to );
		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected, &$expected ) {
					$redirected = true;
			},
			10,
			3
		);
		SRM_Redirect::factory()->maybe_redirect();
		$this->assertFalse( $redirected, 'Expected that /noredirect/ would not redirect, but instead redirected to ' . $redirect_to );
	}

	/**
	 * Tests the match redirect function
	 *
	 */
	public function testMatchRedirect() {
		$redirect_to            = '/gohere';
		srm_create_redirect( '/', $redirect_to );

		$matched_redirect = srm_match_redirect( '/' );

		$this->assertTrue( $matched_redirect['redirect_to'] === $redirect_to );

		srm_create_redirect( '/one-page', $redirect_to );

		$matched_redirect = srm_match_redirect( '/one-page' );

		$this->assertTrue( $matched_redirect['redirect_to'] === $redirect_to );
	}

	/**
	 * Test that the query params are attached to the new redirect.
	 */
	public function testRedirectWithQueryParams() {
		$_SERVER['REQUEST_URI'] = '/one/?test=true';
		$redirected             = false;
		$redirect_to            = '/gohere/';

		srm_create_redirect( '/one/', $redirect_to );
		$expected = '/gohere/?test=true';
		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected, &$expected ) {
				if ( $redirected_to === '/gohere/?test=true' ) {
					$redirected = true;
				}
				$expected = $redirect_to;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertTrue( $redirected, 'Expected that /one/?test=true would redirect to /gohere/?test=true but instead redirected to ' . $redirect_to );
	}

	/**
	 * Test that the query params are attached to the new redirect.
	 */
	public function testMatchQueryParamsFilter() {
		$_SERVER['REQUEST_URI'] = '/one/?test=true';
		$redirected             = false;
		$redirect_to            = '/gohere/';

		add_filter('srm_match_query_params', '__return_true');

		srm_create_redirect( '/one/', $redirect_to );
		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$redirect_to, &$redirected, &$expected ) {
				$redirected = true;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertFalse( $redirected, 'Expected that /noredirect/ would not redirect, but instead redirected to ' . $redirect_to );

		remove_filter('srm_match_query_params', '__return_true');
	}

	/**
	 * Test that redirection to an external domain works with a regular expression without substitution
	 *
	 * @link https://github.com/10up/safe-redirect-manager/issues/269
	 * @since 2.1.2
	 */
	public function testRedirectToExternalDomainWithNonSubstitutingRegex269() {
		$_SERVER['REQUEST_URI'] = '/be/hhhh';
		$redirect_from          = '(go|be)\/h{0,}$';
		$redirect_to            = 'http://xu-osp-plugins.local/404-regex';
		$status                 = 301;
		$use_regex              = true;

		$expected_redirect = 'http://xu-osp-plugins.local/404-regex';
		$expected_status   = 301;

		$actual_request  = '';
		$actual_redirect = '';
		$actual_status   = 0;

		srm_create_redirect( $redirect_from, $redirect_to, $status, $use_regex );

		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$actual_request, &$actual_redirect, &$actual_status ) {
				$actual_request  = $requested_path;
				$actual_redirect = $redirected_to;
				$actual_status   = $status_code;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertSame( $_SERVER['REQUEST_URI'], $actual_request, 'The requested path does not meet the expectation' );
		$this->assertSame( $expected_redirect, $actual_redirect, 'The redirect destination does not meet the expectation' );
		$this->assertSame( $expected_status, $actual_status, 'The redirect status does npt meet the expectation.' );
	}

	/**
	 * Test that redirection to an external domain works with a regular expression with substitution
	 *
	 * @link https://github.com/10up/safe-redirect-manager/issues/380
	 * @since 2.2.0
	 */
	public function testRedirectToExternalDomainWithSubstitutingRegex380() {
		$_SERVER['REQUEST_URI'] = '/test/1234';
		$redirect_from          = '/test/(.*)';
		$redirect_to            = 'http://example.org/$1';
		$status                 = 301;
		$use_regex              = true;

		$expected_redirect = 'http://example.org/1234';
		$expected_status   = 301;

		$actual_request  = '';
		$actual_redirect = '';
		$actual_status   = 0;

		srm_create_redirect( $redirect_from, $redirect_to, $status, $use_regex );

		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$actual_request, &$actual_redirect, &$actual_status ) {
				$actual_request  = $requested_path;
				$actual_redirect = $redirected_to;
				$actual_status   = $status_code;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertSame( $_SERVER['REQUEST_URI'], $actual_request, 'The requested path does not meet the expectation' );
		$this->assertSame( $expected_redirect, $actual_redirect, 'The redirect destination does not meet the expectation' );
		$this->assertSame( $expected_status, $actual_status, 'The redirect status does npt meet the expectation.' );
	}

	/**
	 * Test that redirection to a local URL works with a regular expression with substitution
	 *
	 * @link https://github.com/10up/safe-redirect-manager/issues/380
	 * @since 2.2.0
	 */
	public function testRedirectToPathWithSubstitutingRegex380() {
		$_SERVER['REQUEST_URI'] = '/test/1234';
		$redirect_from          = '/test/(.*)';
		$redirect_to            = '/result/$1';
		$status                 = 301;
		$use_regex              = true;

		$expected_redirect = '/result/1234';
		$expected_status   = 301;

		$actual_request  = '';
		$actual_redirect = '';
		$actual_status   = 0;

		srm_create_redirect( $redirect_from, $redirect_to, $status, $use_regex );

		add_action(
			'srm_do_redirect',
			function( $requested_path, $redirected_to, $status_code ) use ( &$actual_request, &$actual_redirect, &$actual_status ) {
				$actual_request  = $requested_path;
				$actual_redirect = $redirected_to;
				$actual_status   = $status_code;
			},
			10,
			3
		);

		SRM_Redirect::factory()->maybe_redirect();
		$this->assertSame( $_SERVER['REQUEST_URI'], $actual_request, 'The requested path does not meet the expectation' );
		$this->assertSame( $expected_redirect, $actual_redirect, 'The redirect destination does not meet the expectation' );
		$this->assertSame( $expected_status, $actual_status, 'The redirect status does npt meet the expectation.' );
	}
}
