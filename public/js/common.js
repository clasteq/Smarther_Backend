function checkChapterQb(val,selectedid,selectedval) {

    selectedid = selectedid || '';
    selectedval = selectedval || '';
 
    var selval = val; 
    var selid = selectedid; 
    var selectvalue = selectedval; 
    var qb_id = $('#question_bank_id').val(); 
    var class_id = $('#class_id').val();
    var subject_id = $('#subject_id').val();

    $.ajax({
        url: $('#checkChapterQbURL').val(),
        type: "POST",
        dataType: 'json',
        data: {
            class_id: class_id, subject_id:subject_id, chapter_id:val, qb_id:qb_id
        },
        async:true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        
        success: function (res) {   
            if(res.status == 'FAILED') {
                 swal("Oops!", res.message, "error");
            } 
        }
    });
}

function loadChapterOptions(val,selectedid,selectedval,termid) {

    selectedid = selectedid || '';
    selectedval = selectedval || '';
 
    var selval = val;
    var selval = $('#subject_id').val();
    if(selval == '' || selval == null)  { 
        selval = val;
    } 
    console.log('subject_id',selval);
    var selid = selectedid;
    console.log('selid',selid);
    var selectvalue = selectedval;
    console.log('selectvalue',selectvalue);
    var class_id = $('#class_id').val();
    var term_id = $('#term_id').val(); 
    if(term_id == '' || term_id == null)  {
        term_id = termid;
    }
    $("#chapter_dropdown,#edit_chapter_dropdown").html('');
    $.ajax({
        url: $('#getChapterOptionsURL').val(),
        type: "POST",
        dataType: 'json',
        data: {
            subject_id: selval, class_id:class_id,term_id:term_id
        },
        async:true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        
        success: function (res) {
            console.log('res', res);
           
            /*if(selid != null && selectvalue != null){
                $("#edit_chapter_dropdown").append('<option selected value="' + selid + '">' + selectvalue + '</option>');
                $.each(res.chapter, function (key, value) {
                    $("#chapter_dropdown").append('<option  value="' +
                        value.id + '">' + value.chaptername + '</option>');
                });
            }else{
                $('#chapter_dropdown,#edit_chapter_dropdown').html(
                    '<option value="0">-- Select Chapter --</option>');
                    $.each(res.chapter, function (key, value) {
                        $("#chapter_dropdown,#edit_chapter_dropdown").append('<option  value="' +
                            value.id + '">' + value.chaptername + '</option>');
                    });
            }*/
            $('#chapter_dropdown,#edit_chapter_dropdown').html(
                '<option value="">-- Select Chapter --</option>');
                $.each(res.chapter, function (key, value) {
                    var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                    $("#chapter_dropdown,#edit_chapter_dropdown").append('<option  value="' +
                        value.id + '" '+selected+'>' + value.chaptername + '</option>');
                });
            
           
            
        }
    });
}


function loadChapterTopicsOptions(val,selectedid,selectedval) {

    selectedid = selectedid || 0;
    selectedval = selectedval || 0;
 
    var selval = val;
    console.log('subject_id',selval);
    var selid = selectedid;
    console.log('selid',selid);
    var selectvalue = selectedval;
    console.log('selectvalue',selectvalue);

    $("#chapter_topics_dropdown,#edit_chapter_topics_dropdown").html('');
    $.ajax({
        url: $('#getChapterTopicsOptionsURL').val(),
        type: "POST",
        dataType: 'json',
        data: {
            chapter_id: selval,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        
        success: function (res) {
            console.log('res', res);
            $('#chapter_topics_dropdown').html(
                '<option value="0">-- Select Chapter --</option>');
            if(selid > 0 && selectvalue > 0){
                $("#edit_chapter_topics_dropdown").append('<option selected value="' + selid + '">' + selectvalue + '</option>');
               
            }
            $.each(res.chapter, function (key, value) {
                $("#chapter_topics_dropdown,#edit_chapter_topics_dropdown").append('<option  value="' +
                    value.id + '">' + value.chapter_topic_name + '</option>');
            });
           
            
        }
    });
}