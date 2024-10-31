<?php
defined('ABSPATH') || exit;

class PMGExtension extends DiviExtension
{

    public $gettext_domain = 'paw-masonry-gallery';

    public $name = 'paw-masonry-gallery';

    public $version = PMG_VERSION;

    public function __construct($name = 'paw-masonry-gallery', $args = [])
    {
        $this->plugin_dir     = plugin_dir_path(__FILE__);
        $this->plugin_dir_url = plugin_dir_url($this->plugin_dir);
        parent::__construct($name, $args);
    }

    public function wp_hook_enqueue_scripts()
    {
    }
}

new PMGExtension;
