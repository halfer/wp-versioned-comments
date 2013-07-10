<?php
/*
Plugin Name: Versioned Comments
Description: Edit/remove comments, marking as moderated
Version: 0.1
Author: Jon Hinks
Author URI: http://blog.jondh.me.uk/
License: GPL2
*/

$root = dirname(__FILE__);

// This does a non-namespaced check of the PHP version first
$ok = require_once($root . '/vendor/TemplateSystem/wordpress/check53.php');

if ($ok)
{
	require_once $root . '/vendor/TemplateSystem/ControllerBase.php';
	require_once $root . '/controllers/VersionedCommentsController.php';

	$controller = new VersionedCommentsController( $root );
	$controller->runAll();
}