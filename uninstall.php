<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
	exit();

function wp_login_notice_delete_plugin() {
	delete_option('wp_login_notice_settings');
}

wp_login_notice_delete_plugin();
