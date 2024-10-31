<?php
/**
 * @package MyRssPluginWidget
 * @version 1.0.0
 */
/*
Plugin Name: My RSS Plugin
Plugin URI: http://someuser.me/my-rss-plugin-hp.html
Description: Plugin to show RSS any RSS feed, admin can set it to widget area, set its title and number of records.
Author: someuser
Version: 1.0.0
Author URI: http://someuser.me
*/

define(myrssplugin_URL_RSS_DEFAULT, 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/limit=10/xml');
define(myrssplugin_TITLE, 'Top Ten Songs');
define(myrssplugin_MAX_SHOWN_ITEMS, 10);

function myrssplugin_widget_Init(){
  register_widget('MyRssPluginWidget');
}
	
add_action("widgets_init", "myrssplugin_widget_Init");
	
class MyRssPluginWidget extends WP_Widget {
     function MyRssPluginWidget() {
       //Widget code
	   parent::WP_Widget(false,$name="My RSS Plugin");
     }

     function widget($args, $instance) {
       //Widget output
		if( file_exists( ABSPATH . WPINC . '/rss.php') ) {
			require_once(ABSPATH . WPINC . '/rss.php');		
		} else {
			require_once(ABSPATH . WPINC . '/rss-functions.php');
		}
		$options = $instance;
		if( $options == false ) {
			$options[ 'myrssplugin_widget_url'] = myrssplugin_URL_RSS_DEFAULT;
			$options[ 'myrssplugin_widget_RSS_count_items' ] = myrssplugin_MAX_SHOWN_ITEMS;
		}
		$RSSurl = $options[ 'myrssplugin_widget_url'];
		$messages = fetch_rss($RSSurl);
		$messages_count = count($messages->items);
		if($messages_count != 0){
			$output .= '<b>'.$options['myrssplugin_widget_title'].':</b>';	
			$output .= '<ul>';		
			for($i=0; $i<$options['myrssplugin_widget_RSS_count_items'] && $i<$messages_count; $i++){
				$output .= '<li>';
					$output .= '<a target="_blank" href="'.$messages->items[$i]['link'].'">'.$messages->items[$i]['title'].'</a></span>';						
					$output .= '</li>';
			}
			$output .= '</ul>';
		}	
		extract($args);	
		echo $before_widget; 
		echo $before_title . $title . $after_title;
		echo $output; 
		echo $after_widget;
     }

     function update($new_instance, $old_instance) {
       //Save widget options
		$instance = $old_instance;
		$instance['myrssplugin_widget_title'] = strip_tags($new_instance['myrssplugin_widget_title']);
		$instance['myrssplugin_widget_url'] = strip_tags($new_instance['myrssplugin_widget_url']);
		$instance['myrssplugin_widget_RSS_count_items'] = strip_tags($new_instance['myrssplugin_widget_RSS_count_items']);
		return $instance;
     }

     function form($instance) {
       //Output admin widget options form
		$instance = wp_parse_args( (array) $instance, array(
		'myrssplugin_widget_title'=>myrssplugin_TITLE,
		'myrssplugin_widget_url'=>myrssplugin_URL_RSS_DEFAULT,
		'myrssplugin_widget_RSS_count_items'=>myrssplugin_MAX_SHOWN_ITEMS,
		) );
		$myrssplugin_widget_title = htmlspecialchars($instance['myrssplugin_widget_title'], ENT_QUOTES);
		$myrssplugin_widget_url = htmlspecialchars($instance['myrssplugin_widget_url'], ENT_QUOTES);
		$myrssplugin_widget_RSS_count_items = htmlspecialchars($instance['myrssplugin_widget_RSS_count_items'], ENT_QUOTES);			
		
	   ?>
<p><label for="myrssplugin_widget_title"><?php _e('RSS Title:'); ?> <input  id="<?php echo  $this->get_field_id('myrssplugin_widget_title');?>" name="<?php echo  $this->get_field_name('myrssplugin_widget_title');?>" type="text" value="<?php echo $myrssplugin_widget_title; ?>" /></label></p>

	<p><label for="myrssplugin_widget_url"><?php _e('RSS URL:'); ?> <input  id="<?php echo  $this->get_field_id('myrssplugin_widget_url');?>" name="<?php echo  $this->get_field_name('myrssplugin_widget_url');?>" type="text" value="<?php echo $myrssplugin_widget_url; ?>" /></label></p>
 
	<p><label for="myrssplugin_widget_RSS_count_items"><?php _e('Count Items To Show:'); ?> <input  id="myrssplugin_widget_RSS_count_items" name="<?php echo  $this->get_field_name('myrssplugin_widget_RSS_count_items');?>" size="2" maxlength="2" type="text" value="<?php echo $myrssplugin_widget_RSS_count_items;?>" /></label></p>
	
	   <?php
     }
}


?>
