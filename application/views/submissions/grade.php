
<div class="span10">
	<div class="well">
		<h1><?php echo $quest ?><span class="label label-important pull-right">Attempt: <?php echo $attempts;?></span>
		</h1>

			<?php echo $submission;?>
			<hr>

		<?php
			echo validation_errors();
			echo form_open('');
  		?>
  			<fieldset>
  				<div class="control-group">
					<div class="controls">
						<textarea type="text" id="response" name="response"></textarea>
					</div>
				</div>
		<?php foreach ($skills as $skill): ?>
				<div class="control-group">
					<label class="control-label" for="skill-type"><?php echo $skill[0]->name;?></label>
					<div class="controls">
						<input type="hidden" name="skill[]" value="<?php echo $skill[0]->skid;?>">
						<select name="award[]"class="chzn-select">';
							<option></option>
							<?php foreach ($skill as $option):?>
								<option value="<?php echo $option->amount;?>"><?php echo $option->label;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>
			<?php endforeach;?>

			</fieldset>
			<input type="hidden" name="submission" value="<?php echo $id ;?>">
			<input type="hidden" name="quest-id" value="<?php echo $qid ;?>">
			<input type="hidden" name="quest-note" value="<?php echo $quest;?>">
			<input type="hidden" name="users[]" value="<?php echo $uid ;?>">
			<input type="hidden" name="attempt" value="<?php echo $attempts ;?>">

				<div class="form-actions">
					<div class="pull-right">
						<button type="submit" class="btn-primary">Grade</button>
					</div>
				</div>		
		</div>
	</form>
	
</div>
	<script>
		$('.chzn-select').chosen();
	</script>