<?php
/*
  Plugin Name: WP Login Notice
  Plugin URI: http://beek.jp/wp-login-notice/
  Description: Someone will be notified by e-mail After logging.
  Version: 1.3.0
  Author: Satoshi Yoshida
  Author URI: http://beek.jp
  License: GPLv2 or later
 */
/*  Copyright 2015 Satoshi Yoshida (email : s-yoshida@beek.jp)

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

class WpLoginNoticeInit {

	function __construct() {
		add_action('admin_menu', array($this, 'add_pages'));
	}

	function add_pages() {
		add_submenu_page('plugins.php', __('WP Login Notice', 'wp-login-notice'), __('WP Login Notice', 'wp-login-notice'), 'level_8', __FILE__, array($this, 'setting_view'));
	}

	function setting_view() {
		$post_settings = filter_input(INPUT_POST, 'wp_login_notice_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		if (!is_null($post_settings)) {
			check_admin_referer('wp_login_notice_settings');
			update_option('wp_login_notice_settings', $post_settings);
			?><div class="updated fade"><p><strong><?php _e('Settings saved.', 'wp-login-notice'); ?></strong></p></div><?php
		}
		?>
		<div style="padding: 10px;">
			<h1><?php _e('WP Login Notice Settings', 'wp-login-notice'); ?></h1>
			<form action="" method="post" style="padding-left: 10px;">
				<?php
				wp_nonce_field('wp_login_notice_settings');
				$settings = get_option('wp_login_notice_settings');
				$from = isset($settings['from']) ? esc_html($settings['from']) : $this->wp_login_notice_get_mail_from();
				$to = isset($settings['to']) ? esc_html($settings['to']) : $this->wp_login_notice_get_mail_to();
				$cc = isset($settings['cc']) ? esc_html($settings['cc']) : $this->wp_login_notice_get_mail_cc();
				$subject = isset($settings['subject']) ? esc_html($settings['subject']) : $this->wp_login_notice_get_mail_subject();
				$body = isset($settings['body']) ? esc_html($settings['body']) : $this->wp_login_notice_get_mail_body();
				$roles = isset($settings['roles']) ? esc_html($settings['roles']) : $this->wp_login_notice_get_roles();
				$users = isset($settings['users']) ? esc_html($settings['users']) : $this->wp_login_notice_get_users();
				?>

				<h2 style="margin: 20px 0 5px 0;"><?php _e('Available parameters are:', 'wp-login-notice'); ?></h2>
				<ul style="margin-top: 0; margin-left: 10px;">
					<li><?php _e('%SITENAME% :: Site name.', 'wp-login-notice'); ?></li>
					<li><?php _e('%USERNAME% :: Login user name.', 'wp-login-notice'); ?></li>
					<li><?php _e('%DATE% :: Login date.', 'wp-login-notice'); ?></li>
					<li><?php _e('%TIME% :: Login time.', 'wp-login-notice'); ?></li>
					<li><?php _e('%IP% :: Login user ip address.', 'wp-login-notice'); ?></li>
					<li><?php _e('%HOST% :: Login user host name.', 'wp-login-notice'); ?></li>
				</ul>

				<h2 style="margin: 20px 0 5px 0;"><?php _e('From', 'wp-login-notice'); ?></h2>
				<input name="wp_login_notice_settings[from]" type="text" style="width: 100%;" value="<?php echo $from; ?>" placeholder="wp_login_notice@beek.jp">

				<h2 style="margin: 20px 0 5px 0;"><?php _e('To', 'wp-login-notice'); ?></h2>
				<p style="margin: 0;"><?php _e('If there is more than one destination, please enter separated by commas.', 'wp-login-notice'); ?></p>
				<p style="margin: 0;"><?php _e('If not input, it will be sent to the mail address of the logged-in user.', 'wp-login-notice'); ?></p>
				<input name="wp_login_notice_settings[to]" type="text" style="width: 100%;" value="<?php echo $to; ?>" placeholder="wp_login_notice@beek.jp">

				<h2 style="margin: 20px 0 5px 0;"><?php _e('Cc', 'wp-login-notice'); ?></h2>
				<p style="margin: 0;"><?php _e('If there is more than one destination, please enter separated by commas.', 'wp-login-notice'); ?></p>
				<input name="wp_login_notice_settings[cc]" type="text" style="width: 100%;" value="<?php echo $cc; ?>" placeholder="wp_login_notice@beek.jp">

				<h2 style="margin: 20px 0 5px 0;"><?php _e('Subject', 'wp-login-notice'); ?></h2>
				<input name="wp_login_notice_settings[subject]" type="text" style="width: 100%;" value="<?php echo $subject; ?>" placeholder="%SITENAME% login notice.">

				<h2 style="margin: 20px 0 5px 0;"><?php _e('Body', 'wp-login-notice'); ?></h2>
				<textarea name="wp_login_notice_settings[body]" style="width: 100%; height: 150px"><?php echo $body; ?></textarea>
                                
				<h2 style="margin: 20px 0 5px 0;"><?php _e('Target Roles', 'wp-login-notice'); ?></h2>
				<input name="wp_login_notice_settings[roles]" type="text" style="width: 100%;" value="<?php echo $roles; ?>" placeholder="administrator,editor,author,contributor,subscriber">
                                
				<h2 style="margin: 20px 0 5px 0;"><?php _e('Exclude User Name(Not nickname and display name)', 'wp-login-notice'); ?></h2>
				<input name="wp_login_notice_settings[users]" type="text" style="width: 100%;" value="<?php echo $users; ?>" placeholder="administrator,john,michael,matthew">

				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save', 'wp-login-notice'); ?>"></p>
			</form>
		</div>
		<?php
	}

	function wp_login_notice_get_mail_from() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['from']) ? $option['from'] : 'wp-login-notice@beek.jp';
	}

	function wp_login_notice_get_mail_to() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['to']) ? $option['to'] : get_option('admin_email');
	}

	function wp_login_notice_get_mail_cc() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['cc']) ? $option['cc'] : '';
	}

	function wp_login_notice_get_mail_subject() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['subject']) ? $option['subject'] : __('%SITENAME% login notice.', 'wp-login-notice');
	}

	function wp_login_notice_get_mail_body() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['body']) ? $option['body'] : __('%USERNAME% logged in at %DATE% %TIME% by %HOST%(%IP%)', 'wp-login-notice');
	}

	function wp_login_notice_get_roles() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['roles']) ? $option['roles'] : 'administrator,editor,author,contributor,subscriber';
	}

	function wp_login_notice_get_users() {
		$option = get_option('wp_login_notice_settings');
		return isset($option['users']) ? $option['users'] : '';
	}

}

$wp_login_notice = new WpLoginNoticeInit();

function load_wp_login_notice_textdomain() {
	load_plugin_textdomain('wp-login-notice', FALSE, basename(dirname(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'load_wp_login_notice_textdomain');

function wp_login_notice_send($par) {
	$login_user = get_user_by('login',$par);
	$wp_login_notice = new WpLoginNoticeInit();
	if (!$wp_login_notice->wp_login_notice_get_mail_to()) {
		$to = array($login_user->data->user_email);
	} else {
		$to = explode(',', $wp_login_notice->wp_login_notice_get_mail_to());
	}
	array_walk($to, 'wp_login_notice_trim_value');
	
	$roles = explode(',', $wp_login_notice->wp_login_notice_get_roles());
	array_walk($roles, 'wp_login_notice_trim_value');
	if(!in_array($login_user->roles[0], $roles)) {
		return;
	}
	
	$users = explode(',', $wp_login_notice->wp_login_notice_get_users());
	array_walk($users, 'wp_login_notice_trim_value');
	if(in_array($login_user->data->user_login, $users)) {
		return;
	}
	
	$subject = str_replace(array("%SITENAME%", "%USERNAME%", "%DATE%", "%TIME%", "%IP%", "%HOST%"), array(get_option('blogname'), $par, date('Y-m-d', current_time('timestamp')), date('H:i:s', current_time('timestamp')), $_SERVER['REMOTE_ADDR'], gethostbyaddr($_SERVER['REMOTE_ADDR'])), $wp_login_notice->wp_login_notice_get_mail_subject());
	$body = str_replace(array("%SITENAME%", "%USERNAME%", "%DATE%", "%TIME%", "%IP%", "%HOST%"), array(get_option('blogname'), $par, date('Y-m-d', current_time('timestamp')), date('H:i:s', current_time('timestamp')), $_SERVER['REMOTE_ADDR'], gethostbyaddr($_SERVER['REMOTE_ADDR'])), $wp_login_notice->wp_login_notice_get_mail_body());
	$header[] = 'From: ' . $wp_login_notice->wp_login_notice_get_mail_from();
	if ($wp_login_notice->wp_login_notice_get_mail_cc()) {
		$cc = explode(',', $wp_login_notice->wp_login_notice_get_mail_cc());
		array_walk($cc, 'wp_login_notice_trim_value');
		foreach ($cc as $value) {
			$header[] = 'Cc: ' . $value;
		}
	}
	wp_mail($to, $subject, $body, $header);
}

add_action('wp_login', 'wp_login_notice_send');

function wp_login_notice_trim_value(&$value) {
	$value = trim($value);
}
