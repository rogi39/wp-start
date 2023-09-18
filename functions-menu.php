<?php
add_action( 'after_setup_theme', 'nav_menus' );
function nav_menus() {
	add_image_size('blog-item', 370, 225, true);

	register_nav_menus(
		array(
			'menu_main_header' => 'Меню в хедер',
		)
	);
}

class main_nav_menu_Walker extends Walker_Nav_Menu{
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0){
		static $i = 1;
		if ($depth == 0){
			if($item->current){
				$output .= '<li class="menu__item"><a itemprop="url" href="'.$item->url.'" class="menu__link active"><span class="menu__list-span">'.$i.'.</span>'.$item->title.'</a>';
			} else{
				$output .= '<li class="menu__item"><a itemprop="url" href="'.$item->url.'" class="menu__link"><span class="menu__list-span">'.$i.'.</span>'.$item->title.'</a>';
			}
			$i++;
		}
	}
}