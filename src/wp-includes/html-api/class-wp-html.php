<?php

class WP_HTML {
	public static function tag( $tag_name, $attributes = null, $inner_text = '' ) {
		return WP_HTML::tag_with_inner_html( $tag_name, $attributes, esc_html( $inner_text ) );
	}

	public static function tag_with_inner_html( $tag_name, $attributes = null, $inner_html = '' ) {
		$is_void = WP_HTML_Spec::is_void_element( $tag_name );
		$html = $is_void ? "<{$tag_name}>" : "<{$tag_name}>{$inner_html}</{$tag_name}>";

		$p = new WP_HTML_Tag_Processor( $html );

		if ( is_array( $attributes ) ) {
			$p->next_tag();
			foreach ( $attributes as $name => $value ) {
				$p->set_attribute( $name, $value );
			}
		}

		return $p->get_updated_html();
	}
}
