=== Simple Relative Urls ===
Contributors: amisiewicz
Donate link:
Tags: relative, url, localtunnel
Requires at least: 5.0
Tested up to: 5.8
Stable tag: 1.0.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin makes urls (home url, js, css, images) across website relative. Works great with localtunnel npm package.

== Description ==

**Simple Relative Urls** plugin changes:

* home and siteurl
* script src urls
* style link urls
* images src urls

It uses wordpress filters.
It doesn't interact with database.
It is fast, it doesn't search and replace generated html.
It doesn't change hardcoded urls (for example in post content).
It is flexible.

Plugin was originally created to enable wordpress to work with a [localtunnel](https://www.npmjs.com/package/localtunnel) npm package

> localtunnel exposes your localhost to the world for easy testing and sharing! No need to mess with DNS or deploy just to have others test out your changes.

== Example use cases ==

If Your development tasks don't involve images, don't download them from a server. Just install the plugin and add this line of code to your wp-config.php file

`
define( 'SRU_IMG_SRC_RELATIVE', false );
`
Your local copy will then display images from their oryginal source.
