<?php
/*
Plugin Name: Wp Mail Bank
Plugin URI: http://tech-banker.com
Description: WP Mail Bank reconfigures the wp_mail() function and make it more enhanced.
Author: Tech Banker
Version: 1.6
Author URI: http://tech-banker.com
*/


/////////////////////////////////////  Define  WP Mail Bank Constants  ////////////////////////////////////////

if (!defined("MAIL_BK_PLUGIN_DIR")) define("MAIL_BK_PLUGIN_DIR",  plugin_dir_path( __FILE__ ));
if (!defined("MAIL_BK_PLUGIN_DIRNAME")) define("MAIL_BK_PLUGIN_DIRNAME", plugin_basename(dirname(__FILE__)));
if (!defined("mail_bank")) define("mail_bank", "mail-banker");
if (!defined("tech_bank")) define("tech_bank", "tech-banker");
if (!defined("MAIL_FILE")) define("MAIL_FILE","wp-mail-bank/wp-mail-bank.php");

global $phpmailer;


function create_global_menus_for_mail_bank()
{
	global $wpdb,$current_user;
	if(is_super_admin())
	{
		$role = "administrator";
	}
	else
	{
		$role = $wpdb->prefix . "capabilities";
		$current_user->role = array_keys($current_user->$role);
		$role = $current_user->role[0];
	}
	include MAIL_BK_PLUGIN_DIR . "/lib/wp-include-menus.php";
}
/////////////////////////////////////  Functions for Returing Table Names  ////////////////////////////////////////
function wp_mail_bank()
{
	global $wpdb;
	return $wpdb->prefix . "mail_bank";
}
/////////////////////////////////////  Call CSS & JS Scripts - Back End ////////////////////////////////////////

function backend_plugin_js_scripts_mail_bank()
{
	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery.validate.min.js",  plugins_url("/assets/js/jquery.validate.min.js",__FILE__));
}

function backend_plugin_css_scripts_mail_bank()
{
	wp_enqueue_style("framework.css", plugins_url("/assets/css/framework.css",__FILE__));
	wp_enqueue_style("wp-mail-bank.css", plugins_url("/assets/css/wp-mail-bank.css",__FILE__));
}

function wp_mail_bank_configure($phpmailer) 
{
	global $wpdb;
	$data=$wpdb->get_row
	(
		"SELECT * FROM ".wp_mail_bank()
	);
	$mail_type = $data->mailer_type;
	$phpmailer->Mailer = $data->mailer_type == 0 ? "smtp" : "mail"; 
	$phpmailer->FromName = $data->from_name;
	$phpmailer->From = $data->from_email;
	$phpmailer->Sender =  $data->return_path == 0 ? $data->return_email : $data->from_email;
	$phpmailer->WordWrap = $data->word_wrap;
	if($data->mailer_type == 0)
	{	
		switch($data->encryption)
		{
			case 0 :
				$phpmailer->SMTPSecure ="";
				break;
			case 1 :
				$phpmailer->SMTPSecure ="ssl";
				break;
			case 2 :
				$phpmailer->SMTPSecure ="tls";
				break;
		}
		$phpmailer->Host = $data->smtp_host;
		$phpmailer->Port = $data->smtp_port;
		if($data->smtp_keep_alive == 1)
		{
			$phpmailer->SMTPKeepAlive  = TRUE;
		}
		if($data->authentication == 1)
		{
			$phpmailer->SMTPAuth = TRUE;
			$phpmailer->Username =  $data->smtp_username;
			$phpmailer->Password =  $data->smtp_password;
		}
	}
}

////////////////////////////////////// Class Files Action Functions START here /////////////////////////////////////////
if(isset($_REQUEST["action"]))
{
	switch($_REQUEST["action"])
	{
		case "add_mail_library":
		add_action( "admin_init", "add_mail_library");
		function add_mail_library()
		{
			global $wpdb,$current_user,$user_role_permission;
			if(is_super_admin())
			{
				$role = "administrator";
			}
			else
			{
				$role = $wpdb->prefix . "capabilities";
				$current_user->role = array_keys($current_user->$role);
				$role = $current_user->role[0];
			}
			include MAIL_BK_PLUGIN_DIR . "/lib/add_mail_class_file.php";
		}
		break;
	}
}
/////////////////////////////////////  Call Languages for Multi-Lingual ////////////////////////////////////////

function mail_bank_plugin_load_text_domain()
{
	if (function_exists("load_plugin_textdomain"))
	{
		load_plugin_textdomain(mail_bank, false, MAIL_BK_PLUGIN_DIRNAME . "/lang");
	}
}

/////////////////////////////////////  admin menu////////////////////////////////////////

