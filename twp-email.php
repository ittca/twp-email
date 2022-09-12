<?php  /*
  Plugin Name: twp email
  Plugin URI:
  Description: simple smtp mail setup for wordpress
  Author: Tiago Anastácio
  Author URI: ittca.eu
  Version: 1.3.3
  Tags: twp, email, smtp
  Requires at least: 5.5
  Tested up to: 6.0.2
  Requires PHP: 7.4
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if(!defined('ABSPATH'))exit;
define('TWPemailV',"1.3.3");
if(!function_exists('twpEmailFiles')){
  function twpEmailFiles() {wp_enqueue_style('twpe_style', plugins_url('style.css',__FILE__ ));}
  add_action( 'admin_init','twpEmailFiles');
}
if(!function_exists('twped_csm')){
  function twped_csm($slug){
    global $submenu;if($submenu['twp_editor']){foreach($submenu['twp_editor'] as $subm){if($subm[2]==$slug)return 1;}}
  }
}
if (!function_exists('twpDashboradPage')){
  function twpEmailMenu(){
    if(empty($GLOBALS['admin_page_hooks']['twp_editor'])){
      add_menu_page('twp editor','twp','manage_options','twp_editor','twpMain','dashicons-welcome-widgets-menus',99);
    }
    if(empty(twped_csm('twp_about'))){add_submenu_page('twp_editor', 'twp about', __('About'), 'manage_options','twp_about','twpe_About');}
    if(empty(twped_csm('twp_email'))){add_submenu_page('twp_editor', 'twp email', __('Email'), 'manage_options','twp_email','twpe_Email');}
    remove_submenu_page('twp_editor', 'twp_editor');
  }
  function twpe_About(){require_once plugin_dir_path(__FILE__).'adm/about.php';}
  function twpe_Email(){require_once plugin_dir_path(__FILE__).'adm/email.php';}
  add_action('admin_menu', 'twpEmailMenu');
}
if(! function_exists('twpe_settings')){
  function twpe_settings( $links ) {
  	$links[] = '<a href="'.admin_url('admin.php?page=twp_about').'">'.__('Settings').'</a>';
  	return $links;
  }
  add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'twpe_settings');
}
if(!function_exists('twpe_uninstall')){
  function twpe_uninstall(){
    global $wpdb;
    $r=$wpdb->get_results('select d from '.$wpdb->prefix.'twpEmail where id=1');
    if($r[0]->d){$wpdb->query('drop table '.$wpdb->prefix.'twpEmail');}
  }
  register_uninstall_hook( __FILE__, 'twpe_uninstall' );
}
function twpdecr($a){global $wpdb;$r=$wpdb->get_results('select * from '.$wpdb->prefix.'twpEmail');return openssl_decrypt($a,$r[0]->b,$r[0]->c);}
function twpencr($a){global $wpdb;$r=$wpdb->get_results('select * from '.$wpdb->prefix.'twpEmail');return openssl_encrypt($a,$r[0]->b,$r[0]->c);}
if (! function_exists('twpe_smtp_email')){
 function twpe_smtp_email($mail){
   global $wpdb;
   $result = $wpdb->get_results('select * from '.$wpdb->prefix.'twpEmail');
	 $mail->SetFrom($result[0]->email, $result[0]->fromname);
	 $mail->Host = $result[0]->host;
	 $mail->Port = $result[0]->port;
	 $mail->SMTPAuth = true;
	 $mail->SMTPSecure = $result[0]->secure;
	 $mail->Username = $result[0]->email;
	 $mail->Password = twpdecr($result[0]->pass);
	 $mail->IsSMTP();
 }
 add_action( 'wp_mail_failed', function ( $error ) {
   if(is_admin() && current_user_can('administrator')){ ?>
     <div id="twperrormsg" class="notice notice-error is-dismissible">
       <p><strong><?php echo print_r($error->get_error_message()) ?></strong></p><button type="button" class="notice-dismiss" onclick="twperrormsg()"></button>
     </div><?php
   }
 });
 add_action('phpmailer_init','twpe_smtp_email');
}
if(!function_exists('twpe_about_title')){
  function twpe_about_title(){echo __("Email").'&emsp;'; }
  add_action('twp_about_title', 'twpe_about_title');
}
if(!function_exists('twpe_about_body')){
  function twpe_about_body(){ require 'adm/abouttwp.php';}
  add_action('twp_about_body', 'twpe_about_body');
}
