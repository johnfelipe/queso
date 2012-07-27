
	<table class="table">
			<thead>
			  <tr>
				<th style="width:25%">Quest</th>
				<th style="width:60%"></th>
				<th style="width:15%"></th>
			  </tr>
			</thead>
			<tbody>
<?php foreach ($quests as $quest) :?>

					  <tr>
						<td><h4><?php echo $quest->name;?></h4></td>
						<td><?php echo $quest->instructions;?></td>
						<td>
						<?php if ($quest->hidden):?>
						<a class="btn btn-success" href='quest/activate/<?php echo $quest->id;?>'>show</a>
						<?php else:?>
						<a class="btn btn-danger" href='quest/deactivate/<?php echo $quest->id;?>'>hide</a></td>
						<?php endif;?>
					  </tr>					
					  <?php endforeach;?>
			</tbody>
	</table>

</div>