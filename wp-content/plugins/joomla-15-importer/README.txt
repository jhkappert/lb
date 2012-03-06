=== Plugin Name ===
Contributors: ePeterso2
Donate link: http://www.girlchoir.org
Tags: joomla, migrate, migration, convert, conversion, 
Requires at least: 3.0
Tested up to: 3.0.4
Stable tag: 1.0.0

This plugin exports articles from a Joomla 1.5 database and imports them as posts into Wordpress.

== Description ==

This plugin exports articles from a Joomla 1.5 database and imports them as posts into Wordpress.
It exports the content from a single Joomla category then allows the user to import the content
into one or more Wordpress categories.

This plugin does not convert Joomla articles into Wordpress pages, although its framework would
support such a conversion rather easily if someone would choose to code it.

This plugin does not work on Joomla 1.0 databases. If you need to convert your Joomla 1.0 data, you will first need
to [migrate to Joomla 1.5](http://docs.joomla.org/Migrating_from_1.0.x_to_1.5_Stable).

This plugin does not work on Joomla 1.6 databases. The Joomla 1.5 2-level section/category system has been replaced
in Joomla 1.6 with a multi-level hierarchical system similar to Wordpress. This framework could be adapted to support
Joomla 1.6 in the future.

== Installation ==

Simply install the plugin and activate it as you would any other plugin. It requires no special installation or configuration.

== Frequently Asked Questions ==

= How do I use this plugin? =

After you install and activate it, go to Dashboard > Tools > Import > Joomla 1.5. Enter your Joomla database connection
information on the first screen, then enter your export/import info on the second screen and click "Import". It's
really pretty simple and self-explanatory.

= What information do I need to export my Joomla articles? =

You'll need the basic database connection information, which is:

* Hostname of the MySQL server on which your Joomla database runs
* Port number of the MySQL server for your database (defaults to 3306)
* Username of a user with read privileges for your Joomla data
* Password for same user
* Database name of your Joomla database
* Prefix for your Joomla tables (defaults to jos_)

= How can I export an entire Joomla section at one time? =

You can't - you can only export one category at a time. If you'd like to add that feature to this plugin, I'd love to integrate it.

= How do I use this plugin with a version of Wordpress earlier than 3.0? =

Easy. Just upgrade to the latest version of Wordpress, then use this plugin. (You should upgrade anyway for a huge number of other reasons,
not just this plugin.)

= How do I import my Joomla 1.0 articles into Wordpress? =

Easy. Just [migrate to Joomla 1.5](http://docs.joomla.org/Migrating_from_1.0.x_to_1.5_Stable) first, then use this plugin.

= How do I import my Joomla 1.6 articles into Wordpress? =

This plugin doesn't support Joomla 1.6 import at this time.

= Why doesn't this plugin do _______? =

Because nobody coded it to do that. If you've got the time and know-how, I'd welcome any changes or updates to the plugin you can provide.

= Your plugin saved me a ton of time and effort. How can I show you my appreciation for creating it? =

Easy. I wrote this plugin to migrate the website of [The Girl Choir of South Florida](http://www.girlchoir.org),
so the best way to say "thank you" is to make a donation to the choir.
Follow the link and click on the "Give the Gift of Music" button. Or click on the Donate button on one of
the plugin screens. You and the choir will both be glad you did.

If you're not sure what to contribute, ask yourself what you would have paid someone to convert or retype all of your articles for you,
and donate that amount. Or $10 (US) ... whichever is greater.

All donations received from this plugin
will go towards scholarships for needy families. You can see and hear the choir in action at
their [YouTube channel](http://www.youtube.com/girlchoir). They're amazing!

The Girl Choir of South Florida is a nonprofit 501(c)(3) organization, and your contribution may be tax-deductible.
Details of the choir's charitable status are available on its web site at http://www.girlchoir.org.

== Screenshots ==

1. Database connection screen
1. Export/import parameters screen

== Changelog ==

= 1.0.0 =
Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
