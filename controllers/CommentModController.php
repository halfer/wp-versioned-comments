<?php

class CommentModController extends TemplateSystem
{
	const PATH_PLUGIN_NAME = 'wp-comment-mod';

	public function execute()
	{
		$this->initCss();
		add_action( 'add_meta_boxes_comment', array($this, 'moderatorOptions') );
	}

	protected function initCss()
	{
		// Get plugins folder relative to wp root
		$site = site_url();
		$relativePath = substr(plugins_url(), strlen($site));

		wp_register_style(
			'commentmod_css',
			$relativePath . '/' . self::PATH_PLUGIN_NAME . '/styles/main.css'
		);
		wp_enqueue_style('commentmod_css');		
	}

	public function moderatorOptions()
	{
		$this->renderTemplate('version-history');
	}
}