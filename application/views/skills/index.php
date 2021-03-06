
        <div class="span10">
			<h1><?php echo $title ?></h1>
			<p class="lead"><?php echo $instructions ?></p>
			<table class="table">
					<thead>
					  <tr>
						<th>Title</th>
						<th></th>
					  </tr>
					</thead>
					<tbody>

					  <tr>
						<td>
						<span class="skill-title"></span>
						<input class="skill-editing input-xlarge" type="text" name="new-skill-title">
						</td>
						<td><button class="btn btn-large btn-primary pull-right add">Create New Skill</button></td>
					  </tr>					
					
					<?php foreach ($skills as $skill):?>

					  <tr>
						<td>
						<span class="skill-title"><?php echo $skill->name; ?></span>
						<input type="hidden" name="skill-number" class='skill-number' value="<?php echo $skill->id?>">
						<input class="hidden skill-editing" type="text" name="skill-title" value="<?php echo $skill->name;?>">
							</td>
							<td>
							<div class="btn-group pull-right">
						<button class="btn hidden skill-save">
						<span class="add-on"><i class="icon-ok"></i></span>
						</button>
						<button class="btn skill-edit">
						<span class="add-on">
						<i class="icon-pencil"></i></span>
						</button>

						<button class="btn remove btn-danger"><i class="icon-trash"></i></button>
						</div>
						</td>
					  </tr>

					<?php endforeach ?>
					</tbody>
				  </table>

      <hr>
      </div>
	<script>
	
	$('button.skill-save').click( function() {
		event.preventDefault();
		//save new title
		
		//hide editable fields

		
		
		$(this).parent().parent().parent().children().children(".skill-title").show();
		var skillnum = $(this).parent().parent().parent().children('td').children(".skill-number").val();
		var skillname = $(this).parent().parent().parent().children('td').children(".skill-editing").val();
		$.post("skills/edit", { id: skillnum, name: skillname } );
		$(this).parent().parent().parent().children().children(".skill-title").html(skillname);

		$(this).parent().children(".skill-edit").show();
		$(this).parent().parent().parent().children().children(".skill-editing").addClass("hidden");
		$(this).parent().children(".skill-save").addClass("hidden");

	});
	
	$('button.skill-edit').click( function() {
		//show editable fields
		event.preventDefault();
		
		$(this).parent().parent().parent().children().children(".skill-title").hide();
		$(this).parent().children(".skill-edit").hide();
		$(this).parent().parent().parent().children().children(".skill-editing").removeClass("hidden");
		$(this).parent().children(".skill-save").removeClass("hidden");

	});

	$('button.remove').click( function() {
	var skillnum = $(this).parent().parent().parent().children().children("input.skill-number").val();
//		var skillnum = $(this).parent().parent().children("td").children("input.skill-number").val();
		$.post("skills/remove", { id: skillnum } );
		$(this).parent().parent().parent().remove();
	});

	$('button.add').click( function() {
		var skillname = $(this).parent().parent().children('td').children('.skill-editing').val();
		$.post("skills/create", { name: skillname },
		   function(data) {
		   	window.location = "skills";
			 //$('tbody').append(data);
		   });
		
	});

	</script>