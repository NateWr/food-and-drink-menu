=== Food and Drink Menu ===
Contributors: NateWr
Author URI: https://github.com/NateWr
Plugin URL: http://themeofthecrop.com
Requires at Least: 3.5
Tested Up To: 3.9.1
Tags: restaurant menu, cafe, coffee, restaurant, food, drink, dining, cuisine
Stable tag: 1.3.1
License: GPLv2 or later
Donate link: http://themeofthecrop.com

Create a restaurant menu for cafes, bars and eateries, and display it in templates, posts, pages and widgets.

== Description ==

Create a restaurant menu for cafes, bars and eateries, and display it in templates, posts, pages and widgets.

This plugin creates two new post types, Menu Items and Menus, which can be used to easily construct a menu. Create items on your menu, group them into sections and then build menus out of them.

* Unlimited menus and menu items
* Add a photo and price for each menu item
* Menu sections can include guidance, such as "All entrees come with a side salad or fries"
* Add a footer to each menu for legal disclaimers or other notes
* Display your menu or menu item in pages, posts and navigation menus or use the custom post type format
* Use the widget to display your menu in a sidebar
* Responsive menu layout to improve mobile viewing
* Templates to easily customize the output of menus and menu items

This plugin is part of a group of plugins in development for restaurants. Check out the [Restaurant Reservations](http://wordpress.org/plugins/restaurant-reservations/) plugin as well.

= Examples =

[The Wayfarer](http://thewayfarernyc.com/menu/dinner/)

= How to use =

Read the [documentation](http://themeofthecrop.com/docs/food-and-drink-menu?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "How to use the Food and Drink Menu plugin") for how to create your menus. Once you've created a menu you can add it to any menu on your site from the Appearance > Menus area. Alternatively, you can use the widget to add it to a sidebar or include it in any existing page or with a shortcode:

`[fdm-menu id=123]`

Once you've saved the menu in the admin panel, it will give you the shortcode so you don't need to go hunting for the id.

= Developers =

Want to customize the layout? Read [this tutorial](http://themeofthecrop.com/2014/01/28/customize-restaurant-menu-templates/?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "How to use templates to customize your restaurant menu") on how to use the plugin's template system to customize the layout. More tutorials will follow, so follow me on [Twitter](https://twitter.com/themeofthecrop) or [Google+](https://plus.google.com/+Themeofthecrop) for more details.

This plugin is packed with hooks so you can extend and customize it to your delight. A pro addon is available at [Theme of the Crop](http://themeofthecrop.com/?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "Buy the Food and Drink Menu Pro plugin"), but you can create your own addons too.

This plugin is [on GitHub](https://github.com/NateWr/food-and-drink-menu "Food and Drink Menu at Github") so fork it up.

== Installation ==

1. Unzip `food-and-drink-menu.zip`
2. Upload the contents of `food-and-drink-menu.zip` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Create Menu Items and add them to Menus from the WordPress admin dashboard. See the /docs/ folder in the plugin for more information.

== Frequently Asked Questions ==

= Where can I find more documentation? =

There is a full readme file available in the /docs/ folder. You can also learn more at [Theme of the Crop](http://themeofthecrop.com/plugins/food-and-drink-menu?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "Full information about the WordPress restaurant menu plugin").

= I want more features =

A Pro version is available which features:

* Comprehensive icons to indicate dietary and ethical requirements, including organic, gluten-free, kosher, halal and many more
* Discounted prices
* Custom menu item flags to attach any information to any menu item
* Badges for item features, specials and sales
* Google maps to show off local suppliers or ethical sourcing programs

It can be purchased at [Theme of the Crop](http://themeofthecrop.com/?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "Buy the Food and Drink Menu Pro plugin").

== Screenshots ==

1. Classic menu style in a two-column layout
2. Classic menu style in a single-column layout
3. Opt to use basic CSS only to adopt your theme's typography
4. Add menu items with photos like regular WordPress posts
5. Layout your menu with a point-and-click interface
6. Upgrade to Food and Drink Menu Pro at [Theme of the Crop](http://themeofthecrop.com/?utm_source=Plugin&utm_medium=Plugin%20Description&utm_campaign=Food%20and%20Drink%20Menu "Buy the Food and Drink Menu Pro plugin") for discounted prices, sales and specials, custom dietary icons, and more

== Changelog ==

= 1.3.1 (2014-05-14) =
* Update Simple Admin Pages library to improve compatibility with Restaurant Reservations plugin

= 1.3 (2014-05-09) =
* Update Simple Admin Pages library to version 2
* Move settings page under the Menu tab
* Fix i8n localization for a couple of strings

= 1.2.2 (2014-04-23) =
* Minor maintenance release

= 1.2.1 (2014-04-01) =
* Fix: errors in multisite. Use FDM_PLUGIN_DIR with require_once() calls
* Fix: Notice thrown when the_content filter called outside of loop

= 1.2 (2014-02-12) =
* Add a POT file for easier language translations
* Add support for displaying menu title and content when using a shortcode
* Add support for menu footers to display legal notices or other content

= 1.1.4 (2014-02-07) =
* Fix PHP error notice that appeared if an empty section was rendered (h/t [Remco Verweij](https://github.com/verweijder) for reporting)

= 1.1.3 (2014-02-03) =
* Fix CSS error with the Classic style

= 1.1.2 (2014-01-25) =
* Minor maintenance release

= 1.1.1 (2014-01-24) =
* Minor maintenance release

= 1.1 (2014-01-23) =
* Major refactor of code to separate concerns
* Implemented templates to control and customize output of menus and menu items
* Added new hooks to easily implement custom styles
* Improved custom post type page output for menus and menu items
* Try to block more cases where menu items show a bullet point
* Filter menu item post content so proper formatting is displayed
* Updated version of Simple Admin Pages used

= 1.0.2 (2013-12-12) =
*	Bumped the version number to fix tagging issues with WordPress plugin repository

= 1.0.1 (2013-12-12) =
*	Update admin icons to support 3.8

= 1.0 (2013-12-05) =
*	Initial release

== Upgrade Notice ==

= 1.3.1 =
This update is a minor maintenance release to update a library shared with the Restaurant Reservations plugin. If you use both plugins, please update.

= 1.3 =
This update fixes a couple strings that could not be translated and upgrades a library powering the settings panel to improve performance slightly.

= 1.2.2 =
This minor update just indicates its been tested with WordPress 3.9.

= 1.2.1 =
This update fixes an error that occurs when using the plugin with WordPress Multisite as well as a PHP Notice that could occur if the shortcode was called outside of the loop.

= 1.2 =
This update adds support for menu footers, so you can add legal notices or other content (ie - "All dishes prepared in a kitchen that contains nuts."). Shortcodes can now display menu titles and post content. A POT file was added to make language translations easier for you.

= 1.1.4 =
This update fixes a small error that would appear if a menu contained a section with no menu items.

= 1.1.3 =
This update fixes a problem with the Classic style which prevented the font from appearing on some browsers.

= 1.1 =
This update entailed a major refactor of the code. It now supports display of single menu items in shortcodes and widgets, templates to customize output of menus and menu items, hooks to implement new styles and more. Learn more at themeofthecrop.com.

= 1.0.2 =
This small update will make the Food and Drink Menu compatible with the new admin dashboard style in WordPress 3.8
