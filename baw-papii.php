<?php
/*
Plugin Name: BAW Papii
Plugin URI: http://boiteaweb.fr/papii
Description: Get Plugins informations from Officiel repo
Version: 1.6
Author: Julio Potier
Contributors: juliobox
Author URI: http://boiteaweb.fr
License: GPLv3
*/

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

add_shortcode( 'baw_papii', 'bawpapii_sc' );
add_shortcode( 'papii', 'bawpapii_sc' );
function bawpapii_sc( $atts, $new_sc = null )
{
	extract( shortcode_atts( array(
		'plugin' => 'baw-papii-plugins-api-infos', // ego plugin
		'profile' => '', // plugin OR profile	

		'before' => '<ul>', // for profile
		'format' => '<li><a href="%link%" target="_blank">%name%</a>%content%</li>', // for profile
		'after' => '</ul>', // for profile
		'sort' => '0', // for profile

		'info' => 'version', // test purpose, see "FAQ" to get list for plugin details, for profile : homemades or favorites
		'callback' => null,
		'cache' => 12 // hours
		), $atts, 'papii' ) ); // "papii" added for filter hook "shortcode_atts" #3.6
	$content = '';

	if ( ! function_exists( 'plugins_api' ) ) {
		require( ABSPATH . '/wp-admin/includes/plugin-install.php' );
	}
	if ( ! empty( $plugin ) && empty( $profile ) ) {
		if ( ! $xml = get_transient( 'PAPII-' . $plugin ) ) {
			$resp = apply_filters( 'papii-' . $plugin, null );
			if ( ! $resp ) {
				$xml = (array) plugins_api( 'plugin_information', array( 'slug' => wp_unslash( $plugin ), 'is_ssl' => is_ssl() ) ); 
			} else {
				$xml = $resp;
			}
			if( $xml=='N;' ) {
				return 'Plugin not found: ' . esc_html( $plugin );
			}
			if ( $cache > 0 ) {
				set_transient( 'PAPII-' . $plugin, $xml, $cache * HOUR_IN_SECONDS );
			}
		}
		$xml = (array) maybe_unserialize( $xml );
		switch( $info ) {

			case 'banner': 
				set_time_limit( 0 );
				$content = 'http://ps.w.org/' . $plugin . '/assets/banner-772x250.jpg?t=' . md5( serialize( $atts ) );
				$test = wp_remote_head( $content );
				$test = wp_remote_retrieve_response_code( $test );
				if ( $test != 200 ) {
					$content = 'http://ps.w.org/' . $plugin . '/assets/banner-772x250.png?t=' . md5( serialize( $atts ) );
					$test = wp_remote_head( $content );
					$test = wp_remote_retrieve_response_code( $test );
				}
				if ( $test != 200 ) {
					$content = apply_filters( 'papii-no-banner', '' );
				}
			break;

			case 'tags': 
				$content = wp_sprintf( '%l', array_slice( $xml[ $info ], 1) ); 
			break;

			case 'tags_raw': 
				$content = serialize( array_slice( $xml[ $info ], 1 ) ); 
			break;

			case 'sections': 
				$content = serialize( array_keys( $xml[ $info ] ) ); 
			break;

			case 'rating':	
				$note = intval( $xml[ $info ] ) / 20;
				$content = intval( $note ) + ( round( intval( round( $note - ( intval( $note ) ), 1 ) * 10 ) / 5 ) / 2 );
			break;

			case 'contributors':
				foreach ( $xml['contributors'] as $k => $v ) {
					$content[] = sprintf( '<a href="%s" target="_blank">%s</a>', $v, $k );
				}
				$content = wp_sprintf( '%l', $content );
			break;

			case 'contributors_raw':
				$content = serialize( $xml['contributors'] );
			break;

			default:
				if ( isset( $xml[ $info ] ) ) {
					if ( is_array( $xml[$info] ) || is_object( $xml[$info] ) ) {
						$content = serialize( $xml[$info] );
					} else {
						$content = (string)$xml[$info];
					}
				} elseif( isset( $xml['sections'][ $info ] ) ) {
					if ( is_array( $xml['sections'][ $info ] ) || is_object( $xml['sections'][ $info ] ) ) {
						$content = serialize( $xml['sections'][ $info ] );
					} else {
						$content = (string) $xml['sections'][ $info ];
					}
				} else {
					$content = 'Error default: ' . esc_html( $info );
				}
			break;
		}
		if ( ! is_null( $callback ) ) {
			$content = call_user_func( $callback, $content );
		}
	}elseif ( ! empty( $profile ) && in_array( $info, array( 'homemades', 'favorites' ) ) ) {
		if ( ! $xml = get_transient( 'PAPII-' . $profile ) ) {
			$resp = apply_filters( 'papii-' . $profile, null );
			if ( ! $resp ) {
				$resp = wp_remote_get( 'http://profiles.wordpress.org/' . $profile );
				if ( ! is_wp_error( $resp ) && 200 === $resp['response']['code'] ){
					$doc = new DOMDocument();
					@$doc->loadHTML( $resp['body'] );
					$divs = $doc->getElementById( 'main-column' )->getElementsByTagName( 'div' );
					$plugins = array();
					$blocks = array( 'homemades', 'favorites' );
					$i=0;
					foreach ( $divs as $div ) {
						if ( strstr( $div->getAttribute( 'class' ), 'main-plugins' ) ) {
							$lis = $div->getElementsByTagName( 'li' );
							foreach ( $lis as $li ) {
								$as = $li->getElementsByTagName( 'a' );
								foreach ( $as as $a ) {
									$plugins[ $blocks[ $i ] ][ $a->nodeValue ] = $a->getAttribute( 'href' );
								}
							}
							if ( $sort ) {
								ksort( $plugins[ $blocks[ $i ] ] );
							}
							++$i;
						}
					}
					//
					if ( $cache > 0 ) {
						set_transient( 'PAPII-' . $profile, $xml, $cache * HOUR_IN_SECONDS );
					}
				}
			}
		}
		$content .= $before;
		foreach ( $plugins[ $info ] as $name => $link) {
			$new_papii = do_shortcode( str_replace( array( '[papii ', '[baw-papii ', ), '[papii plugin="' . basename( $link ) . '" ', str_replace( 'plugin=', 'dummy=', $new_sc ) ) );
			$temp = str_replace( '%content%', $new_papii, $format );
			$temp = str_replace( array( '%link%', '%name%' ), array( $link, $name ), $temp );
			$content .= $temp;
		}
		$content .= $after;
	}
	return $content;
}

