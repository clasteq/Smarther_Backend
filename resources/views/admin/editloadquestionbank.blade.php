

<div class="form-group form-float float-left col-md-12">
	<input type="hidden" name="sno[]" id="sno_{{$i}}" value="{{$i}}">
	<input type="hidden" name="qb_item_id[{{$qtype1['questiontype_settings']['id']}}][]" id="qb_item_id_{{$item->id}}" value="{{$item->id}}" >

	@if($qtype1['questiontype_settings']['question_type_id']>0) 
	@else 
	<div class="form-group form-float float-left col-md-12"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="oquestion_type[{{$qtype1['questiontype_settings']['id']}}][]" id="oquestion_type_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question Type">
		</div> 
	</div>
	@endif  
 @if($qtype['questiontype_settings']['question_type_id'] != 16)
	@if($qtype['questiontype_settings']['question']==1 && $qtype['questiontype_settings']['answer']==1) 
	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="question[{{$qtype1['questiontype_settings']['id']}}][]" id="question_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question" value="{!! $item->question !!}">
		</div> 
	</div>
	@else
	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="question[{{$qtype1['questiontype_settings']['id']}}][]" id="question_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question" value="{!! $item->question !!}">
		</div> 
	</div>
	<div class="form-group form-float float-left col-md-5"> 
	</div>
	@endif  
	@elseif ($qtype['questiontype_settings']['question_type_id'] == 16)

	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
			<label>Question</label>
		    <input type="file" class="form-control" name="choose_question[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]" value="{{$item->question}}" id="choose_question_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question" >

			<input type="hidden" class="form-control" name="choose_question1[{{$qtype1['questiontype_settings']['id']}}][]" id="choose_question1_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question" value="{{$item->question}}">

			@if(!empty($item->question))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
			 <img src="{{ $fileurl }}" width="90%" style="    max-height: 300px;">
		 @endif
		</div> 
	</div>
	@endif

	@if($qtype['questiontype_settings']['option_1']==1 &&  $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_1[{{$qtype1['questiontype_settings']['id']}}][]" id="option_1_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 1" value="{!! $item->option_1 !!}">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_2']==1 &&  $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_2[{{$qtype1['questiontype_settings']['id']}}][]" id="option_2_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 2" value="{!! $item->option_2 !!}">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_3']==1 &&  $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_3[{{$qtype1['questiontype_settings']['id']}}][]" id="option_3_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 3" value="{!! $item->option_3 !!}">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_4']==1 &&  $qtype['questiontype_settings']['question_type_id']==4) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="option_4[{{$qtype1['questiontype_settings']['id']}}][]" id="option_4_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 4" value="{!! $item->option_4 !!}">
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_1']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
			<label>Option A</label>
		    <input type="file" class="form-control" name="choose_1[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]" id="choose_1_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 1">
			@if(!empty($item->option_1))
           <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_1; ?>
			<img src="{{ $fileurl }}" width="90%" style="    max-height: 300px;">
		@endif
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_2']==1 && $qtype['questiontype_settings']['question_type_id']==16 ) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
			<label>Option B</label>
		    <input type="file" class="form-control" name="choose_2[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]" id="choose_2_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 2">
			@if(!empty($item->option_2))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_2; ?>
			<img src="{{ $fileurl }}" width="90%" style="    max-height: 300px;">

		@endif
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_3']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
			<label>Option C</label>
		    <input type="file" class="form-control" name="choose_3[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]" value="{{$item->option_3}}" id="choose_3_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 3">
			
			@if(!empty($item->option_3))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_3; ?>
			<img src="{{ $fileurl }}" width="90%" style="    max-height: 300px;">

		@endif
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['option_4']==1 && $qtype['questiontype_settings']['question_type_id']==16) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
			<label>Option D</label>
		    <input type="file" class="form-control" name="choose_4[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]"  id="choose_4_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Option 4">
		
			@if(!empty($item->option_4))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_4; ?>
			<img src="{{ $fileurl }}" width="90%"  style="    max-height: 300px;">

		@endif
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['question_file']==1) 
	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
		    <input type="file" class="form-control" name="question_file[{{$qtype1['questiontype_settings']['id']}}][]" id="question_file_{{$qtype1['questiontype_settings']['id']}}_{{$i}}">
		</div> 
		@if(!empty($item->question_file))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/qb/'.$item->question_file; ?>
			<a href={{$fileurl}} target="_blank">View file</a>
		@endif
	</div>
	@endif  

	@if($qtype['questiontype_settings']['answer']==1 &&  $qtype['questiontype_settings']['question_type_id']!=16 &&  $qtype['questiontype_settings']['question_type_id']!= 4 &&  $qtype['questiontype_settings']['question_type_id']!= 5) 
	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="answer[{{$qtype1['questiontype_settings']['id']}}][]" id="answer_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Answer" value="{!! $item->answer !!}">
		</div> 
	</div>
	@endif  

	
	@if($qtype['questiontype_settings']['answer']==1 &&  ($qtype['questiontype_settings']['question_type_id']==16 ||  $qtype['questiontype_settings']['question_type_id']== 4) )
	<div class="form-group form-float float-left col-md-5"> 
		<div class="form-line">
		    {{-- <input type="text" class="form-control" name="answer[{{$qtype['id']}}][]" id="answer_{{$qtype['id']}}_{{$i}}" placeholder="Answer"> --}}
		    @if($qtype['questiontype_settings']['question_type_id']==16)<label>Answer</label>@endif  
			<select name="answer[{{$qtype1['questiontype_settings']['id']}}][]" class="form-control" id="answer_{{$qtype1['questiontype_settings']['id']}}_{{$i}}">
				<option value="">Select Answer</option>
				<option @if($item->answer == 'A')  selected @endif value="A">A</option>
				<option @if($item->answer == 'B')  selected @endif value="B">B</option>
				<option  @if($item->answer == 'C')  selected @endif  value="C">C</option>
				<option  @if($item->answer == 'D')  selected @endif  value="D">D</option>
			</select>
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['answer']==1 && $qtype['questiontype_settings']['question_type_id']== 5) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    {{-- <input type="text" class="form-control" name="answer[{{$qtype['id']}}][]" id="answer_{{$qtype['id']}}_{{$i}}" placeholder="Answer"> --}}

			<select name="answer[{{$qtype1['questiontype_settings']['id']}}][]" class="form-control" id="answer_{{$qtype1['questiontype_settings']['id']}}_{{$i}}">
				<option value="">Select Answer</option>
				<option @if($item->answer == 'A')  selected @endif value="A">True</option>
				<option @if($item->answer == 'B')  selected @endif value="B">False</option>
			</select>
		</div> 
	</div>
	@endif  

	@if($qtype['questiontype_settings']['hint_file']==1) 
	<div class="form-group form-float float-left col-md-8"> <!-- [{{$qtype['id']}}][] -->
		<div class="form-line">
		    Hint File : <input type="file" class="form-control" name="hint_file[{{$qtype1['questiontype_settings']['id']}}][{{$i-1}}]" id="hint_file_{{$qtype['id']}}_{{$i}}" placeholder="Hint File">
		    @if(!empty($item->hint_file))
			<?php $fileurl = config("constants.APP_IMAGE_URL").'image/qb/'.$item->hint_file; ?>
			<img src="{{ $fileurl }}" width="90%"  style="    max-height: 300px;">

			@endif
		</div> 
	</div>
	@endif  

	<div class="form-group form-float float-left col-md-1"> 
		<div class="form-line">
		<a style="color:red;cursor:pointer"  onclick="delete_question({{$item->id}})"><i class="fas fa-trash"></i></a>
		</div> 
	</div>

	{{-- @if($qtype['questiontype_settings']['display_answer']==1) 
	<div class="form-group form-float float-left col-md-6"> 
		<div class="form-line">
		    <input type="text" class="form-control" name="display_answer[{{$qtype1['questiontype_settings']['id']}}][]" id="answer_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Answer" value="{{$item->display_answer}}">
		</div> 
	</div>
	@endif   --}}

</div>