function add_mail_icon($meta = TRUE)
{
	global $wp_admin_bar, $wpdb, $current_user;
	if(is_super_admin())
	{
		$role = "administrator";
	}
	else
	{
		$role = $wpdb->prefix . "capabilities";
		$current_user->role = array_keys($current_user->$role);
		$role = $current_user->role[0];
	}
	switch ($role)
	{
		case "administrator":
			$wp_admin_bar->add_menu(array(
			"id" => "mail_bank",
			"title" => __("<img src=\"" . plugins_url("/assets/images/mail.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />WP Mail Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=smtp_mail"),
			));
				
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "Settings",
					"href" => site_url() . "/wp-admin/admin.php?page=smtp_mail",
					"title" => __("Settings", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "send_test_email",
					"href" => site_url() . "/wp-admin/admin.php?page=send_test_email",
					"title" => __("Send Test Email", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "recommended",
					"href" => site_url() . "/wp-admin/admin.php?page=recommended_plugins",
					"title" => __("Recommendations", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "other_services",
					"href" => site_url() . "/wp-admin/admin.php?page=other_services",
					"title" => __("Our Other Services", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "wp_system_status",
					"href" => site_url() . "/wp-admin/admin.php?page=mail_system_status",
					"title" => __("System Status", mail_bank))
			);
		break;
		case "editor":
			$wp_admin_bar->add_menu(array(
			"id" => "mail_bank",
			"title" => __("<img src=\"" . plugins_url("/assets/images/mail.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Wp Mail Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=mail_settings"),
			));
				
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "Settings",
					"href" => site_url() . "/wp-admin/admin.php?page=smtp_mail",
					"title" => __("Settings", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "send_test_email",
					"href" => site_url() . "/wp-admin/admin.php?page=send_test_email",
					"title" => __("Send Test Email", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "recommended",
					"href" => site_url() . "/wp-admin/admin.php?page=recommended_plugins",
					"title" => __("Recommendations", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "other_services",
					"href" => site_url() . "/wp-admin/admin.php?page=other_services",
					"title" => __("Our Other Services", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "wp_system_status",
					"href" => site_url() . "/wp-admin/admin.php?page=mail_system_status",
					"title" => __("System Status", mail_bank))
			);
		break;
		case "author":
			$wp_admin_bar->add_menu(array(
			"id" => "mail_bank",
			"title" => __("<img src=\"" . plugins_url("/assets/images/mail.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Wp Mail Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=mail_settings"),
			));
				
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "Settings",
					"href" => site_url() . "/wp-admin/admin.php?page=smtp_mail",
					"title" => __("Settings", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "send_test_email",
					"href" => site_url() . "/wp-admin/admin.php?page=send_test_email",
					"title" => __("Send Test Email", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "recommended",
					"href" => site_url() . "/wp-admin/admin.php?page=recommended_plugins",
					"title" => __("Recommendations", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "other_services",
					"href" => site_url() . "/wp-admin/admin.php?page=other_services",
					"title" => __("Our Other Services", mail_bank))
			);
			$wp_admin_bar->add_menu(array(
					"parent" => "mail_bank",
					"id" => "wp_system_status",
					"href" => site_url() . "/wp-admin/admin.php?page=mail_system_status",
					"title" => __("System Status", mail_bank))
			);
		break;
	}
}
/////////////////////////////////////  Call Install Script on Plugin Activation ////////////////////////////////////////
if(!function_exists("plugin_install_script_for_mail_bank"))
{
	function plugin_install_script_for_mail_bank()
	{
		global $wpdb;
		if (is_multisite())
		{
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blog_ids as $blog_id)
			{
				switch_to_blog($blog_id);
				if(file_exists(MAIL_BK_PLUGIN_DIR. "/lib/wp-install-script.php"))
				{
					include MAIL_BK_PLUGIN_DIR. "/lib/wp-install-script.php";
				}
				restore_current_blog();
			}
		}
		else
		{
			if(file_exists(MAIL_BK_PLUGIN_DIR. "/lib/wp-install-script.php"))
			{
				include MAIL_BK_PLUGIN_DIR. "/lib/wp-install-script.php";
			}
		}
	}
}

function mail_bank_plugin_update_message($args)
{
	$response = wp_remote_get( 'http://plugins.svn.wordpress.org/wp-mail-bank/trunk/readme.txt' );
	if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) )
	{
		// Output Upgrade Notice
		$matches        = null;
		$regexp         = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote($args['Version']) . '\s*=|$)~Uis';
		$upgrade_notice = '';
		if ( preg_match( $regexp, $response['body'], $matches ) ) {
			$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));
			$upgrade_notice .= '<div class="framework_plugin_message">';
			foreach ( $changelog as $index => $line ) {
				$upgrade_notice .= "<p>".$line."</p>";
			}
			$upgrade_notice .= '</div> ';
			echo $upgrade_notice;
		}
	}
}




add_action("network_admin_menu", "create_global_menus_for_mail_bank" );
add_action("admin_bar_menu", "add_mail_icon",100);
add_action('phpmailer_init','wp_mail_bank_configure');
add_action("plugins_loaded", "mail_bank_plugin_load_text_domain");
add_action("admin_menu","create_global_menus_for_mail_bank");
add_action("admin_init","backend_plugin_js_scripts_mail_bank");
add_action("admin_init","backend_plugin_css_scripts_mail_bank");
register_activation_hook(__FILE__, "plugin_install_script_for_mail_bank");
add_action("in_plugin_update_message-".MAIL_FILE,"mail_bank_plugin_update_message" );
?>
