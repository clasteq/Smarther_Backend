$('#add_style').on('click', function () {
    var $error = 0;
    if($("#style-form").attr('name') == 'testiichecksheet') {
        var chkstatus = $('#passcomments').prop('checked');
        if(chkstatus === true) {
            if($.trim($("#comments").val()) == "") {
                var $error = 1;
                swal('Oops','Please enter the comments','error');
                return false;
            }
        }
    }
    if($error == 0) {
        var options = {

            beforeSend: function (element) {

                $("#add_style").text('Processing..');

                $("#add_style").prop('disabled', true);

            },
            success: function (response) {



                $("#add_style").prop('disabled', false);

                $("#add_style").text('SUBMIT');

                if (response.status == "SUCCESS") {
                    var jobcard_id = $('#jobcard_id').val();
                    var jobref_no = $('#jobref_no').val();
                    //swal('Success',response.message,'success');  

                    swal({
                      title: response.message,
                      
                      type: "success",
                      confirmButtonColor: "#DD6B55",
                      confirmButtonText: "OK",
                      closeOnConfirm: false,
                    },
                    function(inputValue){
                      //Use the "Strict Equality Comparison" to accept the user's input "false" as string)
                      if (inputValue===true) {
                        if(response.reurl != '') {
                            window.location.href = response.reurl;
                        }   else {
                            window.location.href = $('#getBranchViewJobCardURL').val()+'/'+jobcard_id+'/'+jobref_no;
                        }
                      }
                    });  

                }
                else if (response.status == "FAILED") {

                    swal('Oops',response.message,'warning');

                }

            },
            error: function (jqXHR, textStatus, errorThrown) {

                $("#add_style").prop('disabled', false);

                $("#add_style").text('SUBMIT');

                swal('Oops','Something went to wrong.','error');

            }
        };
        $("#style-form").ajaxForm(options);
    }
});

function loadJobDetails() {
    var jobcard_id = $('#jobcard_id').val();
    var request = $.ajax({
        type: 'post',
        url: $('#getBranchLoadJobCardURL').val(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            code:jobcard_id,
        },
        dataType:'json',
        encode: true
    });
    request.done(function (response) {
        if(response.status == 'SUCCESS') {
            $('#jobref_no').val(response.data.ref_no); 
            $('#jobcard_id').val(response.data.id); 
            $('#model_name').text(response.data.model_name); 
            $('#revision').text(response.data.revision); 
            $('#system_serial_number').text(response.data.system_serial_number); 
            $('#system_rating').text(response.data.rating_name); 
            $('#rating_name').text(response.data.rating_name); 
        }   else {
            $('#jobref_no').val(''); 
            $('#jobcard_id').val(jobcard_id); 
            $('#model_name').text(''); 
            $('#revision').text(''); 
            $('#system_serial_number').text(''); 
            $('#system_rating').text(''); 
            $('#rating_name').text(''); 
        }
        $('#jobref_no').val(response.data.ref_no); 
        $('#jobcard_id').val(response.data.id); 
        $('#model_name').text(response.data.model_name); 
        $('#revision').text(response.data.revision); 
        $('#system_serial_number').text(response.data.system_serial_number); 

    });
    request.fail(function (jqXHR, textStatus) {

        swal("Oops!", "Sorry,Could not process your request", "error");
    });
}  