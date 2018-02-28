# Debug Tool - Wordpress

Tags: debug, debugger, developer, query, queries, wp debug, debug bar
Contributors: eugenbobrowski
Tested up to: 4.7.2
Requires at least: 3.3
Stable Tag: 1.2
License: GPLv2 or later


Show helpful debug bar at front and admin side. Tool for developers.

## Description

This is tool that help developers and administrators easier getting information.

The debug bar is available on front side for everyone if WP_DEBUG is true. 
If you are admin you can call bar on any page by keys Ctrl+Alt+Shift+D.

  
### Features:
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

```php
do_action('check_segment', 'segment_1');

//do somethisng

do_action('check_segment', 'segment_1');
```
The code below returns in debug bar new item like this:

> segment_1: 14.14/3/1

It means that `segment_1` was implementing 14.14 ms once time. Had 3 requests to database via `$wpdb`. 

The digits there ara mean time, queries and how many times this code was implemented.

**References menu**

This is a menu you can customize and add your own reference item. 
The default one item of this menu is "Errors" that shows notices and warnings including case when `WP_DEBUG` is false.

If you cant se this menu you are happy because there are no errors to show. ... And you didn't add your own item to this menu. 

And also you can easy add your item to this menu and print there any var_dump or anything else. 
 
```php
add_filter('wp_debug_refs', 'my_debug_tool_ref');

function my_debug_tool_ref ($refs) {
    
    global $post;
    
    $refs['my_ref_id'] = array(
        'title' => 'My ref',
        'content' => '<b>My ref</b><br />' . var_export($post, true),
    );
    return $refs;
}

```




## Installation

DebugTool is easy to install and configure.

1. Upload the `debug-tool` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set WP_DEBUG constant if you want to see the bar at front side when you are logged out.


## Frequently Asked Questions

### How to check code segment via Debug tool

### How to add my info to debug bar menu

### How to turn on debug at production site


## Screenshots

## Changelog

### 1.2
*Release Date - 18th March, 2017*

* Add sorting to queries table

### 1.1
*Release Date - 1st March, 2017*

* Add queries reference tab

### 1.0
*Release Date - 15th February, 2017*

* Initial release
