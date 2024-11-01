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
add_theme_support('html5', ['script', 'style']);


add_filter('style_loader_tag', 'sj_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'sj_remove_type_attr', 10, 2);
add_filter('wp_print_footer_scripts ', 'sj_remove_type_attr', 10, 2);
function sj_remove_type_attr($tag) {
	return str_replace('/>', '>', $tag);
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
	// $out = str_replace('?paged=', '', $out);

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
	if (is_null($result) && !current_user_can('manage_options')) {
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

add_filter('excerpt_more', fn() => '...');

// add_action('phpmailer_init', 'smtp_phpmailer_init');
// function smtp_phpmailer_init($phpmailer) {
// 	$phpmailer->IsSMTP();
// 	$phpmailer->CharSet    = 'UTF-8';
// 	$phpmailer->Host       = 'ssl://smtp.mail.ru';
// 	$phpmailer->Username   = 'mail@site.ru';
// 	$phpmailer->Password   = 'pas';
// 	$phpmailer->SMTPAuth   = true;
// 	$phpmailer->SMTPSecure = 'ssl';
// 	$phpmailer->Port       = 465;
// 	$phpmailer->From       = 'mail@site.ru';
// 	$phpmailer->FromName   = 'site.ru';
// 	$phpmailer->isHTML(true);
// }


// редирект со страницы автора
add_action('template_redirect', function () {
	if (is_author()) {
		wp_redirect(home_url());
		exit;
	}
});


// новые пользователи наверху в админке
add_action('users_list_table_query_args', function ($args) {
	$args['orderby'] = empty($_REQUEST['orderby']) ? 'registered' : $_REQUEST['orderby'];
	$args['order'] = empty($_REQUEST['order']) ? 'DESC' : $_REQUEST['order'];
	return $args;
});

// отклчить xmlrpc
add_filter('xmlrpc_enabled', '__return_false');
if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) exit;


// очистить логин если правильный в login
add_action('login_footer', function () {
?>
	<script>
		if (document.getElementById('login_error')) {
			document.getElementById('user_login').value = '';
		}
	</script>
<?php
});
add_filter('login_errors', function () {
	return "Ошибка";
});


function cleanPostArr($arr) {
	foreach ($arr as $key => $val) {
		if (is_array($arr[$key])) {
			foreach ($arr[$key] as $k => $v) {
				$arr[$key][$k] = cleanInputs($v);
			}
		} else {
			$arr[$key] = cleanInputs($val);
		}
	}
	return $arr;
}

function cleanInputs($value = '') {
	$value = stripslashes($value);
	$value = strip_tags($value);
	$value = htmlspecialchars($value);
	$value = trim($value);
	return $value;
}

function getCaptcha($field) {
	$secret_key = 'key';
	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $field);
	$return = json_decode($response);
	return $return;
}

add_action('init', 'toLowerUrls');
function toLowerUrls() {
	$url = $_SERVER['REQUEST_URI'];
	$params = $_SERVER['QUERY_STRING'];
	if (preg_match('/[\.]/', $url)) {
		return;
	}
	if (preg_match('/[A-Z]/', $url)) {
		$lc_url = empty($params)
			? strtolower($url)
			: strtolower(substr($url, 0, strrpos($url, '?'))) . '?' . $params;
		if ($lc_url !== $url) {
			header('Location: ' . $lc_url, TRUE, 301);
			exit();
		}
	}
}
