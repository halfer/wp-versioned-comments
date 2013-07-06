<?php

class CommentModController extends TemplateSystem
{
	const PATH_PLUGIN_NAME = 'wp-comment-mod';
	const COMMENT_KEY_HISTORY = 'wp-comment-mod_history';

	/**
	 * Main controller entry point
	 */
	public function execute()
	{
		$this->initCss();
		$this->initEditCommentHandler();

		add_action('add_meta_boxes_comment', array($this, 'moderatorOptions'));
	}

	/**
	 * Called to link in our CSS file
	 */
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

	/**
	 * Called to set up the edit comment handler
	 * 
	 * @todo I think this should be called only for admin users
	 */
	protected function initEditCommentHandler()
	{
		// This hook is run prior to the comment save
		add_action('comment_save_pre', array($this, 'commentSavePreHandler'));
	}

	/**
	 * Upon the add_meta_boxes_comment event, renders our extra bit
	 * 
	 * If there is a very large number of previous versions, we ought to do some pagination or something.
	 * However I've not added that just yet, since it is unlikely to be required in most cases.
	 */
	public function moderatorOptions(stdClass $comment)
	{
		// Grab all the previous versions
		$serialisedVersions = get_comment_meta($comment->comment_ID, self::COMMENT_KEY_HISTORY);

		// Unserialise all the previous comment arrays
		$commentVersions = array();
		foreach ($serialisedVersions as $serialisedVersion)
		{
			$commentVersions[] = unserialize($serialisedVersion);
		}

		$this->renderTemplate(
			'version-history',
			array('commentVersions' => $commentVersions,)
		);
	}

	/**
	 * A comment has just been saved, handle it here
	 * 
	 * @param integer $commentId
	 */
	public function commentSavePreHandler($commentText)
	{
		// Obtain the comment ID from the form
		$commentId = (int) $_POST['comment_ID'];
		$comment = get_comment($commentId);

		// Get simplified version of old and new comment
		$oldComment = $this->getMonitoredFields($comment);
		$newComment = $this->getMonitoredFields($_POST);

		// Only create a new version if there's a monitorable change
		if (array_diff_assoc($oldComment, $newComment))
		{
			$this->createVersion($commentId, $oldComment);
		}
		
		return $commentText;
	}

	/**
	 * Stores a serialised (i.e. non-searchable) version of the comment
	 * 
	 * @param integer $commentId
	 * @param array $oldComment
	 */
	protected function createVersion($commentId, array $oldComment)
	{
		// Let's add in a timestamp too
		$oldComment['comment_date'] = time();

		// Always create a new (non-unique) meta entry for this version
		add_comment_meta($commentId, self::COMMENT_KEY_HISTORY, serialize($oldComment));
	}

	/**
	 * Grabs the monitored fields components from the array or object parameter
	 * 
	 * @param mixed $comment
	 * @return array
	 */
	protected function getMonitoredFields($comment)
	{
		$array = array();
		foreach ($this->getMonitoredFieldnames() as $fieldName)
		{
			if (is_array($comment))
			{
				$array[$fieldName] = $comment[$fieldName];
			}
			elseif (is_object($comment))
			{
				$array[$fieldName] = $comment->$fieldName;
			}
		}

		return $array;
	}

	/**
	 * A change in these is regarded as a "versionable" change
	 * 
	 * @return array
	 */
	protected function getMonitoredFieldnames()
	{
		return array(
			'comment_author',
			'comment_author_email',
			'comment_author_url',
			'comment_content'
		);
	}
}