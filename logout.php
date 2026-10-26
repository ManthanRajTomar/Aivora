<?php
/**
 * AIVORA - User Logout Handler (logout.php)
 * Destroys user session tokens and redirects.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();

set_flash('info', 'You have been safely signed out. See you again soon!');
redirect('login.php');
