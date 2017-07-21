=== Entries on page x ===
Contributors: decafmedia
Donate link: http://laptop.org/
Tags: archive, archives, link, links, menu, navigation
Requires at least: 2.5
Tested up to: 3.0.1
Stable tag: 1.3.4

Generates a link back to the archive page the current entry is on. Makes it easier for users to retrieve the chronology of a blog.

== Description ==

Generates a link back to the archive page the current entry is on. Makes it easier for users to retrieve the chronology of a blog.

**Examples (without function):**

* [Entries on page 12](#)
* [Entries on page 1](#) of category »WordPress«
* [Entries on page 4](#) for tag »Plugins«
* [Entries on page 2](#) by John Doe
* [Entries on page 9](#) from August 2008

**Live demo:**
See description at [decaf.de/entries-on-page-x/](http://decaf.de/entries-on-page-x/)

**Installation:**
Plugin needs `<?php if (function_exists('archive_page_link')) { archive_page_link(); } ?>` in your templates. See [installation](http://wordpress.org/extend/plugins/entries-on-page-x/installation/) details.

**Multi-language:**
Plugin works in several languages right now, see [notes](http://wordpress.org/extend/plugins/entries-on-page-x/other_notes/).

== Installation ==

1. Upload the entries-on-page-x folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place `<?php if (function_exists('archive_page_link')) { archive_page_link(); } ?>` in your templates.
4. HTML of your article template should contain the post ID (`id="post-<?php the_ID(); ?>`) if you like 'Entries on page x' to refer not only to a page with posts but to the post itself.

== Frequently Asked Questions ==

= Is the plugin compatible with 'WP Cache' and 'WP Super Cache'? =

Technically, it is. But as it wouldn't be meaningful (with static HTML files not even possible) to let cached pages serve the dynamic archive link, 'Entries on page x' detects these plugins and serves **a)** a basic link to page x without category, tag, author oder date if you run 'WP Cache' or 'WP Super Cache' in legacy caching mode (»Half on« setting), or **b)** a link to the blog's home page only if you run 'WP Super Cache' in full mode.

If you use 'Super cache' mode permanently, is doesn't make sense to run the 'Entries on page x' plugin. But if you activate 'Super cache' only at high server loads, you can use 'Entries on page x' and don't have to worry about broken links.

= Is the plugin compatible with 'DB Cache' and 'Hyper Cache'? =

Yes, it is. (It is supposed to be.)

== Changelog ==

= 1.3.4 (1/5/2010) =
* Fixed language files (due to relative paths vs. full paths behaviour in WP 2.9 core)

= 1.3.3 (1/3/2010) =
* Ukrainian/Українська language added

= 1.3.2 (8/11/2009) =
* Belarusian/Беларуская language added

= 1.3.1 (6/5/2009) =
* bugfix with categories

= 1.3 (4/24/2009) =
* add_action conditional + cache plugins compatibility

= 1.2 (3/6/2009) =
* major bugfixing concerning category links and permalink settings

== Localization (L10n) ==

Available **languages** at this time:

* **English**
* **German/Deutsch**
* **Turkish/Türkçe** *(by courtesy of [DJ N-Gin](http://dj-tut.de))*
* **Polski** *(by courtesy of [Jeena Paradies](http://jeenaparadies.net))*
* **Swedish/Svenska** *(by courtesy of [Jeena Paradies](http://jeenaparadies.net))*
* **Bulgarian/Български** *(by courtesy of [Bellerophon](http://bellerophon-blog.de))*
* **Chinese/ 中文** *(by courtesy of [Johanna and  孔晶 Kong Jing](http://www.johanna-enzinger.de))*
* **French/Français** *(by courtesy of [Patrick Andrieu](http://www.atomic-eggs.com/cwi/cwi_4.shtml))*
* **Spanish/Español** *(by courtesy of [Stonie](http://stonie.wwnw.de/))*
* **Russian/Русский** *(by courtesy of dedlfix)*
* **Italian/Italiano** *(by courtesy of at)*
* **Belarusian/Беларуская** *(by courtesy of [FatCow](http://www.fatcow.com/))*
* **Ukrainian/Українська** *(by courtesy of [ghost](http://antsar.info/))*

If you localize the plugin, we would be glad to know -- Thanks a lot!

== Code ==

**Structure of the HTML code is like that:**

	<span class="entriesonpagex"><a href=".."><strong>Entries on page <span class="page">3</span></strong></a> of category »<strong>Foobar</strong>«</span>
  
  
**Make use of these CSS classes if you want to set up individual styles:**

	.entriesonpagex { }
	.entriesonpagex strong { }
	.entriesonpagex a { }
	.entriesonpagex a strong { }
	.entriesonpagex a strong .page { }
  
  
**HTML of your article template should contain the post ID if you like 'Entries on page x' to refer not only to a page with posts but to the post itself:**

	<div id="post-<?php the_ID(); ?>" class="post"><h1>Post title..

== Cookies ==

The plugin makes use of **cookies** in order to save the refering page that leads to a single entry. If cookies aren't accepted, the plugin generates default archive links apart from category, tag, author and date.