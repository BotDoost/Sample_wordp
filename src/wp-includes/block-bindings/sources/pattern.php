<?php
/**
 * The "pattern" source for the Block Bindings API. This source is used by the
 * Partially Synced Patterns.
 *
 * @since 6.5.0
 * @package WordPress
 */
function pattern_source_callback( $source_attrs, $block_instance, $attribute_name ) {
	if ( ! _wp_array_get( $block_instance->attributes, array( 'metadata', 'id' ), false ) ) {
		return null;
	}
	$block_id = $block_instance->attributes['metadata']['id'];
	return _wp_array_get( $block_instance->context, array( 'pattern/overrides', $block_id, $attribute_name ), null );
}

register_block_bindings_source(
	'core/pattern-attributes',
	array(
		'label'              => __( 'Pattern Attributes' ),
		'get_value_callback' => 'pattern_source_callback',
	)
);
