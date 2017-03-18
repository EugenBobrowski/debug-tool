=== Debug Tool ===
Contributors: eugenbobrowski
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UUWASQ4U7BNUE
Tags: debug, debugger, developer, queries, wp debug, debug bar, for developers
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Show helpful debug bar at front and admin side. Tool for developers.

== Description ==

This is tool that help developers and administrators easier getting information.

The debug bar is available on front side for everyone if WP_DEBUG is true. If you are admin you can call bar on any page by keys Ctrl+Alt+Shift+D.


= Features: =

* Implementation time counter
* Queries counter
* Queries list (sortable)
* Memory counter
* Segment checker
* References menu

**Implementation time counter**

It count the time from plugin loaded action to debug bar.
It does not consider processes that implement before and during the plugins are loading.

**Queries counter**

It count all what is queried via $wpdb object.

**Queries list**

It show all what is queried via $wpdb object.

The `SAVEQUERIES` constant must be defined as `true`.

**Memory counter**

Returns the amount of memory, in bytes, that's currently being allocated to your PHP script.

**Segment checker**

Also you can check any segment of your code.

<pre><code>
do_action('check_segment', 'segment_1');

//do somethisng

do_action('check_segment', 'segment_1');
</code></pre>
The code below returns in debug bar new item like this:

> segment_1: 14.14/3/1

The digits there ara time, queries and how many times this code was implemented.

**References menu**

The default one item of this menu is Errors. It shows notices and warnings including case when `WP_DEBUG` is false.

And also you can easy add your item to this menu and print there any var_dump or anything else.

<pre><code>add_filter('wp_debug_refs', 'my_debug_tool_ref');

function my_debug_tool_ref ($refs) {

    global $post;

    $refs['my_ref_id'] = array(
        'title' => 'My ref',
        'content' => '<b>My ref</b><br />' . var_export($post, true),
    );
    return $refs;
}

</code></pre>


== Installation ==

Debug Tool is easy to install and configure.

1. Upload the `easyazon` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set WP_DEBUG constant if you want to see the bar at front side when you are logged out.

== Screenshots ==

1. Page with debug bar
2. Error modal
3. Queries modal

== Changelog ==

= 1.2 =
*Release Date - 18th March, 2017*

* Add sorting to queries table

= 1.1 =
*Release Date - 1st March, 2017*

* Add queries reference tab

= 1.0 =
*Release Date - 15th February, 2017*

* Initial release