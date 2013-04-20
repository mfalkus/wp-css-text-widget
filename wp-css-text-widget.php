<?php
/*
Plugin Name: CSS-Class Text Widget
Plugin URI: http://falkus.co
Description: Heavily based on the built in Text Widget plugin, but with more flexibility
Version: 1.0
Author: Martin Falkus
Author URI: http://falkus.co
License: GPL2
*/

/*  Copyright 2013 Martin Falkus  (email : martin@falkus.co)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * CSS-Class Text Widget class
 */
class CSS_Class_Text_Widget extends WP_Widget {

    function __construct() {
        $widget_ops = array(
                        'classname' => 'css_class_text_widget',
                        'description' => 'Arbitrary text or HTML, with a custom CSS class on the outter element'
                      );
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct( false, 'CSS-Class Text Widget', $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
        $css_class = empty( $instance['css-class'] ) ? '' : ' '.$instance['css-class'];
        echo $before_widget; ?>
            <div class="textwidget<?php echo $css_class ?>">
                <?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
                <?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
        $instance['filter'] = isset($new_instance['filter']);
        $instance['css-class'] = strip_tags($new_instance['css-class']);
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
        $title = strip_tags($instance['title']);
        $css_class = strip_tags($instance['css-class']);
        $text = esc_textarea($instance['text']);
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        <label for="<?php echo $this->get_field_id('css-class'); ?>">CSS Class: </label><small>Applied to textwidget div, after the title.</small>
        <input class="widefat" id="<?php echo $this->get_field_id('css-class'); ?>" name="<?php echo $this->get_field_name('css-class'); ?>" type="text" value="<?php echo esc_attr($css_class); ?>" /></p>

        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
    }
}

/**
 * Register our widget, hooked on to widgets_init
 */
function css_class_text_widget_register() {
    register_widget('CSS_Class_Text_Widget');
}

add_action('widgets_init', 'css_class_text_widget_register');
