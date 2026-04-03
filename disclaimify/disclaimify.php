<?php

/**
 * Plugin Name: Disclaimify - Affiliate Disclosure / Disclaimer for WordPress
 * Description: Disclaimify is the ultimate solution to add affiliate disclosure statements & inform your readers about affiliate links while ensuring transparency.
 * Plugin URI:  https://hasthemes.com/plugins/
 * Author:      HasThemes
 * Author URI:  https://hasthemes.com/
 * Version:     1.0.0
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: disclaimify
 * Domain Path: /languages
 */

/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin const
 */
define('DISCLAIMIFY_VERSION', '1.0.0');
define('DISCLAIMIFY_PL_ROOT', __FILE__);
define('DISCLAIMIFY_PL_URL', plugins_url('/', DISCLAIMIFY_PL_ROOT));
define('DISCLAIMIFY_PL_PATH', plugin_dir_path(DISCLAIMIFY_PL_ROOT));
define('DISCLAIMIFY_DIR_URL', plugin_dir_url(DISCLAIMIFY_PL_ROOT));
define('DISCLAIMIFY_PLUGIN_BASE', plugin_basename(DISCLAIMIFY_PL_ROOT));

/**
 * Require Files
 */
require_once (DISCLAIMIFY_PL_PATH . 'includes/base.php');
