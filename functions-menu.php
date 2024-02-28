<?php
add_action('after_setup_theme', 'nav_menus');
function nav_menus() {
	add_image_size('blog-item', 370, 225, true);

	register_nav_menus(
		array(
			'menu_main_header' => 'Меню в хедер',
		)
	);
}

class main_nav_menu_Walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		static $i = 1;
		if ($depth == 0) {
			if ($item->current) {
				$output .= '<li class="menu__item"><a href="' . $item->url . '" class="menu__link active"><span class="menu__list-span">' . $i . '.</span>' . $item->title . '</a>';
			} else {
				$output .= '<li class="menu__item"><a href="' . $item->url . '" class="menu__link"><span class="menu__list-span">' . $i . '.</span>' . $item->title . '</a>';
			}
			$i++;
		}
	}
}

class sub_menu_Walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		if ($depth == 0) {
			if ($args->has_children && $item->current) {
				$output .= '<li class="menu__item menu__item_sub active"><a href="' . $item->url . '" class="menu__link">' . $item->title . '</a><span class="menu__item-svg-block"><svg class="menu__item-svg"><use xlink:href="' . get_template_directory_uri() . '/images/sprite.svg#arrow-menu"></use></svg></span>';
			} else if ($args->has_children && $item->url != '#') {
				$output .= '<li class="menu__item menu__item_sub"><a href="' . $item->url . '" class="menu__link">' . $item->title . '</a><span class="menu__item-svg-block"><svg class="menu__item-svg"><use xlink:href="' . get_template_directory_uri() . '/images/sprite.svg#arrow-menu"></use></svg></span>';
			} else if ($args->has_children && $item->url == '#') {
				$output .= '<li class="menu__item"><span class="menu__link">' . $item->title . '</span><span class="menu__item-svg-block"><svg class="menu__item-svg"><use xlink:href="' . get_template_directory_uri() . '/images/sprite.svg#arrow-menu"></use></svg></span>';
			} else if ($item->current) {
				$output .= '<li class="menu__item active"><a href="' . $item->url . '" class="menu__link">' . $item->title . '</a>';
			} else {
				$output .= '<li class="menu__item"><a href="' . $item->url . '" class="menu__link">' . $item->title . '</a>';
			}
		}
		if ($depth == 1) {
			if ($item->current) {
				$output .= '<li class="menu__sub-item"><a href="' . $item->url . '" class="menu__sub-link menu__sub-link_active">' . $item->title . '</a>';
			} else {
				$output .= '<li class="menu__sub-item"><a href="' . $item->url . '" class="menu__sub-link">' . $item->title . '</a>';
			}
		}
	}
	function start_lvl(&$output, $depth = 0, $args = array()) {
		$output .= '<ul class="menu__sub">';
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
		$output .= '</ul>';
	}
	function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
		$id_field = $this->db_fields['id'];
		if (is_object($args[0])) {
			$args[0]->has_children = !empty($children_elements[$element->$id_field]);
		}
		return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}
}
