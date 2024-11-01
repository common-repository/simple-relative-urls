<?php
/**
 * Plugin Name: Simple Relative Urls
 * Plugin URI: https://github.com/endriu84/simple-relative-urls
 * Description: Changes database options 'home' and 'siteurl' to relative site url
 * Version: 1.0.4
 * Author: Andrzej Misiewicz
 * Author URI: https://pandasoft.pl
 * Author Email: andrzej@misiewicz.it
 * License: GPLv2 or later
 */

/**
 * To make a copy of the website without downloading any images
 * use SRU_IMG_SRC_RELATIVE constant in wp-config.php like this
 * define( 'SRU_IMG_SRC_RELATIVE', false );
 */
if ( ! defined( 'SRU_IMG_SRC_RELATIVE' ) ) {
	define( 'SRU_IMG_SRC_RELATIVE', true );
}


final class Simple_Relative_Urls {

	private $scheme;

	private $host = '';

	/**
	 * Non standard port only
	 *
	 * @var string
	 */
	private $port = '';


	public function __construct() {

		$this->scheme = is_ssl() ? 'https' : 'http';
		$this->setup_host_and_port();
	}

	public function register() {
		// Main urls.
		add_filter( 'option_home', array( $this, 'get_url' ), 100, 1 );
		add_filter( 'option_siteurl', array( $this, 'get_url' ), 100, 1 );
		// CSS & JS assets.
		add_filter( 'style_loader_src', array( $this, 'replace_host' ), 100, 1 );
		add_filter( 'script_loader_src', array( $this, 'replace_host' ), 100, 1 );
		// Images.
		if ( SRU_IMG_SRC_RELATIVE ) {
			add_filter( 'wp_get_attachment_image_src', array( $this, 'replace_image_host' ), 100, 1 );
			// srcset.
			add_filter( 'option_upload_url_path', array( $this, 'upload_url_path' ), 100, 1 );
		}
	}

	public function get_url( $url ) {

		if ( '' === $this->host ) {
			return $url;
		}

		return $this->scheme . '://' . $this->host . ( $this->port ? ':' . $this->port : '' );
	}

	public function replace_host( $url ) {

		if ( '' === $this->host ) {
			return $url;
		}

		$parts           = wp_parse_url( $url );
		$parts['scheme'] = $this->scheme;
		$parts['host']   = $this->host;
		if ( $this->port ) {
			$parts['port'] = $this->port;
		} elseif ( isset( $parts['port'] ) ) {
			unset( $parts['port'] );
		}

		return $this->build_url( $parts );
	}

	public function replace_image_host( $image ) {

		if ( isset( $image[0] ) ) {
			$image[0] = $this->replace_host( $image[0] );
		}

		return $image;
	}

	public function upload_url_path( $upload_url_path ) {

		$url = $this->get_url( $upload_url_path );
		if ( $url ) {
			if ( get_option( 'upload_path' ) && 'wp-content/uploads' !== get_option( 'upload_path' ) ) {
				$url .= '/' . get_option( 'upload_path' ) . '/';
			} else {
				$url .= '/wp-content/uploads/';
			}
		}

		return $url;
	}

	/**
	 * Sets up host and port from a server variable
	 *
	 * @return void
	 */
	private function setup_host_and_port() {

		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$parts = explode( ':', $_SERVER['HTTP_HOST'] );
			if ( isset( $parts[0] ) ) {
				$this->host = apply_filters( 'simple_relative_urls_host_whitelist', $parts[0] );
			}

			if ( isset( $parts[1] ) ) {
				$this->port = $parts[1];
			}
		}
	}

	/**
	 * https://stackoverflow.com/questions/4354904/php-parse-url-reverse-parsed-url
	 *
	 * @param  array $parts
	 * @return string
	 */
	private function build_url( array $parts ) {

		return ( isset( $parts['scheme'] ) ? "{$parts['scheme']}:" : '' ) .
		( ( isset( $parts['user'] ) || isset( $parts['host'] ) ) ? '//' : '' ) .
		( isset( $parts['user'] ) ? "{$parts['user']}" : '' ) .
		( isset( $parts['pass'] ) ? ":{$parts['pass']}" : '' ) .
		( isset( $parts['user'] ) ? '@' : '' ) .
		( isset( $parts['host'] ) ? "{$parts['host']}" : '' ) .
		( isset( $parts['port'] ) ? ":{$parts['port']}" : '' ) .
		( isset( $parts['path'] ) ? "{$parts['path']}" : '' ) .
		( isset( $parts['query'] ) ? "?{$parts['query']}" : '' ) .
		( isset( $parts['fragment'] ) ? "#{$parts['fragment']}" : '' );
	}
}

global $simple_relative_urls;

$simple_relative_urls = new Simple_Relative_Urls();
$simple_relative_urls->register();


/*
// Possible usage (plugin autoptimize).
global $simple_relative_urls;
add_filter( 'autoptimize_filter_base_replace_cdn', array( $simple_relative_urls, 'replace_host' ), 100, 1 );
*/
