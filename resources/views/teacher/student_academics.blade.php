<table class="table table-striped table-bordered">
	<thead>
		<tr><th>Academic Year</th><th>Month Start</th><th>Month End</th><th>Class</th><th>Section</th></tr>
	</thead>
	@if(!empty($students))
		<tbody>
			@foreach($students as $stud)
				<?php echo "<pre>"; print_r($stud); ?>
			@endforeach
		</tbody>
	@endif
</table>