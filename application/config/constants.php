<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');


/**
 * Customize definition
 */
define('PATH_ROOT', rtrim(str_replace('system', '', str_replace($_SERVER['DOCUMENT_ROOT'], '', BASEPATH)),'/').'/');
define('IMG_UPLOAD_MAX_SIZE', 4096000);
define('FILE_UPLOAD_MAX_SIZE', 4096000);
define('IMG_UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'].'/uploads/');
define('IMG_UPLOAD_DIR_REL', str_replace($_SERVER['DOCUMENT_ROOT'], '', IMG_UPLOAD_DIR));
define('IMG_MAX_WIDTH',800);
define('IMG_MAX_HEIGHT',600);
define('IMG_THUMB_WIDTH',400);
define('IMG_THUMB_HEIGHT',400);
define('IMG_SMALL_WIDTH',90);
define('IMG_SMALL_HEIGHT',90);
define('ASSETS_URL', PATH_ROOT.'assets/');
define('IMG_URL', ASSETS_URL.'img/');
define('CSS_URL', ASSETS_URL.'css/');
define('JS_URL', ASSETS_URL.'js/');
define('LIBS_URL', ASSETS_URL.'libs/');
define('MARGIN_PRICE','5');

/* End of file constants.php */
/* Location: ./application/config/constants.php */