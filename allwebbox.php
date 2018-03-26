<?php
/*
Plugin Name: allwebbox
Plugin URI: https://larasoftbd.com/plugins
Description: Plugin social and survey rating.
Author: Omar
Version: 4.8.1
Author URI: https://larasoftbd.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: allwebbox
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }
define('ALWEBDIR', plugin_dir_path( __FILE__ ));
define('ALWEBURL', plugin_dir_url( __FILE__ ));

require_once(ALWEBDIR . 'class/allwebClass.php');
//require_once(ALWEBDIR . 'smtp/wp_mail_smtp.php');
require_once(ALWEBDIR . 'inc/bb-custom-module/fl-custom-module.php');
require_once(ALWEBDIR . 'inc/sendgrid-email-delivery-simplified/wpsendgrid.php');

new Allwebbox();