<?php

// Import this class as a unique name
use TemplateSystem\Change2\ControllerBase as VersionedCommentsControllerBase;

class VersionedCommentsController extends VersionedCommentsControllerBase
{
	const PATH_PLUGIN_NAME = 'wp-versioned-comments';
	const COMMENT_KEY_HISTORY = 'wp-versioned-comments-history';
	const COMMENT_KEY_FORMAT = 'wp-versioned-comments-format';
	const DATABASE_FORMAT = 1;

	/**
	 * Main controller entry point
	 */
	public function execute()
	{
		$this->initCss();
		$this->initEditCommentHandler();

		add_action('add_meta_boxes_comment', array($this, 'editCommentDialogue'));
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
			'versioned-comments-css',
			$relativePath . '/' . self::PATH_PLUGIN_NAME . '/styles/main.css'
		);
		wp_enqueue_style('versioned-comments-css');		
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
	public function editCommentDialogue(stdClass $comment)
	{
		// Just for safety; a comment should not be amended unless the user may mod comments anyway, afaik
		if (!$this->userCanModerateComments())
		{
			return;
		}

		// Grab all the previous versions
		$serialisedVersions = get_comment_meta($comment->comment_ID, self::COMMENT_KEY_HISTORY);

		// Unserialise all the previous comment arrays
		$commentVersions = array();
		foreach ($serialisedVersions as $serialisedVersion)
		{
			$commentVersion = unserialize($serialisedVersion);

			// Look up the amending user in each case
			$userId = array_key_exists('comment_amended_by_user_id', $commentVersion) ?
				$commentVersion['comment_amended_by_user_id'] :
				null;
			if ($userId)
			{
				$user = get_user_by('id', $userId);
				$commentVersion['comment_amended_by_user_name'] = $user->display_name;
			}

			$commentVersions[] = $commentVersion;
		}

		// Send the comments in reverse chrono order
		$this->renderTemplate(
			'version-history',
			array('commentVersions' => array_reverse($commentVersions), )
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

		// Allow moderator to opt out of creating a version (e.g. for trivial changes). The AJAX version
		// (i.e. Quick Edit) does not have this, so we assume the user wishes to create a version here.
		// In the future we might offer an options panel to configure this.
		$doSave =
			((bool) $_POST['comments-save-version']) ||
			$this->isAjax()
		;

		// Just for safety; a comment should not be amended unless the user may mod comments anyway, afaik
		if ($doSave && $this->userCanModerateComments())
		{
			$comment = get_comment($commentId);

			// Get simplified version of old and new comment
			$oldComment = $this->getMonitoredFields($comment);
			$newComment = $this->getMonitoredFields($_POST);

			// Only create a new version if there's a monitorable change
			if (array_diff_assoc($oldComment, $newComment))
			{
				$this->createVersion($commentId, $oldComment);
			}
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
		// Let's add in a timestamp and current user too
		$oldComment['comment_date'] = time();
		$oldComment['comment_amended_by_user_id'] = get_current_user_id();

		// Always create a new (non-unique) meta entry for this version
		add_comment_meta($commentId, self::COMMENT_KEY_HISTORY, serialize($oldComment));

		// If it doesn't exist, write the database format code (do nothing if it already exists)
		add_comment_meta($commentId, self::COMMENT_KEY_FORMAT, self::DATABASE_FORMAT, $_unique = true);
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

	protected function userCanModerateComments()
	{
		return current_user_can('moderate_comments');
	}

	protected function isAjax()
	{
		return
			!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
		;
	}
}