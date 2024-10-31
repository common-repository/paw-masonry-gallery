<?php
defined('ABSPATH') || exit;

class PMG_Gallery extends ET_Builder_Module
{
    protected $settings;

    const MAIN_CSS_ELEMENT = '%%order_class%%.paw-masonry-gallery';

    protected $module_credits = [
        'module_uri' => 'https://divitorque.com',
        'author'     => 'Divi Torque',
        'author_uri' => 'https://divitorque.com',
    ];

    public function init()
    {

        $this->name             = esc_html__('Masonry Gallery', 'paw-masonry-gallery');
        $this->slug             = 'divi_pro_gallery';
        $this->vb_support       = 'on';
        $this->main_css_element = '%%order_class%%';
        $this->icon_path        = plugin_dir_path(__FILE__) . './pro-gallery.svg';

        $this->settings_modal_toggles = [
            'general'  => [
                'toggles' => [
                    'general'     => esc_html__('General', 'paw-masonry-gallery'),
                    'caption'     => esc_html__('Captions', 'paw-masonry-gallery'),
                    'lightbox'    => esc_html__('Lightbox', 'paw-masonry-gallery'),
                ],
            ],
            'advanced' => [
                'toggles' => [
                    'overlay'     => esc_html__('Overlay', 'paw-masonry-gallery'),
                    'items'        => esc_html__('Items', 'paw-masonry-gallery'),
                    'image'        => esc_html__('Image', 'paw-masonry-gallery'),
                    'caption'      => esc_html__('Caption', 'paw-masonry-gallery'),
                    'caption_text' => [
                        'title'             => esc_html__('Caption Text', 'paw-masonry-gallery'),
                        'tabbed_subtoggles' => true,
                        'sub_toggles'       => [
                            'caption_title' => [
                                'name' => esc_html__('Title', 'paw-masonry-gallery'),
                            ],
                            'caption_desc'  => [
                                'name' => esc_html__('Description', 'paw-masonry-gallery'),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->custom_css_fields = [
            'gallery' => [
                'label'    => esc_html__('Gallery', 'paw-masonry-gallery'),
                'selector' => '.paw-masonry-gallery',
            ],

            'items'   => [
                'label'    => esc_html__('Items', 'paw-masonry-gallery'),
                'selector' => '.pmg-items',
            ],

            'item'    => [
                'label'    => esc_html__('Item', 'paw-masonry-gallery'),
                'selector' => '.pmg-item',
            ],

            'image'   => [
                'label'    => esc_html__('Image', 'paw-masonry-gallery'),
                'selector' => '.pmg-item .pic',
            ],
        ];
    }

    public function get_fields()
    {
        $general = [

            'gallery_ids'     => [
                'label'            => __('Choose Images', 'paw-masonry-gallery'),
                'description'      => __('Choose the images that you would like to appear in the image gallery.', 'paw-masonry-gallery'),
                'type'             => 'upload-gallery',
                'option_category'  => 'basic_option',
                'toggle_slug'      => 'general',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

            'image_size'      => [
                'label'            => __('Image Size', 'paw-masonry-gallery'),
                'type'             => 'select',
                'description'      => __(' Select the size of your images.', 'paw-masonry-gallery'),
                'default'          => 'medium',
                'options'          => self::_get_image_sizes(),
                'option_category'  => 'basic_option',
                'toggle_slug'      => 'general',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

            '_layout'         => [
                'type'        => 'pmg_separator',
                '_text'       => __('Gallery Layout', 'paw-masonry-gallery'),
                'toggle_slug' => 'general',
            ],

            'gallery_type'    => [
                'label'            => __('Gallery Type', 'paw-masonry-gallery'),
                'type'             => 'pmg_layout_selector',
                'default'          => 'masonry',
                'options'          => [
                    'masonry'   => __('Masonry', 'paw-masonry-gallery'),
                    'grid'      => __('Grid', 'paw-masonry-gallery'),
                    'highlight' => __('Highlight', 'paw-masonry-gallery'),
                    'slider'    => __('Slider', 'paw-masonry-gallery'),
                ],
                'toggle_slug'      => 'general',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

            'columns'         => [
                'label'            => __('Columns', 'paw-masonry-gallery'),
                'toggle_slug'      => 'general',
                'type'             => 'select',
                'default'          => '4',
                'options'          => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'mobile_options'   => true,
                'validate_unit'    => true,
                'option_category'  => 'layout',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

            'gutter'          => [
                'label'            => __('Spacing', 'paw-masonry-gallery'),
                'toggle_slug'      => 'general',
                'type'             => 'text',
                'option_category'  => 'basic_option',
                'default'          => 20,
                'default_on_front' => 20,
                'mobile_options'   => true,
                'computed_affects' => [
                    '__gallery',
                ],
                'validate_unit'    => false,
            ],

        ];

        $overlay = [
            'overlay_bg_color' => [
                'label'          => esc_html__('Overlay Color', 'paw-masonry-gallery'),
                'type'           => 'color-alpha',
                'default'        => '',
                'custom_color'   => true,
                'mobile_options' => true,
                'tab_slug'       => 'advanced',
                'toggle_slug'    => 'overlay',
            ],

            'overlay_bg_opacity' => [
                'label'            => esc_html__('Opacity', 'paw-masonry-gallery'),
                'type'             => 'range',
                'option_category'  => 'font_option',
                'range_settings'   => [
                    'min'  => '0.1',
                    'max'  => '1',
                    'step' => '0.1',
                ],
                'mobile_options'   => true,
                'tab_slug'       => 'advanced',
                'toggle_slug'    => 'overlay',
            ],
        ];

        $click_action = [

            'click_action'     => [
                'label'            => __('Lightbox & Links', 'paw-masonry-gallery'),
                'type'             => 'select',
                'default'          => 'lightbox',
                'options'          => [
                    'no-link'    => esc_html__('No Link', 'paw-masonry-gallery'),
                    'file'       => esc_html__('Media File', 'paw-masonry-gallery'),
                    'attachment' => esc_html__('Attachment Page', 'paw-masonry-gallery'),
                    'lightbox'   => esc_html__('Lightbox', 'paw-masonry-gallery'),
                ],
                'option_category'  => 'basic_option',
                'toggle_slug'      => 'lightbox',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

        ];

        $caption = [
            'hide_caption'     => [
                'label'            => esc_html__('Hide Caption', 'paw-masonry-gallery'),
                'type'             => 'yes_no_button',
                'options'          => [
                    'on'  => __('yes', 'paw-masonry-gallery'),
                    'off' => __('no', 'paw-masonry-gallery'),
                ],
                'default'          => 'off',
                'toggle_slug'      => 'caption',
                'computed_affects' => [
                    '__gallery',
                ],
            ],

            'hide_title'       => [
                'label'            => esc_html__('Hide Title', 'paw-masonry-gallery'),
                'type'             => 'yes_no_button',
                'options'          => [
                    'on'  => __('yes', 'paw-masonry-gallery'),
                    'off' => __('no', 'paw-masonry-gallery'),
                ],
                'default'          => 'off',
                'toggle_slug'      => 'caption',
                'computed_affects' => [
                    '__gallery',
                ],
                'show_if'          => [
                    'hide_caption' => 'off',
                ],
            ],

            'hide_description' => [
                'label'            => esc_html__('Hide Description', 'paw-masonry-gallery'),
                'type'             => 'yes_no_button',
                'options'          => [
                    'on'  => __('yes', 'paw-masonry-gallery'),
                    'off' => __('no', 'paw-masonry-gallery'),
                ],
                'default'          => 'on',
                'toggle_slug'      => 'caption',
                'computed_affects' => [
                    '__gallery',
                ],
                'show_if'          => [
                    'hide_caption' => 'off',
                ],
            ],
        ];

        $computed = [
            '__gallery' => [
                'type'                => 'computed',
                'computed_callback'   => ['PMG_Gallery', 'gallery_html'],
                'computed_depends_on' => [
                    'gallery_ids',
                    'image_size',
                    'columns',
                    'click_action',
                    'gutter',
                    'hide_caption',
                    'hide_title',
                    'hide_description',
                ],
            ],
        ];

        return array_merge(
            $general,
            $caption,
            $overlay,
            $click_action,
            $computed
        );
    }

    public function get_advanced_fields_config()
    {

        $advanced_fields                 = [];
        $advanced_fields['text']         = [];
        $advanced_fields['text_shadow']  = [];
        $advanced_fields['fonts']        = [];
        $advanced_fields['borders']      = [];
        $advanced_fields['box_shadow']   = [];
        $advanced_fields['link_options'] = [];
        $advanced_fields['button']       = [];

        $advanced_fields['borders']['items'] = [
            'css'         => [
                'main' => [
                    'border_radii'  => '%%order_class%% .pmg-item',
                    'border_styles' => '%%order_class%% .pmg-item',
                ],
            ],
            'toggle_slug' => 'items',
        ];

        $advanced_fields['fonts']['caption_title'] = [
            'label'           => esc_html__('Title', 'paw-masonry-gallery'),
            'css'             => [
                'main' => "%%order_class%% .pmg-item-content h2",
            ],
            'font_size'       => [
                'default' => '18px',
            ],
            'line_height'     => [
                'default' => '1.2em',
            ],
            'hide_text_align' => true,
            'toggle_slug'     => 'caption_text',
            'sub_toggle'      => 'caption_title',
        ];

        $advanced_fields['fonts']['caption_desc'] = [
            'label'           => esc_html__('Description', 'paw-masonry-gallery'),
            'css'             => [
                'main' => "%%order_class%% .pmg-item-content p",
            ],
            'font_size'       => [
                'default' => '14px',
            ],
            'line_height'     => [
                'default' => '1.5em',
            ],
            'hide_text_align' => true,
            'toggle_slug'     => 'caption_text',
            'sub_toggle'      => 'caption_desc',
        ];

        $advanced_fields['borders']['image'] = [
            'label_prefix' => esc_html__('Image', 'paw-masonry-gallery'),
            'css'          => [
                'main' => [
                    'border_radii'  => '%%order_class%% .pmg-items .pmg-item img, %%order_class%% .pmg-items .pmg-item .pic',
                    'border_styles' => '%%order_class%% .pmg-items .pmg-item img, %%order_class%% .pmg-items .pmg-item .pic',
                ],
            ],
            'tab_slug'     => 'advanced',
            'toggle_slug'  => 'image',
        ];

        $advanced_fields['filters'] = [
            'css'                  => [
                'main' => '%%order_class%%',
            ],

            'child_filters_target' => [
                'tab_slug'    => 'advanced',
                'toggle_slug' => 'image',
            ],
        ];

        return $advanced_fields;
    }

    static function gallery_html($args = [], $conditional_tags = [], $current_page = [])
    {

        $gallery = new self();

        $gallery->props = $args;

        $output = $gallery->gallery_handler([], [], $current_page);

        return $output;
    }

    public function gallery_handler($args = [], $conditional_tags = [], $current_page = [])
    {

        foreach ($args as $arg => $value) {
            $this->props[$arg] = $value;
        }

        if (empty($this->props['gallery_ids'])) {
            return sprintf('%s', esc_html__('Gallery not found.', 'paw-masonry-gallery'));
        }

        $gallery_items = get_posts([
            'include'        => $this->props['gallery_ids'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => 'ASC',
            'orderby'        => 'post__in',
        ]);

        if (empty($gallery_items)) {
            return sprintf('%s', esc_html__('Gallery not found.', 'paw-masonry-gallery'));
        }

        ob_start();

        foreach ($gallery_items as $image) {

            $image_object = get_post($image->ID);
            $thumb        = wp_get_attachment_image_src($image->ID, 'thumbnail');
            $image_full   = wp_get_attachment_image_src($image->ID, 'full');
            $image_src    = wp_get_attachment_image_src($image->ID, $this->props['image_size']);

            $image_data = [
                'id'              => $image->ID,
                'title'           => $image_object->post_title,
                'description'     => $image_object->post_content,
                'caption'         => wp_get_attachment_caption($image->ID),
                'image_full'      => esc_url($image->guid),
                'img_classes'     => ['image', 'pic', 'wp-image-' . $image->ID],
                'item_classes'    => ['pmg-item'],
                'link_attributes' => [],
                'link_classes'    => ['pmg-item-link'],
                'click_action'    => $this->props['click_action'],
            ];

            if ($thumb) {
                $image_data['img_thumb'] = esc_url($thumb[0]);
            }

            if ($image_full) {
                $image_data['image_full'] = esc_url($image_full[0]);
            }

            if ($image_src) {
                $image_data['image_src'] = esc_url($image_src[0]);
            }


            if ('no-link' !== $this->props['click_action']) {

                if ('file' === $this->props['click_action']) {
                    $image_data['link_attributes']['href'] = $image_full ? esc_url($image_full[0]) : esc_url($image_src[0]);
                }

                if ('attachment' === $this->props['click_action']) {
                    $image_data['link_attributes']['href'] = get_attachment_link($image->ID);
                }

                if ('lightbox' === $this->props['click_action']) {
                    $image_data['link_attributes']['href']       = $image_full ? esc_url($image_full[0]) : esc_url($image_src[0]);
                    $image_data['link_attributes']['class'][]    = 'pmg-lightbox-link';
                    $image_data['link_attributes']['aria-label'] = esc_html__('Open image', 'paw-masonry-gallery');
                    $image_data['link_attributes']['title']      = esc_html__('Open image', 'paw-masonry-gallery');

                    // Enable lightbox.
                    $image_data['link_attributes']['data-fancybox'] = 'pmg-gallery';
                }
            }

            include PMG_PLUGIN_DIR . 'includes/views/item.php';
        }

        $gallery_html = ob_get_clean();
        $grid_sizer = '<div class="grid-sizer"></div> <div class="gutter-sizer"></div>';

        return wp_kses_post($grid_sizer . $gallery_html);
    }

    public function render($attrs, $content, $render_slug)
    {

        wp_enqueue_script('pmg-frontend');
        wp_enqueue_style('pmg-frontend');

        $this->generate_css($render_slug);

        $columns = self::_get_responsive_options('columns', $this->props);

        $data_config = [
            'columnsResponsive' => $columns['responsive_status'],
            'columns'           => absint($columns['desktop']),
            'columnsTablet'     => absint($columns['tablet']),
            'columnsPhone'      => absint($columns['phone']),
            'gutter'            => absint($this->props['gutter']),
        ];

        $data_config = "data-config=" . json_encode($data_config);

        $output = sprintf(
            '<div class="paw-masonry-gallery" %2$s>
                %1$s 
            </div>',
            $this->gallery_handler(),
            $data_config
        );


        return $output;
    }

    public function generate_css($render_slug)
    {

        $gutter  = $this->props['gutter'] ? intval($this->props['gutter']) : 20;
        $columns = self::_get_responsive_options('columns', $this->props);

        ET_Builder_Element::set_style(
            $render_slug,
            [
                'selector'    => '%%order_class%% .pmg-item',
                'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['desktop']} - 1 )) / {$columns['desktop']});",
            ]
        );

        if ($columns['responsive_status']) {
            ET_Builder_Element::set_style(
                $render_slug,
                [
                    'selector'    => '%%order_class%% .pmg-item',
                    'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['tablet']} - 1 )) / {$columns['tablet']});",
                    'media_query' => ET_Builder_Element::get_media_query('max_width_980'),
                ]
            );

            ET_Builder_Element::set_style(
                $render_slug,
                [
                    'selector'    => '%%order_class%% .pmg-item',
                    'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['phone']} - 1 )) / {$columns['phone']});",
                    'media_query' => ET_Builder_Element::get_media_query('max_width_767'),
                ]
            );
        }

        ET_Builder_Element::set_style(
            $render_slug,
            [
                'selector'    => '%%order_class%% .grid-sizer',
                'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['phone']} - 1 )) / {$columns['phone']});",
            ]
        );

        if ($columns['responsive_status']) {
            ET_Builder_Element::set_style(
                $render_slug,
                [
                    'selector'    => '%%order_class%% .grid-sizer',
                    'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['tablet']} - 1 )) / {$columns['tablet']});",
                    'media_query' => ET_Builder_Element::get_media_query('max_width_980'),
                ]
            );

            ET_Builder_Element::set_style(
                $render_slug,
                [
                    'selector'    => '%%order_class%% .grid-sizer',
                    'declaration' => "width: calc((100% - {$gutter}px * ( {$columns['phone']} - 1 )) / {$columns['phone']});",
                    'media_query' => ET_Builder_Element::get_media_query('max_width_767'),
                ]
            );
        }

        ET_Builder_Element::set_style(
            $render_slug,
            [
                'selector'    => '%%order_class%% .gutter-sizer',
                'declaration' => "width: {$gutter}px;",
            ]
        );

        ET_Builder_Element::set_style(
            $render_slug,
            [
                'selector'    => '%%order_class%% .pmg-item',
                'declaration' => "margin-bottom: {$gutter}px;",
            ]
        );


        $this->generate_styles(
            [
                'hover'          => false,
                'base_attr_name' => 'overlay_bg_color',
                'selector'       => '%%order_class%% .pmg-item:hover .pmg-item-overlay',
                'css_property'   => ['background-color'],
                'render_slug'    => $render_slug,
            ]
        );

        $this->generate_styles(
            [
                'base_attr_name' => 'overlay_bg_opacity',
                'selector'       => '%%order_class%% .pmg-item:hover .pmg-item-content img.pic',
                'css_property'   => ['opacity'],
                'render_slug'    => $render_slug,
                'hover'          => false,
            ]
        );
    }

    public function multi_view_filter_value($raw_value, $args)
    {

        $name = isset($args['name']) ? $args['name'] : '';
        if ($raw_value && 'font_icon' === $name) {
            return et_pb_get_extended_font_icon_value($raw_value, true);
        }

        return $raw_value;
    }

    public static function _get_image_sizes()
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

        $sizes['full'] = __('Full', 'paw-masonry-gallery');

        return $sizes;
    }

    public static function _get_responsive_options($option_name, $props)
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

new PMG_Gallery();
