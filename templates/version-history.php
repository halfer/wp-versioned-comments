<div id="comment-version-options" class="stuffbox">
	<h3>
		<label>Version options</label>
	</h3>
	<div class="inside">
		<div>
			<input
				type="checkbox"
				id="comments-save-version"
				name="comments-save-version"
				checked="checked"
				value="1"
			/>
			<label for="save_version">Create previous version prior to update</label>
		</div>		
	</div>
</div>

<div id="comment-version-history" class="stuffbox">
	<h3>
		<label>Version history</label>
	</h3>
	<div class="inside">
		<?php foreach ($commentVersions as $ord => $commentVersion): ?>
			<fieldset>
				<legend>
					Version <?php echo count($commentVersions) - $ord ?>
					amended on <?php echo date('j M Y G:i:s', $commentVersion['comment_date'] ) ?>
					<?php if ($commentVersion['comment_amended_by_user_name']): ?>
						by <?php echo $commentVersion['comment_amended_by_user_name'] ?>
					<?php endif ?>
				</legend>
				<div class="comment-items">
					<span class="comment-item">
						Name: <?php echo $commentVersion['comment_author'] ?>
					</span>
					<span class="comment-item">
						E-mail:
						<?php
							echo $commentVersion['comment_author_email'] ?
							$commentVersion['comment_author_email'] :
							'(empty)'
						?>
					</span>
					<span class="comment-item">
						URL:
						<?php
							echo $commentVersion['comment_author_url'] ?
							$commentVersion['comment_author_url'] :
							'(empty)'
						?>
					</span>
				</div>
				<?php echo $commentVersion['comment_content'] ?>
			</fieldset>
		<?php endforeach ?>
	</div>
</div>