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
					Version <?php echo $ord + 1 ?>
					by <?php echo $commentVersion['comment_author'] ?>
					<?php if ($commentVersion['comment_author_url']): ?>
						(<?php echo $commentVersion['comment_author_url'] ?>)
					<?php endif ?>
					edited on <?php echo date('r', $commentVersion['comment_date'] ) ?>
				</legend>
				<?php echo $commentVersion['comment_content'] ?>
			</fieldset>
		<?php endforeach ?>
	</div>
</div>