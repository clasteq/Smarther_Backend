function loadStates() {  
    if($('.state_id').length >0) {
        $('.state_id').html('<option value="">Select State</option>');
    }
    var country_id  = $('#country_id').val();
    var stateid = $('#hstateid').val();
    if(country_id > 0) {
        var request = $.ajax({
            type: 'post',
            url: $('#loadstates').val(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                stateid:stateid,country_id:country_id
            },
            dataType:'json',
            encode: true
        });
        request.done(function (response) {
            if(response.status == 1) { 
                $('.state_id').html(response.data);
                var district_id  = $('#hdistrictid').val();
                if(district_id > 0) {
                  $('.state_id').trigger('onchange');
                }
            }
            else
                $('.state_id').html('<option value="">Select State</option>');
        });
        request.fail(function (jqXHR, textStatus) {
            $('.state_id').html('<option value="">Select State</option>');
        });
    }   else {
        $('.state_id').html('<option value="">Select State</option>');
    }
}

function loadDistricts1($obj) {
    if($('.pincode_id').length >0) {
        $('.pincode_id').html('<option value="">Select Pincode</option>');
    }
    var districtid  = $('#hdistrictid').val();  
    var stateid = $($obj).val();
    if(stateid > 0) {
        var request = $.ajax({
            type: 'post',
            url: $('#loaddistricts').val(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                state_id:stateid,districtid:districtid
            },
            dataType:'json',
            encode: true,
            async: false
        });
        request.done(function (response) {
            /*if(response.status == 1) {
                $('.district_id').html(response.data);
                var pincodeid  = $('#hpincodeid').val();
                if(pincodeid > 0) {
                  $('.district_id').trigger('onchange');
                }
            }
            else
                $('.district_id').html('<option value="">Select City</option>');*/

            $('.district_id').html(
                '<option value="">-- Select City --</option>'); 
            $.each(response.districts, function(key, value) {
              var selected = '';
              if (districtid != null) {
                   if(districtid == value.id) {
                    selected = ' selected ';
                   }
              }
                $(".district_id").append('<option value="' + value
                    .id + '" '+selected+'>' + value.district_name + '</option>');
            });
        });
        request.fail(function (jqXHR, textStatus) {
            $('.district_id').html('<option value="">Select City</option>');
        });
    }   else {
        $('.district_id').html('<option value="">Select City</option>');
    }
}

function isDecimal(evt, obj) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)  && (charCode != 46 || $(obj).val().indexOf('.') != -1)) {
        return false;
    }
    return true;
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function checkbalance($obj) {
    var max_val = $($obj).attr('max');  max_val = parseInt(max_val);  
    var entered_val = $($obj).val();  entered_val = parseInt(entered_val);   
    if(entered_val > max_val) {
        $($obj).val(max_val);
    }
}

function loadClasses(term_id) {  
    if($('.class_id').length >0) {
        $('.class_id').html('<option value="">Select Class</option>');
    }
    var school_code  = $('#school_code').val(); 
    /*if($('#term_id').length >0) {
        term_id  = $('#term_id').val();
    }*/
    
    var classid = $('#hclass_id').val();
    if($.trim(school_code) != '') {
        var request = $.ajax({
            type: 'post',
            url: $('#loadclasses').val(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                school_code:school_code,classid:classid, term_id:term_id
            },
            dataType:'json',
            encode: true
        });
        request.done(function (response) {
            if(response.status == "SUCCESS") { 
                $('.class_id').html(response.data); 
            }
            else
                $('.class_id').html('<option value="">Select Class</option>');
        });
        request.fail(function (jqXHR, textStatus) {
            $('.class_id').html('<option value="">Select Class</option>');
        });
    }   else {
        $('.class_id').html('<option value="">Select Class</option>');
    }
}


        function loadClassSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: $('#getFetchSectionURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json', 
                success: function(res) {

                    $('#section_dropdown,#edit_section_dropdown').html(
                        '<option value="">-- Select Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadClassExams(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#exam_id").html('');
            $.ajax({
                url: $('#getFetchExamURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('#exam_id').html(
                        '<option value="">-- Select Exam --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.exams, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#exam_id").append('<option value="' + value
                            .id + '" '+selected+' data-startdate="'+value.monthyear+'">' + value.exam_name + '</option>');
                    });
                }
            });
        }

        function loadClassSubjects(val, selectedid, selectedval, isclass) {
 
            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            isclass = isclass || 0;  
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#subject_id,#edit_subject_id").html('');
            $.ajax({
                url: $('#getFetchSubjectURL').val(),
                type: "POST",
                data: {
                    section_id: section_id,isclass:isclass,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                async:true,
                success: function(res) {

                    $('#subject_id,#edit_subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.subjects, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#subject_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }

        function loadSubjectSections(val, selectedid, selectedval) {
 
            selectedid = selectedid || " ";
            selectedval = selectedval || " "; 
            var subject_id = val;
            var selid = selectedid;
            var selval = selectedval;
            var class_id = $('#class_id').val();
            $("#section_dropdown").html('');
            $.ajax({
                url: $('#getFetchSubjectSectionsURL').val(),
                type: "POST",
                data: {
                    subject_id: subject_id,class_id:class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('#section_dropdown').html(
                        '<option value="">-- Select Section --</option>'); 
                    $.each(res.sections, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });

                    $("#section_dropdown").select2();
                }
            });
        }

        /*$("#exam_id").change(function() {
              var selectedItem = $(this).val();
              var abc = $('option:selected',this).data("startdate");
              $('#monthyear').val(monthyear);
        });*/

        function loadmonthyear(){
            var monthyear = $('#exam_id').find(':selected').data('startdate');
            $('#monthyear').val(monthyear);
        }