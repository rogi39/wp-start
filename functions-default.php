<?php
remove_action('wp_head',             'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles',     'print_emoji_styles');
remove_action('admin_print_styles',  'print_emoji_styles');

remove_action('wp_head', 'wp_resource_hints', 2); //remove dns-prefetch
remove_action('wp_head', 'wp_generator'); //remove meta name="generator"
remove_action('wp_head', 'wlwmanifest_link'); //remove wlwmanifest
remove_action('wp_head', 'rsd_link'); // remove EditURI
remove_action('wp_head', 'rest_output_link_wp_head'); // remove 'https://api.w.org/
remove_action('wp_head', 'rel_canonical'); //remove canonical
remove_action('wp_head', 'wp_shortlink_wp_head', 10); //remove shortlink
remove_action('wp_head', 'wp_oembed_add_discovery_links'); //remove alternate

// add_filter('show_admin_bar', '__return_false');
add_theme_support('post-thumbnails');
add_filter('use_block_editor_for_post', '__return_false', 10);
add_theme_support('title-tag');


add_filter('style_loader_tag', 'sj_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'sj_remove_type_attr', 10, 2);
add_filter('wp_print_footer_scripts ', 'sj_remove_type_attr', 10, 2);
function sj_remove_type_attr($tag) {
	return preg_replace("/type=['\"]text\/(javascript|css)['\"]/", '', $tag);
}

function remove_all_images($sizes) {
	unset($sizes['1536x1536']);
	unset($sizes['2048x2048']);
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_all_images');

//disable generate image pdf
function wpb_disable_pdf_previews() {
	$fallbacksizes = array();
	return $fallbacksizes;
}
add_filter('fallback_intermediate_image_sizes', 'wpb_disable_pdf_previews');



// if( function_exists('acf_add_options_page') ) {
// 	acf_add_options_page(array(
// 		'page_title' 	=> 'Сайт',
// 		'menu_title'	=> 'Сайт',
// 		'menu_slug' 	=> 'site-options',
// 		'capability'	=> 'edit_posts',
// 		'redirect'		=> false,
// 		'icon_url' => 'dashicons-code-standards',
// 	));
// }

add_filter('wp_pagenavi', 'pagenavi_pagination', 10, 2);
function pagenavi_pagination($html) {
	$out = '';
	$out = str_replace('<div', '', $html);
	$out = str_replace('class=\'wp-pagenavi\' role=\'navigation\'>', '', $out);
	$out = str_replace('<a', '<li class="pagination__item"><a class="pagination__link"', $out);
	$out = str_replace('</a>', '</a></li>', $out);
	$out = str_replace('<span aria-current=\'page\' class=\'current\'', '<li class="pagination__item pagination__item_active"><span class="pagination__link"', $out);
	$out = str_replace('<span class=\'extend\'', '<li class="pagination__item"><span class="pagination__link"', $out);
	$out = str_replace('</span>', '</span></li>', $out);
	$out = str_replace('</div>', '', $out);

	return '<ul class="pagination">' . $out . '</ul>';
}

//не резать gif при загрузке
function disable_upload_sizes($sizes, $metadata) {
	$filetype = wp_check_filetype($metadata['file']);
	if ($filetype['type'] == 'image/gif') {
		$sizes = array();
	}
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'disable_upload_sizes', 10, 2);

//разрешить загрузку svg
function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

add_filter('rest_authentication_errors', function ($result) {
	if (!empty($result)) {
		return $result;
	}
	if (!is_user_logged_in()) {
		return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
	}
	return $result;
});

//ограничение редакции поста
function my_revisions_to_keep($revisions) {
	return 5;
}
add_filter('wp_revisions_to_keep', 'my_revisions_to_keep');

add_filter('excerpt_length', function () {
	return 20;
});

add_filter('excerpt_more', fn () => '...');


function cleanInputs($value = '') {
	$value = trim($value);
	$value = stripslashes($value);
	$value = strip_tags($value);
	$value = htmlspecialchars($value);
	return $value;
}

// новые пользователи наверху
add_action('users_list_table_query_args', function ($args) {
	$args['orderby'] = empty($_REQUEST['orderby']) ? 'registered' : $_REQUEST['orderby'];
	$args['order'] = empty($_REQUEST['order']) ? 'DESC' : $_REQUEST['order'];
	return $args;
});
