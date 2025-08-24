=== Marbl for Amazon ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://www.c-amie.co.uk/qlink/?id=166
Tags: Amazon, eBay, link, monetization, international, multi-region, buyer, associate, affiliate, partner
Requires at least: 4.7
Tested up to: 6.5.2
Stable tag: 4.7
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embeds a Multi-Region Affiliate & Referral Buyers Link (Marbl) for Amazon store chooser drop-list buyer link into the page at any point the shortcode is added.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wordpress.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

= To install Marbl =
1. Upload the plugin files to the `/wp-content/plugins/marbl` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings, 'Marbl Links for Amazon & eBay' screen to configure the plugin
4. Configure the 'General Settings' section as required

= To enable Shortcodes for Amazon Links =
1. Open Settings, 'Marbl Links for Amazon & eBay' screen
2. Select the 'Amazon Settings' button
3. Select 'yes' next to 'Enable Marbl for Amazon'
4. Click 'Save Changes'
5. Enter at least one global Amazon Associates ID into its respective country textbox
6. Click 'Save Changes'

= To enable Shortcodes for eBay Links =
1. Open Settings, 'Marbl Links for Amazon & eBay' screen
2. Select the 'eBay Settings' button
3. Select 'yes' next to 'Enable Marbl for eBay'
4. Click 'Save Changes'
5. Enter your eBay Partner ID
6. Enter your Default Campaign ID
7. Enable at least one global eBay region from the list
8. Click 'Save Changes'

== Frequently Asked Questions ==

= How do I obtain an Amazon Affiliates ID? =

Head to https://affiliate-program.amazon.com/ and select the regional Amazon programme that you would like to sign up with.

= How do I obtain an eBay Partner Network ID? =

Head to https://epn.ebay.com and sign-up for your regional programme.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.9.20.20240418 =
* [JavaScript] rel="nofollow" now writes "rel="nofollow sponsored"

= 0.9.19.20240407 =
* [JavaScript] Added .DisclaimerText and .DisclaimerPosition fields to both Amazon and eBay types
* [JavaScript] Added .TOP and .BOTTOM static fields to set the disclaimer position
* [JavaScript] Added .setDisclaimer(position, text) function to both Amazon and eBay types
* [WordPress] Added Enable/Disable disclaimer text fields for all sponsored link types
* [WordPress] Added fields to set disclaimer text and set the top/bottom position for all sponsored link types

= 0.9.18.20240305 =
* [JavaScript] Added this.RegionsList list to the eBay class to allow 'ALL' to be fed into the .addRegions() method

= 0.9.17.20230807 =
* Added flag images for AE, PL, SA, SE and SG
* [JavaScript] Added regional support for UAE, Belgium, Netherlands, Poland, Saudi Arabia, Sweden and Singapore
* [WordPress] Added property to lookup full country names
* [WordPress] Expanded country support for Amazon on the Amazon Associates Settings page
* [WordPress] Full country names are now displayed on the settings pages

= 0.9.16.20230510
* [JavaScript] Fixed issue with eBay URL link conversion where the URL did not include a SEO friendly product name string

= 0.9.15.20221220
* [JavaScript] Updated eBay URL syntax

= 0.9.14.20220523 =
* [WordPress] Added eBay.pl support on the admin UI for eBay settings
* [WordPress] Fixed issue with saving eBay country visibility
* [JavaScript] Added eBay.pl support
* [JavaScript] Updated eBay URL syntax
* [JavaScript] Added GB as an eBay country code alias for United Kingdom
* [JavaScript] Ampersand characters in freetext search strings were not being URL encoded and were thus dropped as querystring demarcation characters

= 0.9.13.20220121 =
* [JavaScript] Added rendering choice property to allow the default rendering of the Marbl link adjacent to the calling HTML <script> tag or alternatively into a named HTML Element (by ID)
* [JavaScript] Added INPLACE and CONTAINER ENUM
* [JavaScript] RenderMode and ContainerId properties

= 0.9.12.20210222 =
* [JavaScript] Improved Amazon ISBN detection that was leading to corrupt derived link results
* [JavaScript] Added PrintDebug [boolean] property to allow console.log output of derived link generation decisions
* [JavaScript] Fixed an issue where legacy http:// links that contained Amazon search friendly product names in the URL would not display the friendly name
* [JavaScript] Fixed an issue where Amazon URL keyword search term friendly names would appear with + signs instead of corrected spaces

= 0.9.11.20210220 =
* [JavaScript] Implemented new eBay Partner Network Link format
* [JavaScript] Added createDerivedLink(), createDerivedBuyLink(); to MarblEbayLink. createDerivedLink() allows an eBay URL containing a Item ID, search phrase or URL to be automatically converted into a working link
* [JavaScript] MarblAmazonLink.createDerivedLink() can now accept and will attempt to parse an Amazon URL to extract keyword and/or ASIN/ISBN information from the URL
* [JavaScript] Deprecated and removed eBay getRedirectorVectorId()
* [JavaScript] Deprecated and removed eBay getVectorId()
* [JavaScript] Deprecated and removed eBay PartnerId
* [JavaScript] Modularised Amazon.isAsin() and Amazon.isIsbn();
* [JavaScript] Fixed non-working Amazon ISBN link URL
* [WordPress] Removed Partner ID box from the eBay settings UI
* [WordPress] Enhanced uninstaller

