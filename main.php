<?php
/*
Plugin Name: Comment Mod
Description: Edit/remove comments, marking as moderated
Version: 0.1
Author: Jon Hinks
Author URI: http://blog.jondh.me.uk/
License: GPL2
*/

$root = dirname(__FILE__);

require_once $root . '/vendor/TemplateSystem/TemplateSystem.php';
require_once $root . '/controllers/CommentModController.php';

$controller = new CommentModController( $root );
$controller->runAll();