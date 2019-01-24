<?php

/**
 * @group rewrite
 */
class Tests_Rewrite_Tags extends WP_UnitTestCase {
	protected $rewritecode;
	protected $rewritereplace;
	protected $queryreplace;
	protected $wp_rewrite;

	public function setUp() {
		global $wp_rewrite;
		$this->wp_rewrite = $wp_rewrite;
		$wp_rewrite       = new WP_Rewrite();
		$wp_rewrite->init();

		$this->rewritecode    = $wp_rewrite->rewritecode;
		$this->rewritereplace = $wp_rewrite->rewritereplace;
		$this->queryreplace   = $wp_rewrite->queryreplace;
	}

	public function tearDown() {
		global $wp_rewrite;
		$wp_rewrite = $this->wp_rewrite;
	}

	public function _invalid_rewrite_tags() {
		return array(
			array( 'foo', 'bar' ),
			array( '%', 'bar' ),
			array( '%a', 'bar' ),
			array( 'a%', 'bar' ),
			array( '%%', 'bar' ),
			array( '', 'bar' ),
		);
	}

	/**
	 * @dataProvider _invalid_rewrite_tags
	 *
	 * @param string $tag   Rewrite tag.
	 * @param string $regex Regex.
	 */
	public function test_add_rewrite_tag_invalid( $tag, $regex ) {
		global $wp_rewrite;

		add_rewrite_tag( $tag, $regex );
		$this->assertEqualSets( $this->rewritecode, $wp_rewrite->rewritecode );
		$this->assertEqualSets( $this->rewritereplace, $wp_rewrite->rewritereplace );
		$this->assertEqualSets( $this->queryreplace, $wp_rewrite->queryreplace );
	}

	public function test_add_rewrite_tag_empty_query() {
		global $wp_rewrite;

		$rewritecode   = $wp_rewrite->rewritecode;
		$rewritecode[] = '%foo%';
		add_rewrite_tag( '%foo%', 'bar' );

		$this->assertEqualSets( $rewritecode, $wp_rewrite->rewritecode );
		$this->assertEqualSets( array_merge( $this->rewritereplace, array( 'bar' ) ), $wp_rewrite->rewritereplace );
		$this->assertEqualSets( array_merge( $this->queryreplace, array( 'foo=' ) ), $wp_rewrite->queryreplace );
	}

	public function test_add_rewrite_tag_custom_query() {
		global $wp_rewrite;

		$rewritecode   = $wp_rewrite->rewritecode;
		$rewritecode[] = '%foo%';
		add_rewrite_tag( '%foo%', 'bar', 'baz=' );

		$this->assertEqualSets( $rewritecode, $wp_rewrite->rewritecode );
		$this->assertEqualSets( array_merge( $this->rewritereplace, array( 'bar' ) ), $wp_rewrite->rewritereplace );
		$this->assertEqualSets( array_merge( $this->queryreplace, array( 'baz=' ) ), $wp_rewrite->queryreplace );
	}

	public function test_add_rewrite_tag_updates_existing() {
		global $wp_rewrite;

		add_rewrite_tag( '%pagename%', 'foo', 'bar=' );
		$this->assertContains( '%pagename%', $wp_rewrite->rewritecode );
		$this->assertContains( 'foo', $wp_rewrite->rewritereplace );
		$this->assertContains( 'bar=', $wp_rewrite->queryreplace );
		$this->assertNotContains( 'pagename=', $wp_rewrite->queryreplace );
	}
}
