 
          $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
                $('#section_dropdown').val('');
                $('#subject_id').val('');
                $('#test_dropdown').html('');
                $('#hw_date').change();

                if($('.subject-homework-row').length > 1) {
                    var rowindex;  var rowlen = $('.subject-homework-row').length-1;
                    for(rowindex = rowlen; rowindex>=1; rowindex--) {
                        console.log(rowindex); 
                        $('.subject-homework-row')[rowindex].remove();
                    } 
                    $('.add-subject-homework').removeClass('disabled')
                    $('.add-subject-homework').prop('disabled', false)
                }
            });

        $(function() {

              
            $('#addtopics').on('click', function() {
                $('#style-form .course_id').trigger('change');
            });


            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            filterposts();

                            $('#smallModal').modal('hide');

                            $("#style-form")[0].reset();

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });


            $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            filterposts();

                            $('#smallModal-2').modal('hide');

                            $("#edit-style-form")[0].reset();

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });



        });

        function loadTopics(id) {

            $('#edit-style-form .hw_view_file').addClass('d-none');
            $('#edit-style-form #hw_view_file').attr('href', '#');
            $('#edit-style-form .dt_view_file').addClass('d-none');
            $('#edit-style-form #dt_view_file').attr('href', '#');

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/homeworkgrp') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);
                $('#edit_class_id').val(response.data.class_id);

                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.is_section_name;
                loadClassSection(val, selectedid, selectedval);

                $('#edit_section_id').val(response.data.section_id);
                $('#edit_subject_id').val(response.data.subject_id);
                loadClassSubjects(response.data.section_id, response.data.subject_id, response.data.subject_id);

                testList(response.data.subject_id,response.data.class_id,response.data.is_test_id, response.data.is_test_id)

                $('#edit_test_dropdown').val(response.data.is_test_id);
                // response.data.teachers.is_subject_id
                // $('#edit_period').val(response.data.period);
                $('#edit_hw_title').val(response.data.hw_title);
                $('#edit_hw_description').val(response.data.hw_description);
                $('#edit_hw_date').val(response.data.hw_date);
                $('#edit_hw_submission_date').val(response.data.hw_submission_date);
                $('#edit_position').val(response.data.position);
                $('#edit_status').val(response.data.status);
                $('#edit_approve_status').val(response.data.approve_status);

                if (response.data.homework_file != '' && response.data.homework_file != null) {
                    $('#edit-style-form .hw_view_file').removeClass('d-none');
                    $('#edit-style-form #hw_view_file').attr('href', response.data.is_hw_attachment);
                    $('#edit-style-form #is_hw_attachment').val(response.data.is_hw_attachment);
                }

                if (response.data.dailytask_file != '' && response.data.dailytask_file != null) {
                    $('#edit-style-form .dt_view_file').removeClass('d-none');
                    $('#edit-style-form #dt_view_file').attr('href', response.data.is_dt_attachment);
                    $('#edit-style-form #is_dt_attachment').val(response.data.is_dt_attachment);
                }

                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }



        function myFunction(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    if (selid != null && selval != null) {

                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');

                    } else {
                        $('#section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    }
                    $.each(res.section, function(key, value) {
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

        function testList(val,class_id,selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var subject_id = val;
            var selid = selectedid;
            var selval = selectedval;

            class_id = class_id;

            $("#test_dropdown,#edit_test_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-tests') }}",
                type: "POST",
                data: {
                    subject_id: subject_id,
                    class_id:class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#test_dropdown').html('<option value="">-- Select Test --</option>');
                    $.each(res.tests, function(key, value) {
                        var selected = '';
                        var arr = selectedid.toString().split(',');
                        var result = arr.map(function (x) {
                            return parseInt(x, 10);
                        });
                        if(result.indexOf(value.id) !== -1) {
                            selected = ' selected ';
                        }
                        $("#test_dropdown,#edit_test_dropdown").append('<option value="' + value.id + '" '+selected+'>' + value.test_name + ' '+ value.from_date + ' to '+ value.to_date + '</option>');
                    });
                }
            });
        }


        $('#hw_date').change(function() {
            date = this.value;
            date1 = date.split('T')[0];
            date2 = date.split('T')[1];
            date3 = '09:30:00';

            var someDate = new Date(date1);
            someDate.setDate(someDate.getDate() + 1); //number  of days to add, e.x. 15 days
            var dateFormated = someDate.toISOString().substr(0,10);
            console.log(dateFormated);
            fin_date = dateFormated+'T'+date3;

              $('#hw_submission_date').val(fin_date);

        });




        $('#edit_hw_date').change(function() {
            date = this.value;
            date1 = date.split('T')[0];
            date2 = date.split('T')[1];
            date3 = '09:30:00';

            var someDate = new Date(date1);
            someDate.setDate(someDate.getDate() + 1); //number  of days to add, e.x. 15 days
            var dateFormated = someDate.toISOString().substr(0,10);
            console.log(dateFormated);
            fin_date = dateFormated+'T'+date3;

            $('#edit_hw_submission_date').val(fin_date);

        });


        function loadClassSectionHw(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#sectionid").html('');
            $.ajax({
                url: $('#getFetchSectionURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('#sectionid').html(
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
                        $("#sectionid").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadClassSubjectsHw(val, selectedid, selectedval, isclass) {
 
            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            isclass = isclass || 0;
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $(".subject_id").html('');
            $.ajax({
                url: $('#getFetchSubjectURL').val(),
                type: "POST",
                data: {
                    section_id: section_id,isclass:isclass,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('.subject_id').html(
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
                        $(".subject_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }

        //$('#edit_hw_date').change(); 

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('subject-homework-container');
            const checkbox = document.getElementById('send_sms_checkbox');
            let maxRows = 3; // Default value

            function updateMaxRows() {
                maxRows = checkbox.checked ? 3 : 6;
                toggleAddButton();
            }

            container.addEventListener('click', function(event) {
                const clickedButton = event.target.closest('button');
                if (!clickedButton) return;

                if (clickedButton.classList.contains('delete-subject-homework')) {
                    if (container.querySelectorAll('.subject-homework-row').length > 1) {
                        clickedButton.closest('.subject-homework-row').remove();
                        toggleAddButton();
                    }
                } else if (clickedButton.classList.contains('add-subject-homework')) {



                    if (checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        return;
                    }

                    const newRow = container.querySelector('.subject-homework-row').cloneNode(true);

                    // Clear the values of the cloned row
                    newRow.querySelectorAll('input, select, textarea').forEach(function(element) {
                        element.value = '';
                    });

                    // Add event listener to the new select element
                    newRow.querySelector('select.subject_id').addEventListener('change', function() {
                        if (checkDuplicateSubjects()) {
                            swal('Oops', 'This subject has already been selected.', 'warning');
                            this.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    toggleAddButton();
                }
            });

            function toggleAddButton() {
                const rows = container.querySelectorAll('.subject-homework-row');
                const addButton = container.querySelectorAll('.add-subject-homework');
                addButton.forEach(function(button) {
                    button.disabled = rows.length >= maxRows;
                    button.classList.toggle('disabled', rows.length >= maxRows);
                });
            }

            function checkDuplicateSubjects() {
                const subjects = [];
                let hasDuplicate = false;
                container.querySelectorAll('.subject_id').forEach(function(select) {
                    if (select.value && subjects.includes(select.value)) {
                        hasDuplicate = true;
                    } else {
                        subjects.push(select.value);
                    }
                });
                return hasDuplicate;
            }

            // Attach change event to existing select elements
            container.querySelectorAll('select.subject_id').forEach(function(select) {
                select.addEventListener('change', function() {
                    if (checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        this.value = '';
                    }
                });
            });

            // Event listener for checkbox state change
            checkbox.addEventListener('change', updateMaxRows);

            // Initial call to set the add button state
            updateMaxRows();
        });  