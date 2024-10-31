<?php

namespace TorqueMasonryGallery;

class Helper
{
    public static function render_attributes($attributes)
    {

        $return = '';

        foreach ($attributes as $name => $value) {

            if (is_array($value) && 'class' == $name) {
                $value = implode(' ', $value);
            } elseif (is_array($value)) {
                $value = wp_json_encode($value);
            }

            if (in_array($name, array('alt', 'rel', 'title'))) {
                $value = str_replace('<script', '&lt;script', $value);
                $value = wp_strip_all_tags(htmlspecialchars($value));
                $value = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $value);
            }

            $return .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
        }

        return $return;
    }

    public static function get_image_sizes()
    {

        $default_image_sizes = ['thumbnail', 'medium', 'medium_large', 'large'];
        $image_sizes         = [];
        foreach ($default_image_sizes as $size) {
            $image_sizes[$size] = [
                'width'  => (int) get_option($size . '_size_w'),
                'height' => (int) get_option($size . '_size_h'),
                'crop'   => (bool) get_option($size . '_crop'),
            ];
        }

        $sizes = [];

        foreach ($image_sizes as $size_key => $size_attributes) {
            $control_title    = ucwords(str_replace('_', ' ', $size_key));
            $sizes[$size_key] = $control_title;
        }

        $sizes['full'] = __('Full', 'divi-pro-gallery');

        return $sizes;
    }

    public static function get_responsive_options($option_name, $props)
    {

        $option                = [];
        $last_edited           = $props["{$option_name}_last_edited"];
        $get_responsive_status = et_pb_get_responsive_status($last_edited);
        $is_responsive_enabled = isset($last_edited) ? $get_responsive_status : false;
        $option_name_tablet    = "{$option_name}_tablet";
        $option_name_phone     = "{$option_name}_phone";

        $option["responsive_status"] = $is_responsive_enabled ? true : false;

        if ($is_responsive_enabled && !empty($props[$option_name_tablet])) {
            $option["tablet"] = $props[$option_name_tablet];
        } else {
            $option["tablet"] = $props[$option_name];
        }

        if ($is_responsive_enabled && !empty($props[$option_name_phone])) {
            $option["phone"] = $props[$option_name_phone];
        } else {
            $option["phone"] = $props[$option_name];
        }

        $option["desktop"] = $props[$option_name];

        return $option;
    }
}

Helper::render_attributes(['class' => 'asdfasdf']);
