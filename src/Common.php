<?php

namespace Leadhub;

class Common {
    public static function pretty_fold_text($text, $divide, $ratio = .5) {
        $words = explode(" ", $text);

        $put = "";
        $inc = 0;

        while(strlen($put) < strlen($text) * $ratio) {
            $put .= $words[$inc];
            $put .= " ";

            $inc++;
        }

        $put .= $divide;

        $put .= implode(" ", array_slice($words, $inc));

        return $put;
    }

    public static function clean_phone_number($n) {
        return preg_replace('|[^0-9]|', '', $n);
    }

    public static function format_phone_number($n) {
        return preg_replace('|[0-9]?([0-9]{3})([0-9]{3})([0-9]{4})$|', '($1) $2-$3', Common::clean_phone_number($n));
    }

    public static function get_attachment_image_alt($attachment_id, $null = false) {
        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

        if(!$alt) {
            $post = get_post($attachment_id);

            if($post) {
                $alt = $post->post_title;
            }

            else {
                return $null ? null : ""; // nope
            }
        }

        return $alt;
    }

    public static function get_retina_image($attachment_id, $sizes, $class = "") {
        if($class) {
            $class = "class='$class'";
        }

        $alt = Common::get_attachment_image_alt($attachment_id, true);

        if(null === $alt) {
            return ""; // nope
        }

        $srcstack = array();
        $src = ''; $width = 0; $height = 0;

        $first = true;

        foreach($sizes as $times => $size) {
            $image = wp_get_attachment_image_src($attachment_id, $size);

            if(!$image) {
                return "";
            }

            if($first) {
                $src = $image[0];
                $width = $image[1];
                $height = $image[2];
            }

            $first = false;

            array_push($srcstack, $image[0] . " " . $times);
        }

        $srcset = implode(", ", $srcstack);

        return "<img src='$src' srcset='$srcset' alt='$alt' width='$width' height='$height' $class />";
    }

    public static function fix_carbon_wysiwyg() {
        echo "<style>.carbon-wysiwyg iframe { min-height: 200px; }</style>";
    }

    public static function register_post_type($name, $plural, $args=array(), $prefix = "") {
        $uppercase = ucwords($name);
        $plural_uppercase = ucwords($plural);

        $menu_page = LLP_MENU_PAGE;

        $labels = array(
            'name' => $plural_uppercase,
            'singular_name' => $uppercase,
            'menu_name' => $plural_uppercase,
            'add_new_item' => "Add new $uppercase",
            'edit_item' => "Edit $uppercase",
            'view_item' => "View $uppercase",
            "all_items" => "$plural_uppercase",
            "search_items" => "Search $plural",
            "not_found" => "No $plural found",
        );

        $args = wp_parse_args($args, array(
            'labels' => $labels,
            'public' => true,
            'rewrite' => array(
                'slug' => sanitize_title($plural),
                'with_front' => true,
            ),
            'show_ui' => true,
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'show_in_menu' => true,
            'show_in_nav_menus' => true
        ));

        register_post_type($prefix . Common::post_type_slug($name), $args);
    }

    public static function post_type_slug($name) {
        $base = str_replace("-", "_", sanitize_title($name));
        return apply_filters('lh_post_type_slug-' . $base, $base);
    }

    public static function get_youtube_embed_url($url, $autoplay = false) {
        $link = 'https://youtube.com/embed/' . llp_get_youtube_id($url) . '?rel=0';

        if($autoplay) {
            $link .= '&autoplay=1';
        }

        return $link;
    }

    public static function get_youtube_id($url) {
        return preg_replace('|.*v=([A-Za-z0-9_-]+)$|', '$1', $url);
    }
}
