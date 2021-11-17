<?php

if ( is_multisite() ) :
	/**
	 * Test update_posts_count() get called via filters of WP_Site in multisite.
	 *
	 * @group ms-site
	 * @group multisite
	 *
	 * @covers ::_update_posts_count_on_delete
	 */
	class Tests_update_posts_count_on_delete extends WP_UnitTestCase {

		/**
		 * This test that the posts count is updated correctly when a posts are added and deleted.
		 * @ticket 53443
		 */
		public function test_update_posts_count_on_delete() {

			$blog_id = self::factory()->blog->create();
			switch_to_blog( $blog_id );

			/*
			 * Get the total number of posts, and take into account the "Hello World" blog post automatically created with the blog.
			 */
			$current_post_count = (int) get_option( 'post_count' ) + 1;

			$post_id = self::factory()->post->create(
				array(
					'post_type'   => 'post',
					'post_author' => '1',
					'post_date'   => '2012-10-23 19:34:42',
					'post_status' => 'publish',
				)
			);

			/**
			 * checks the add_action( 'deleted_post', '_update_posts_count_on_delete' ); is called when a post is created.
			 * and _update_posts_count_on_transition_post_status() is called on that filter which then calls update_posts_count to update the count.
			 */
			$this->assertEquals( $current_post_count + 1, (int) get_option( 'post_count' ), 'post added' );

			wp_delete_post( $post_id );

			/**
			 * checks the add_action( 'transition_post_status', '_update_posts_count_on_transition_post_status', 10, 3 ); is called when a post is deleted.
			 * and __update_posts_count_on_delete() is called on that filter which then calls update_posts_count to update the count.
			 */
			$this->assertEquals( $current_post_count, (int) get_option( 'post_count' ), 'post deleted' );

		}
	}


endif;
