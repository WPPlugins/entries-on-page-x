<?php
/*
Plugin Name: Entries on page&nbsp;x
Plugin URI: http://decaf.de/entries-on-page-x/
Description: Generates a link back to the archive page the current entry is on. Makes it easier for users to retrieve the chronology of a blog. <em>(Plugin needs some code in your templates. See <a href="http://wordpress.org/extend/plugins/entries-on-page-x/installation/">installation</a> details!)</em>
Version: 1.3.4
Author: DECAF
Author URI: http://decaf.de


	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/


	// actions
	if (!is_admin()) {
		add_action('init', 'i18n_eopx_setup');
		add_action('wp', 'archive_page_cookie');
	}



	// i18n setup
	$i18n_eopx_domain = 'entries-on-page-x';
	$i18n_eopx_is_setup = 0;
	function i18n_eopx_setup() {
		global $i18n_eopx_domain, $i18n_eopx_is_setup;
		if($i18n_eopx_is_setup) {
			return;
		}
		load_plugin_textdomain($i18n_eopx_domain, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
	}



	function archive_page_cookie() {
		global $wp_query, $wasquery;

		// read wp-cache-config
		$wp_cache_config_file = WP_CONTENT_DIR.'/wp-cache-config.php';
		if (is_readable($wp_cache_config_file)) {
			@include($wp_cache_config_file);
		}

		if (!$cache_enabled && !$super_cache_enabled) {

			$wasquery = array(); /* init */
			if (is_category() || is_tag() || is_author() || is_date()) {

				// category pages, tag pages, author pages and date pages
				// read query vars
				$wasquery = array($wp_query->query_vars['category_name'], $wp_query->query_vars['tag_id'], $wp_query->query_vars['author'], $wp_query->query_vars['year'], $wp_query->query_vars['monthnum'], $wp_query->query_vars['day'], $wp_query->query_vars['cat'], $wp_query->query_vars['m']);

				#print_r($wp_query);

				// set cookie
				setcookie('wordpress_'.md5(get_bloginfo('url')), implode(',', $wasquery), 0, "/");
			}
			elseif (is_single() && !is_preview()) {

				// single entry pages
				// check referer
				if (strpos(wp_get_referer(), get_bloginfo('url')) !== false) {

					// internal referer
					// read cookie information
					$wasquery = $_COOKIE['wordpress_'.md5(get_bloginfo('url'))];
					$wasquery = explode(',', strip_tags($wasquery));
				}
				// clear cookie
				setcookie('wordpress_'.md5(get_bloginfo('url')), '', 0, "/");
			}
			else {
				// clear cookie
				setcookie('wordpress_'.md5(get_bloginfo('url')), '', 0, "/");
			}
		}
	}



	function archive_page_num($wasquery) {
		global $wpdb, $archive_page_num;

		// init
		$counter    = 0;
		$postid     = get_the_ID(); // current post
		$tag_object = get_tag($wasquery[1]);

		// query
		$new_query = new WP_Query('category_name='.$wasquery[0].'&tag='.$tag_object->slug.'&author='.$wasquery[2].'&year='.$wasquery[3].'&monthnum='.$wasquery[4].'&day='.$wasquery[5].'&cat='.$wasquery[6].'&m='.$wasquery[7].'&showposts=-1');
		while ($new_query->have_posts()) {
			$new_query->the_post();
			// update_post_caches($posts);
			$counter++;
			
			// stop at current entry
			if ( get_the_ID() == $postid ) break;
		}

		// calculate page number
		if ( get_option('posts_per_page') > 0 ) {
			$ispage = ceil($counter/get_option('posts_per_page'));
		}
		else {
			$ispage = 1;
		}
		return $ispage;
	}



	function add_pagenum($url, $pagenum) {
		if ($pagenum > 1) {
			return add_query_arg('paged', $pagenum, $url);
		}
		else {
			return $url;
		}
	}



	function archive_page_link() {
		global $wasquery;

		if (is_single() && !is_preview()) {

			// read wp-cache-config
			$wp_cache_config_file = WP_CONTENT_DIR.'/wp-cache-config.php';
			if (is_readable($wp_cache_config_file)) {
				@include($wp_cache_config_file);
			}

			// clear $wasquery when caching is activated
			if ($cache_enabled || $super_cache_enabled) {
				unset($wasquery);
			}

			// pagenum
			$pagenum = archive_page_num($wasquery);

			$link = ''; /* init */
			switch(true) {

				// WP Super Cache enabled
				// fallback
				case ($super_cache_enabled):

					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Go to home page', 'entries-on-page-x').' </strong></a></span>';
					// add URL
					$link = str_replace('%url', get_bloginfo('url'), $link);
					break;

				// category
				case ($wasquery[0] != 0 || $wasquery[6] != ''):

					// check cat id + cat name
					$catid = 0;
					$catname = '';
					if ($wasquery[0] != 0) {
						$catid   = get_cat_id($wasquery[0]);
						$catname = $wasquery[0];
					}
					else {
						$catid   = $wasquery[6];
						$catname = get_cat_name($wasquery[6]);
					}
					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').' </strong></a> '.__('of category&nbsp;<strong>»%category«</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_category_link($catid), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add category name
					$link = str_replace('%category', $catname, $link);
					break;

				// tag
				case ($wasquery[1] > 0):

					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('for tag&nbsp;<strong>»%tag«</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_tag_link($wasquery[1]), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add tag name
					$tag_object = get_tag($wasquery[1]);
					$tag = $tag_object->name;
					$link = str_replace('%tag', $tag, $link);
					break;

				// author
				case ($wasquery[2] != ''):

					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('by&nbsp;<strong>%author</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_author_posts_url($wasquery[2]), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add author name
					$link = str_replace('%author', get_author_name($wasquery[2]), $link);
					break;

				// date (?m=..)
				case ($wasquery[7] > 0):					

					// date
					$year  = substr($wasquery[7], 0, 4);
					$month = substr($wasquery[7], 4, 2);
					$day   = substr($wasquery[7], 6, 2);
					if ($year)  $date = date_i18n(__('Y', 'entries-on-page-x'), mktime(1, 1, 1, 1, 1, $year));
					if ($month) $date = date_i18n(__('F Y', 'entries-on-page-x'), mktime(1, 1, 1, $month, 1, $year));
					if ($day)   $date = date_i18n(__('j F Y', 'entries-on-page-x'), mktime(1, 1, 1, $month, $day, $year));
					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('from&nbsp;<strong>%day</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_day_link($year, $month, $day), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add day
					$link = str_replace('%day', $date, $link);
					break;

				// day
				case ($wasquery[5] > 0):

					// date
					$date = date_i18n(__('j F Y', 'entries-on-page-x'), mktime(1, 1, 1, $wasquery[4], $wasquery[5], $wasquery[3]));
					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('from&nbsp;<strong>%day</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_day_link($wasquery[3], $wasquery[4], $wasquery[5]), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add day
					$link = str_replace('%day', $date, $link);
					break;

				// month
				case ($wasquery[4] > 0):

					// date
					$date = date_i18n(__('F Y', 'entries-on-page-x'), mktime(1, 1, 1, $wasquery[4], 1, $wasquery[3]));
					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('from&nbsp;<strong>%month</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_month_link($wasquery[3], $wasquery[4]), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add month
					$link = str_replace('%month', $date, $link);
					break;

				// year
				case ($wasquery[3] > 0):

					// date
					$date = date_i18n(__('Y', 'entries-on-page-x'), mktime(1, 1, 1, 1, 1, $wasquery[3]));
					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a> '.__('in&nbsp;<strong>%year</strong>', 'entries-on-page-x').'</span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_year_link($wasquery[3]), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					// add year
					$link = str_replace('%year', $date, $link);
					break;

				// default
				default:

					// link
					$link = '<span class="entriesonpagex"><a href="%url"><strong>'.__('Entries on page&nbsp;<span class="page">%page</span>', 'entries-on-page-x').'</strong></a></span>';
					// add page number
					$link = str_replace('%page', $pagenum, $link);
					// add URL
					$url = add_pagenum(get_bloginfo('url'), $pagenum).'#post-'.get_the_ID();
					$link = str_replace('%url', $url, $link);
					break;
			};

			// output
			echo $link;
		}
	}
?>