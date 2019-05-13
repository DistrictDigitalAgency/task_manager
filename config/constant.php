<?php
	return array(
        'default_title' => 'Advanced Task Manager',
    	'system_default' => [
            'application_name' => 'Advanced Task Manager',
            'setup_guide' => 1,
            'timezone_id' => '266',
            'default_language' => 'en',
            'direction' => 'ltr',
            'error_display' => 1,
            'textarea_limit' => '300',
            'notification_position' => 'toast-bottom-right',
            'multilingual' => 1,
            'throttle_attempt' => 5,
            'throttle_lockout_period' => 2,
            'login_type' => null,
            'lock_screen_timeout' => 1,
            'cache_lifetime' => '100',
            'credit' => 'SHARED ON CODELIST.CC',
            'celebration_days' => 30,
            'under_maintenance_message' => 'The system is under maitnenance.',
            'chat_refresh_duration' => 60,
            'enable_password_strength_meter' => 1,
            'hidden_value' => 'xxxxxxxxxxxxxxxx',
            'subordinate_level' => 1,
            'location_level' => 1,
            'show_error_messages' => 0,
            'task_users' => 'multiple'
        ],
        'default_role' => 'admin',
        'default_department' => 'System Administration',
        'default_designation' => 'System Administrator',
        'item_code' => 'ATM1310',
        'upload_path' => [
            'backup' => 'uploads/backup/',
            'logo' => 'uploads/logo/',
            'avatar' => 'uploads/avatar/',
            'attachments' => 'uploads/attachments/',
        ],
        'ignore_var' => array('_token','config_type','ajax_submit'),
        'path' => [
            'country' => '/config/country.php',
            'timezone' => '/config/timezone.php',
            'translation' => '/config/translation.php',
            'localization' => '/config/localization.php',
            'verifier' => 'http://envato.wmlab.in/',
            'config' => '/config/config.php',
            'mail' => '/config/mail.php',
            'service' => '/config/services.php',
        ],
        'mail_default' => [
            'driver' => 'log',
            'from_name' => 'WM Lab',
            'from_address' => 'support@wmlab.in'
        ],
        'social_login_provider' => [
            'facebook','twitter','google','github'
        ]
	);
?>