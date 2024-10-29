=== PAPII - Plugin API Infos ===
Contributors: juliobox
Tags: plugin, information, shortcode
Requires at least: 2.8
Tested up to: All versions
Stable tag: trunk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RB7646G6NVPWU
License: GPLv2

Add some shortcodes to load information about a plugin from the repository or plugins from a profile.

== Description ==
Ever wanted to display some plugin's informations on your website ? But you didn't want to hard code it. PAPII is designed for this. Just use the [papii] shortcode to display informations from the official page.

== Installation ==

1. Extract the plugin folder from the downloaded ZIP file.
2. Upload BAW Papii folder to your /wp-content/plugins/ directory.
3. Activate the plugin from the "Plugins" page in your Dashboard.
4. Go to settings !

== Screenshots ==
1. Demo for this plugin using kind of the example in FAQ
1. Demo with the profile plugins list


== Frequently Asked Questions ==
- How to use it ?
> Use this shortcode [papii] with this properties:
  plugin, info, cache.
  Example : [papii plugin="baw-autoshortener" info="version" cache="48"]
  This code will show the version for baw-autoshortener plugin and store this data 48h.

- I need to retrieve all informations, what are they ?
> You have : name, slug, version, author, author_profile, contributors, requires, tested, rating, num_ratings, downloaded, last_updated, added, homepage, description, installation, screenshots, changelog, faq, short_description, description, download_link, tags
Compatibility property is not included.

- Ok, so i need a full example
> Use it first in your post like this:
  [papii plugin="baw-autoshortener" cache="24"]
  Then you can add this for example :
  Download the version [papii info="version" plugin="baw-autoshortener" cache="24"] for plugin [papii info="name" plugin="baw-autoshortener" cache="24"] (downloaded [papii info="downloaded" plugin="baw-autoshortener" cache="24"] times), here: [papii info="download_link" plugin="baw-autoshortener" cache="24"]
  This is a good way to implement this in a theme with do_shortcode ;)

- How can i get plugins from a WP.org profile ?
> Use the same shortcode like this:
[papii profile="juliobox" info="homemade"/]
you can even add in the shortcode content more shortcodes, see the plugin URI for some advanced examples.

- I'm developper, what can i do more?
> You can ask in a theme/plugin for "tags_raw", "contributors_raw" or "sections" and format it like you want.

== Changelog ==

= 1.6 =
* 09 jun 2014
* Change the way to retreive informations, more WP style
* Add the possibility to get plugins from a WP.org profile, homemade plugins or favorite ones, see FAQ

= 1.5.3 =
* 10 may 2013
* Fix a bug when you try to get informations from a non existent plugin

= 1.5.2 =
* 02 may 2013
* Filter added to shortcut the repository call, filter named "papii-".$plugin ($plugin comming from the "plugin" shortcode's prop)

= 1.5.1 =
* 23 apr 2013
* Little bug fix, sorry

= 1.5 =
* 23 apr 2013
* Better code again, avoid warning on cast or not serialized data
* Can return "sections" as serialized data

= 1.4 =
* 23 apr 2013
* Stop using XML and parsing, go .php !
* Better and shorter code again

= 1.3 =
* 22 apr 2013
* Remove the baw-papii_settings shortcode, useless
* Remove the "sep" value
* Added a "papii" shortcode, same as "baw_papii"
* Cache improved, one call per plugin, not per information, my bad!
* Added a function from my friend Greg to parse and cache full XML, thanks!

= 1.2 =
* 26 aug 2011
* First Release