= 0.9.10.20210130 =
* [JavaScript] Link labels will always be generated for Amazon Link ASIN and ISBN values, instead of leaving a blank in the event that no manual label paramater is provided
* [JavaScript] Added createDerivedLink(), createDerivedBuyLink(); to MarblAmazonLink. createDerivedLink() allows an Amazon URL containing a ASIN or ISBN to be automatically converted into a working link

= 0.9.9.20210128 =
* [WordPress] Improved string sanitisation processing
* [WordPress] Added macro to allow &quot; to be used to enter a quotation mark in the search or label shortcode parameters should a quote be necessary (e.g. for inches)
* [WordPress] The default region country flags were not displaying properly on the eBay Settings tab in the Setting plugin
* [JavaScript] Added HTML decode/encode functions to allow contectual sanitisation of search and label text depending on whether it is in the affliate URL or link text
* [JavaScript] Added workaround to allow the EPN redirector to accept searches with " marks in it without sending the user to the homepage

= 0.9.8.20190411 =
* [JavaScript] Added MarblLink generator class with automatic repository pathintrospection
* [JavaScript] Merged MarblLink, MarblAmazonLink and MarblEbayLink into a single Marbl.js file
* [JavaScript] Added eBay Vector ID lookups internally rather than expecting them to come from external supplier lookups
* [JavaScript] createCustomBuyLink() -> createLink()
* [JavaScript] Added optional CustomImagePath argument to createLink()
* [JavaScript] The behaviour of DisplayMode.IMAGE was not correctly inserting the Label into the ATL tag if one was provided

= 0.9.7.20190409 =
* [WordPress] Improved appearance and layout of the settings UI
* [WordPress] Implemented custom CSS/image resources path setting
* [WordPress] Added options to enable/disable Amazon Affiliates links
* [WordPress] Added options to enable/disable eBay Partner Network links
* [WordPress] Implemented input sanitisation and validation on all settings input objects
* [JavaScript] Added IMAGE, TEXT, BOTH enum values to the link classes

= 0.9.6.20190408 =
* Added flag images for AT, BE, CH, IE and NL
* Added default eBay icons
* Added default eBay CSS
* [WordPress] Added administrative screen settings for eBay Partner ID
* [WordPress] Added administrative screen settings for eBay Campaign ID
* [Bug Fix] Display mode wasn't honouring the bit flag settings as intended
* [JavaScript] First release of Marbl for eBay Partner Network (EPN), with search, store and item links
* Default CSS adds a 2px pad to the bottom of the icon to keep it off of baselines
* [WordPress] Added examples of the eBay icons to the eBay settings tab
* [WordPress] Changed the Amazon sotores="" attribute to regions="" to align the Amazon and eBay API
* [WordPress] Added usage examples on the administrative screen for eBay

= 0.9.5.20190325 =
* [JavaScript] Converted to Object-Orientated
* [JavaScript] Added Links for eBay Partner Network [beta]
* [WordPress] Added settings for eBay Partner Network
* [WordPress] Converted settings page into tabs
* [Wordpress] Plugin syntax changed from [marbl-amazon... to [marbl type="amazon"...

= 0.9.4.20180626 =
* [JavaScript] Added 'Link add nofollow option' with default=true to base class
* [WordPress] Added settings option to configure the Link nofollow on/off state

= 0.9.3.20180605 =
* [JavaScript] Added CustomImage parameter, used to override the built-in images
* [JavaScript] Converted bolLabelOnly to ENUM DisplayStyle
* [JavaScript] Added IMAGE, TEXT, BOTH emum lookup values
* [JavaScript] If label is set on an image only render, the label will be added to the image ALT text
* [JavaScript] URL encoded labels with + sings are now decoded back to spaces
* [WordPress] Added 'nclude default CSS StyleSheet' configuration option
* [WordPress] imageonly property renamed display to align with the DisplayStyle ENUM

= 0.9.2.20180604 =
* [JavaScript] Updated plugin entry points to 'marbl-amazon' as a brand identity for 'Multi-region Affiliate & Referral Buyers Link'
* [WordPress] Added usage and examples to the options page
* Added the 4 built-in Amazon icons to the options page
* [JavaScript] Renamed the base JS class to MarblAmazonLink
* [WordPress] Added WordPress uninstall
* Code pattern now available to add Marbl to PD9 Software MegaBBS 2.2

= 0.9.1.20180531 =
* [JavaScript] Updated plugin entry points to 'abl-amazon' to comply with WordPress plugin directory naming requirements
* [JavaScript] Added support for text label linking instead of the default icon links

= 0.9.0.20180521 =
* [WordPress] Initial WordPress plugin version as 'amazon-buyer-link'
* [WordPress] Added config page to WordPress plugin
* Added default 16x16, 24x24, 32x32 and 48x48 Amazon icons

= 0.1.0.20180429 =
* Initial release
* [JavaScript] Standalone JavaScript File for www.c-amie.co.uk


== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`

