var autocompleteoptions = {
    source: function( request, response ) {
 
        $objid =  this.element[0].id;  
        $formid = this.element.parents('form').attr('id'); console.log($formid)
        $.ajax({
          url: $('#baseurl').val()+'/get_places',
          method:"POST",
          dataType: "json",
          data: {
            term: request.term
          },
          headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
          success: function( data ) {
               var arr = [];
               var i = 0;
               var fullObj = data;
               $.each(data, function(index, value){
                   $.each(value, function(idx, v){
                       var obj = {
                           label: value['description'],
                           value: value['description'],
                           place_id: v
                       };
                       if(idx == "place_id"){
                           arr[i] = obj;
                           i++;
                       }
                   });
               });
               response(arr);
           }
       });
    },
    minLength: 2,
    select: function( event, ui ) { 
        $.ajax({
              url: $('#baseurl').val()+'/get_place_detail',
              method:"POST",
              dataType: "json",
              data: {
                place_id: ui.item.place_id
              },
              headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
              success: function( data ) {  console.log(data)
                $('#'+$formid+' #latitude').val(data['latitude']);
                $('#'+$formid+' #longitude').val(data['longitude']);
                /*$('#'+$formid+' #userset_area').val(data['locality']);
                $('#'+$formid+' #userset_pincode').val(data['postal_code']);*/
              }
        });
    },
};

 

$( ".googleaddress" ).autocomplete(autocompleteoptions);