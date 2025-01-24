<?php //echo "<pre>"; print_r($subjects); exit; ?>
@if(!empty($homeworks))
	@if(!empty($homeworks['homeworks_list']) && count($homeworks['homeworks_list'])>0)
		@foreach($homeworks['homeworks_list'] as $subj)
			<div class="edit_subject-homework-row">
				<input type="hidden" name="subject_hw_id[]" id="edit_subject_hw_id_0" value="{{$subj['id']}}">
			    <div class="form-group form-float float-left col-md-6">
			        <label class="form-label">Subject</label>
			        <div class="form-line">
			            <select class="form-control subject_id" name="subject_id[]" id="edit_subject_id_0" required>
			                <option value="">Select Subject</option>
			                @if (!empty($subjects))
			                    @foreach ($subjects as $course)
			                    	@php($selected = '')
			                    	@if($subj['subject_id'] == $course->id) @php($selected = 'selected') @endif
			                        <option value="{{ $course->id }}" {{$selected}}>{{ $course->subject_name }}</option>
			                    @endforeach
			                @endif
			            </select>
			        </div>
			    </div>
			    <div class="form-group form-float float-left col-md-6">
			        <label class="form-label">Home Work Details <span class="manstar">*</span></label>
			        <div class="form-line">
			            <textarea class="form-control hw_description" name="hw_description[]" id="edit_hw_description_0"  rows="3" cols="30" required>{{$subj['hw_description']}}</textarea>
			        </div>
			        <div class="">
			            <button type="button" class="btn btn-success edit_add-subject-homework"><i class="fas fa-plus"></i></button>
			            <button type="button" class="btn btn-danger edit_delete-subject-homework"><i class="fas fa-trash"></i></button>
			        </div>
			    </div>
			</div>
		@endforeach
	@endif
@endif