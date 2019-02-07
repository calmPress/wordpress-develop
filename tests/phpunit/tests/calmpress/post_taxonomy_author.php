<?php
/**
 * Unit tests covering Post_Taxonomy_Author functionality
 *
 * @package calmPress
 * @since 1.0.0
 */

use calmpress\post_authors;

class WP_Test_Post_Taxonomy_Author extends WP_UnitTestCase {

	/**
	 * Test the constructor and term_id method.
	 *
	 * @since 1.0.0
	 */
	function test_constructor() {

		// Construct out of correct taxonomy.
		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertEquals( $author1, $author->term_id() );

		// Construct with wrong taxonomy generate error.
		$author1 = wp_insert_term( 'author1', 'category' );
		$author1 = $author1['term_id'];
		$this->setExpectedException('PHPUnit_Framework_Error_Notice');
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );

		// An error is generated but the term is still used as if it was legit.
		$this->assertEquals( $author1, $author->term_id() );
	}

	/**
	 * Test the name method.
	 *
	 * @since 1.0.0
	 */
	function test_name() {
		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertEquals( 'author1', $author->name() );
	}

	/**
	 * Test the slug method.
	 *
	 * @since 1.0.0
	 */
	function test_slug() {
		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertEquals( 'author1', $author->slug() );
	}

	/**
	 * Test the posts_count method.
	 *
	 * @since 1.0.0
	 */
	function test_posts_count() {
		$user = $this->factory->user->create( [ 'name' => 'test', 'display_name' => 'display name', 'description' => 'test description' ] );

		$post1 = $this->factory->post->create( [
			'post_title' => 'test1',
			'post_author' => $user,
			'post_status' => 'publish',
		] );

		$post2 = $this->factory->post->create( [
			'post_title' => 'test2',
			'post_author' => $user,
			'post_status' => 'publish',
		] );

		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];

		wp_set_object_terms( $post1, $author1, post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME, true );
		wp_set_object_terms( $post2, $author1, post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME, true );

		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertEquals( 2, $author->posts_count() );
	}

	/**
	 * Test the image method.
	 *
	 * @since 1.0.0
	 */
	function test_image() {

		// Test no image associated.
		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertNull( $author->image() );

		// Test when a junk value.
		update_term_meta( $author1, 'calm_featured_image', 999999);
		$this->assertNull( $author->image() );

		// Test with actually existing image.
		$file = DIR_TESTDATA . '/images/canola.jpg';
		$attachment_id = $this->factory->attachment->create_upload_object( $file, 0 );
		update_term_meta( $author1, 'calm_featured_image', $attachment_id);
		$this->assertEquals( $attachment_id, $author->image()->ID );

	}

	/**
	 * Test the posts_url method.
	 *
	 * @since 1.0.0
	 */
	function test_posts_url() {
		$author1 = wp_insert_term( 'author1', post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME );
		$author1 = $author1['term_id'];
		$author = new post_authors\Post_Taxonomy_Author( get_term( $author1 ) );
		$this->assertEquals( get_term_link( $author1, post_authors\Post_Authors_As_Taxonomy::TAXONOMY_NAME ), $author->posts_url() );
	}

}
