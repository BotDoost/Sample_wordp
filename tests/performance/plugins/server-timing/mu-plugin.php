<?php

add_action(
	'template_redirect',
	function() {

		global $server_timing_values, $timestart, $template_start;

		if ( ! is_array( $server_timing_values ) ) {
			$server_timing_values = array();
		}

		$template_start = microtime( true );
		$server_timing_values['before-template'] = $template_start - $timestart;

		ob_start();

		add_action(
			'shutdown',
			function() {

				global $server_timing_values, $timestart, $template_start;

				$output = ob_get_clean();

				$server_timing_values['template'] = microtime( true ) - $template_start;

				$server_timing_values['total'] = $server_timing_values['before-template'] + $server_timing_values['template'];

				$header_values = array();
				foreach ( $server_timing_values as $slug => $value ) {
					if ( is_float( $value ) ) {
						$value = round( $value * 1000.0, 2 );
					}
					$header_values[] = sprintf( 'wp-%1$s;dur=%2$s', $slug, $value );
				}
				header( 'Server-Timing: ' . implode( ', ', $header_values ) );

				echo $output;
			},
			-9999
		);
	},
	PHP_INT_MAX
);
