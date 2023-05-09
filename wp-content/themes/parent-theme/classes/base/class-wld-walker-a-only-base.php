<?php

class WLD_Walker_A_Only_Base extends Walker_Nav_Menu {
	public bool $first = true;

	public function start_lvl( &$output, $depth = 0, $args = null ) : void {
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) : void {
	}

	/** @noinspection DuplicatedCode */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) : void {
		$item_output    = '';
		$classes        = empty( $data_object->classes ) ? array() : (array) $data_object->classes;
		$classes[]      = 'menu-item-' . $data_object->ID;
		$args           = apply_filters( 'nav_menu_item_args', $args, $data_object, $depth );
		$class_names    = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $data_object, $args, $depth ) );
		$class_names    = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$item_id        = apply_filters( 'nav_menu_item_id', 'menu-item-' . $data_object->ID, $data_object, $args, $depth );
		$attr_id        = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';
		$atts           = array();
		$atts['title']  = ! empty( $data_object->attr_title ) ? $data_object->attr_title : '';
		$atts['target'] = ! empty( $data_object->target ) ? $data_object->target : '';
		$atts['rel']    = ! empty( $data_object->xfn ) ? $data_object->xfn : '';
		$atts['href']   = ! empty( $data_object->url ) ? $data_object->url : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $data_object, $args, $depth );
		$attributes     = '';
		foreach ( (array) $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				if ( 'href' === $attr ) {
					$value = esc_url( $value );
				} else {
					$value = esc_attr( $value );
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		/** @noinspection PhpUndefinedFieldInspection */
		$title  = apply_filters( 'the_title', $data_object->title, $data_object->ID );
		$title  = apply_filters( 'nav_menu_item_title', $title, $data_object, $args, $depth );
		$before = '';
		if ( isset( $args->empty_first_before ) && $args->empty_first_before && $this->first ) {
			$this->first = false;
		} else {
			$before = $args->before;
		}
		$item_output .= $before;
		$item_output .= '<a' . $attributes . $attr_id . $class_names . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a> ';
		$item_output .= $args->after;
		$output      .= apply_filters( 'walker_nav_menu_start_el', $item_output, $data_object, $depth, $args );
	}

	public function end_el( &$output, $data_object, $depth = 0, $args = null ) : void {
	}
}
