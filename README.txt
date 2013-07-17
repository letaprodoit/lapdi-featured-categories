=== TSP Featured Categories ===
Contributors: thesoftwarepeople,sharrondenice
Donate link: http://www.thesoftwarepeople.com/software/plugins/wordpress/featured-categories-for-wordpress.html
Tags: featured categories display gallery slider jquery moving boxes the software people
Requires at least: 3.5.1
Tested up to: 3.5.2
Stable tag: 1.0.2
License: Apache v2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

Featured Categories allows you to add featured categories with images to your blog's website.

== Description ==

The Software People's (TSP) Featured Categories plugin allows you to add featured categories with images to your blog's website. Featured categories have three (3) layouts and include category thumbnails and scrolling category gallery.

= Shortcodes =

Add a `Featured Categories` to posts and pages by using a shortcode inside your text or evaluated from within your theme. You may override page/post `Featured Categories` options with shortcode attributes defined on the plugin's settings page.

* `[tsp-featured-categories]` - Will display posts with the default options defined in the plugin's settings page.
* `[tsp-featured-categories title="Featured Categories"  numbercats="3" cattype="all" hideempty="1" hidedesc="N" maxdesc="60" layout="0" parentcat="3" widthbox=500 heightbox=300 orderby="count" widththumb="80" heightthumb="80" beforetitle="" aftertitle=""]` - Will override all attributes defined on the plugin's settings page.

== Installation ==

1. Upload `tsp-featured-categories` to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. After installation, refer to the `TSP Featured Categories` settings page for more detailed instructions on setting up your shortcodes.
4. `Featured Categories` widgets can be added to the sidemenu bar by visiting `Appearance > Widgets` and dragging the `TSP Featured Categories` widget to your sidebar menu.
5. Add some widgets to the sidemenu bar, Add shortcodes to pages and posts (see Instructions)
6. View your site
7. Adjust your CSS for your theme by visiting `Appearance > Edit CSS`
8. Adjust the `Sliding Gallery` settings, if necessary, by visiting `Plugins > Editor`, Select `TSP Featured Categories` and edit the `tsp-featured-categories.css` and `js/gallery-scripts.js` files
9. Manipulating the CSS for `#makeMeScrollable` and `#tsp-featured-categories` entries changes the gallery and category styles respectfully

== Frequently asked questions ==

= I've installed the plugin but my posts are not displaying? =

1. Make sure the folder `/wp-content/uploads/` has recursive, 777 permissions
2. Make sure you are listing all `categories` and/or `parentcat` is empty or the `parentcat` has children categories.

== Screenshots ==

1. Admin area widget settings.
2. Featured Categories displayed on the front-end.
3. Featured Categories gallery.
4. Admin area shortcode settings area.

== Changelog ==

= 1.0.2 =
* Fixed bug that caused images to not be added to posts.

= 1.0.1 =
* Checks for existence of parent settings menu before overwriting it
* Added in default images. TSP Plugins menu icon missing.

= 1.0 =
* Launch

== Upgrade notice ==

= 1.0.2 =
Image attachment fix.

= 1.0.1 =
Menu fix. Added in default images.