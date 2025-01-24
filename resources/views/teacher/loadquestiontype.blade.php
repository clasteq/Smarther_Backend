@if(isset($qtype['questiontype_settings']['question_type_id']))
	<input type="hidden" name="sno[]" id="sno_{{$i}}" value="{{$i}}">
	@if($qtype['questiontype_settings']['question_type_id']>0) 
	@else 
	<div class="form-group form-float float-left col-md-12"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="oquestion_type[{{$qtype['id']}}][]" id="oquestion_type_{{$qtype['id']}}_{{$i}}" placeholder="Question Type">
		</div> 
	</div>
	@endif  
	@if($qtype['questiontype_settings']['question_type_id'] != 16)
	
	@if($qtype['questiontype_settings']['question']==1 && $qtype['questiontype_settings']['answer']==1) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="question[{{$qtype['id']}}][]" id="question_{{$qtype['id']}}_{{$i}}" placeholder="Question">
		</div> 
	</div>
	@else
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="question[{{$qtype['id']}}][]" id="question_{{$qtype['id']}}_{{$i}}" placeholder="Question">
		</div>
	</div>
	<div class="form-group form-float float-left col-md-6"> 
	</div>
	@endif  
	@else
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="choose_question[{{$qtype['id']}}][]" id="choose_question_{{$qtype['id']}}_{{$i}}" placeholder="Question">
			<input type="hidden" class="form-control" name="choose_question1[{{$qtype['id']}}][]" id="choose_question1_{{$qtype['id']}}_{{$i}}" placeholder="Question">
		</div> 
	</div>
	@endif

	@if($qtype['questiontype_settings']['option_1']==1 && $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_1[{{$qtype['id']}}][]" id="option_1_{{$qtype['id']}}_{{$i}}" placeholder="Option 1">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_2']==1 && $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_2[{{$qtype['id']}}][]" id="option_2_{{$qtype['id']}}_{{$i}}" placeholder="Option 2">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_3']==1 && $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_3[{{$qtype['id']}}][]" id="option_3_{{$qtype['id']}}_{{$i}}" placeholder="Option 3">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_4']==1 && $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_4[{{$qtype['id']}}][]" id="option_4_{{$qtype['id']}}_{{$i}}" placeholder="Option 4">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_1']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="choose_1[{{$qtype['id']}}][]" id="choose_1_{{$qtype['id']}}_{{$i}}" placeholder="Option 1">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_2']==1 && $qtype['questiontype_settings']['question_type_id']==16 ) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="choose_2[{{$qtype['id']}}][]" id="choose_2_{{$qtype['id']}}_{{$i}}" placeholder="Option 2">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_3']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="choose_3[{{$qtype['id']}}][]" id="choose_3_{{$qtype['id']}}_{{$i}}" placeholder="Option 3">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_4']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="choose_4[{{$qtype['id']}}][]" id="choose_4_{{$qtype['id']}}_{{$i}}" placeholder="Option 4">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['question_file']==1 ) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="question_file[{{$qtype['id']}}][]" id="question_file_{{$qtype['id']}}_{{$i}}">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['answer']==1 &&  $qtype['questiontype_settings']['question_type_id']==16 || $qtype['questiontype_settings']['question_type_id'] == 4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    {{-- <input type="text" class="form-control" name="answer[{{$qtype['id']}}][]" id="answer_{{$qtype['id']}}_{{$i}}" placeholder="Answer"> --}}

			<select name="answer[{{$qtype['id']}}][]" class="form-control" id="answer_{{$qtype['id']}}_{{$i}}">
				<option value="">Select Answer</option>
				<option value="A">A</option>
				<option value="B">B</option>
				<option value="C">C</option>
				<option value="D">D</option>
			</select>
		</div> 
	</div>
	@endif  

	
	@if($qtype['questiontype_settings']['answer']==1 && $qtype['questiontype_settings']['question_type_id'] == 5) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    {{-- <input type="text" class="form-control" name="answer[{{$qtype['id']}}][]" id="answer_{{$qtype['id']}}_{{$i}}" placeholder="Answer"> --}}

			<select name="answer[{{$qtype['id']}}][]" class="form-control" id="answer_{{$qtype['id']}}_{{$i}}">
				<option value="">Select Answer</option>
				<option value="A">True</option>
				<option value="B">False</option>
			</select>
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['answer']==1 && $qtype['questiontype_settings']['question_type_id']!=16 && $qtype['questiontype_settings']['question_type_id']!= 4  && $qtype['questiontype_settings']['question_type_id']!= 5) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="answer[{{$qtype['id']}}][]" id="answer_{{$qtype['id']}}_{{$i}}" placeholder="Answer">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['display_answer']==1) 
	<div class="form-group form-float float-left col-md-8"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="display_answer[{{$qtype['id']}}][]" id="display_answer_{{$qtype['id']}}_{{$i}}" placeholder="Display Answer for Match">
		</div> 
	</div>
	@endif  

@else 
	<div class="form-group form-float float-left col-md-12"> 
		<div class="form-group form-float float-left col-md-4"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="oquestion_type[{{$qtype['id']}}][]" id="oquestion_type_{{$qtype['id']}}_{{$i}}" placeholder="Question Type">
		</div> 
	</div>
	<div class="form-group form-float float-left col-md-4"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="oquestion[{{$qtype['id']}}][]" id="oquestion_{{$qtype['id']}}_{{$i}}" placeholder="Question">
		</div> 
	</div>
	<div class="form-group form-float float-left col-md-4"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="oanswer[{{$qtype['id']}}][]" id="oanswer_{{$qtype['id']}}_{{$i}}" placeholder="Answer">
		</div> 
	</div>
@endif

