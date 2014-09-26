<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | Hooks
  | -------------------------------------------------------------------------
  | This file lets you define "hooks" to extend CI without hacking the core
  | files.  Please see the user guide for info:
  |
  |	http://codeigniter.com/user_guide/general/hooks.html
  |
 */

$hook['post_controller_constructor'][] = array(
    'class'    => 'Ddi_hook',
    'function' => 'custom_auth',
    'filename' => 'hooks.php',
    'filepath' => 'hooks'
);
$hook['post_controller_constructor'][] = array(
    'class'    => 'Ddi_hook',
    'function' => 'custom_cfg',
    'filename' => 'hooks.php',
    'filepath' => 'hooks'
);
$hook['post_controller'][] = array(
    'class'    => 'Ddi_hook',
    'function' => 'view',
    'filename' => 'hooks.php',
    'filepath' => 'hooks'
);


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */