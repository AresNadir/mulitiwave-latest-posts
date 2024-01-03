<?php
/**
 * Plugin Name: Latests Posts Shortcode
 * Description: Loads the latests post of the blog with load more button.
 * Version: 1.0
 * Author: Ares Ioakimidis.
 * Author URI: https://nadir.gr
 */

 function my_latest_posts_shortcode($atts) {
	$atts = shortcode_atts(array(
			'posts_per_page' => 2,
	), $atts);

	// Query the latest posts
	$query_args = array(
			'post_type' => 'post',
			'posts_per_page' => $atts['posts_per_page'],
			'no_found_rows' => true,
	);
	$query = new WP_Query($query_args);

	// Initialize output
	$output = '<div id="latest-posts-container">';

	// Loop through posts
	if ($query->have_posts()) {
			while ($query->have_posts()) {
					$query->the_post();
					$output .= '<div class="post-entry_container">';
					$output .= '<h2 class="post_title">' . get_the_title() . '</h2>';
					$output .= '<p class="post_excerpt">' . get_the_excerpt() . '</p>';
					$output .= '
					<a class="read_more_btn" href="' . get_permalink() . '">
						<div class="read-more-btn-container">
							<span class="read_more_btn_text">
								Read More
							</span>
							<span>
								→
							</span>
						</div>
					</a>';
					$output .= '</div>';
			}
			wp_reset_postdata();
	}

	// Load More button
	$output .= '</div>';
	$output .= '
		<a id="load-more-posts" data-page="2" data-per-page="' . $atts['posts_per_page'] . '">
			<div class="load-more-btn-container">
				<span class="load_more_btn_text">
					Load More
				</span>
				<span>
				+
				</span>
			</div>
		</a>';

	return $output;
}
add_shortcode('my_latest_posts', 'my_latest_posts_shortcode');



function load_more_posts() {
	$page = $_POST['page'];
	$per_page = $_POST['per_page'];

	$query_args = array(
			'post_type' => 'post',
			'posts_per_page' => $per_page,
			'paged' => $page,
	);
	$query = new WP_Query($query_args);

	$output = '';
	if ($query->have_posts()) {
			while ($query->have_posts()) {
					$query->the_post();
					$output .= '<div class="post-entry_container post-entry">';
					$output .= '<h2 class="post_title">' . get_the_title() . '</h2>';
					$output .= '<p class="post_excerpt">' . get_the_excerpt() . '</p>';
					$output .= '
					<a class="read_more_btn" href="' . get_permalink() . '">
						<div class="read-more-btn-container">
							<span class="read_more_btn_text">
								Read More
							</span>
							<span>
								→
							</span>
						</div>
					</a>';
					$output .= '</div>';
			}
	}

	// Check if there are more posts
	$more_posts = ($query->max_num_pages > $page);

	wp_reset_postdata();

	// Return the posts and the flag
	echo json_encode(['posts' => $output, 'more_posts' => $more_posts]);
	die();
}


add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');



function my_enqueue_scripts() {
	wp_enqueue_script('my-ajax-handle', plugins_url('mulitiwave-latest-posts/js/load_more_btn.js'), array('jquery'));
	wp_localize_script('my-ajax-handle', 'ajaxurl', admin_url('admin-ajax.php'));
	wp_enqueue_style('my-plugin-style', plugins_url('mulitiwave-latest-posts/css/styles.css'));
}

add_action('wp_enqueue_scripts', 'my_enqueue_scripts');