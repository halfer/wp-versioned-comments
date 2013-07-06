<div id="comment-version-history" class="stuffbox">
	<h3>
		<label>Version history</label>
	</h3>
	<div class="inside">
		<div>
			<input
				type="checkbox" id="save_version" name="save_version" checked="checked"
			/>
			<label for="save_version">Create previous version prior to save</label>
		</div>

		<?php foreach ($commentVersions as $ord => $commentVersion): ?>
			<fieldset>
				<legend>
					Version <?php echo count($commentVersions) - $ord ?>
					amended on <?php echo date('j M Y G:i:s', $commentVersion['comment_date'] ) ?>
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