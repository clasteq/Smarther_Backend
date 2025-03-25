<?php $user_type = Auth::User()->user_type;
    $prsize = sizeof($posts); 
    $links = $posts->links();
    if($prsize > 0) {
        $arr = $posts->toArray();
        if(isset($arr['data']) ){
            $posts_arr = $arr['data']; //echo "<pre>"; print_r($posts_arr); exit;
        }
    }
    
?>
@if($prsize > 0)
<section class="posts pagination_section">
    <div class="container">
        <div class="row mb-5 mt-5 " style="    background: #ebd2cf;">
            <input type="hidden" name="pagename" id="pagename" value="communcation_survey">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-1 p-3 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                <div class="d-flex activity activityimage">
                    <?php //$post['posted_user']['profile_image'] = '';    ?>
                    @if(empty($post['posted_user']['profile_image']))
                    <?php $shortcode = $post['posted_user']['is_shortname']; ?>
                    <svg class="col-md-2" height="100" width="100">
                      <defs>
                        <linearGradient id="grad1">
                          <stop offset="0%" stop-color="#FF6F61" />
                          <stop offset="100%" stop-color="#FF6F61" />
                        </linearGradient>
                      </defs>
                      <ellipse cx="60" cy="40" rx="40" ry="40" fill="url(#grad1)" />
                      <text fill="#ffffff" font-size="35" font-family="Verdana" x="35" y="55">{{$shortcode}}</text>
                      Sorry, your browser does not support inline SVG.
                    </svg>
                    @else 
                    <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                    @endif
                    <p class="mt-2 ml-3 col-md-4"> {{$post['posted_user']['name_code']}} <br> 
                    Expiry At: {{date('d M, Y', strtotime($post['expiry_date']))}}</p>  
                    <div class=" float-left col-md-6 receiverslist" id="likeact_{{$id}}" >  
                    @if(!empty($post['is_post_receivers']))
                    Scholars :     @foreach($post['is_post_receivers'] as $receivers)
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">{{$receivers->name}}  {{$receivers->name1}}</p> 
                        @endforeach
                    @elseif($post['scholar_post_type'] == 3)
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">All</p> 
                    @endif 

                    @if(!empty($post['is_staff_receivers']))
                    <br/>Staffs :    @foreach($post['is_staff_receivers'] as $receivers)
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">{{$receivers->name}}  {{$receivers->name1}}</p> 
                        @endforeach
                    @elseif($post['staff_post_type'] == 3) 
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">All</p> 
                    @endif 
                    </div>   
                </div>
                <div class="activitycontent mt-1 center d-none">
                    <h6>{{$post['survey_question']}}</h6>
                </div>    


                <div class="activitycontent mt-1 center">
                    <canvas id="myChart{{$post['id']}}" style="width:100%;max-width:600px"></canvas> 
                </div>   


                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">
                       <div class="likeact float-left" id="likeact_{{$id}}" > 
                        <!--  href="{{URL('/')}}/admin/poststatus?id={{$post['id']}}" -->
                        <a href="javascript:void(0);" onclick="openpoststatus({{$post['id']}});" data-toggle="tooltip" data-placement="top" title="Post Status">
                         <p>{{$post['is_read_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['sent_count'] + $post['sent_count_staff']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['acknowledged_count'] + $post['acknowledged_count_staff']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                         </a>
                        </div>   
                        
                        <p class=" float-right">{{$post['is_created_ago']}}</p>
                        <?php //if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) { 
                        //if($post['status'] == 'PENDING')  { ?>
                        @if((isset($session_module['Posts']) && ($session_module['Posts']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                        @if(strtotime($post['expiry_date']) > strtotime(date('Y-m-d')))
                        <a class="col-md-1 float-right" href="{{URL('/')}}/admin/editsurvey?id={{$post['id']}}" title="Edit Survey" ><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                        @endif
                        @endif
                        <?php //} ?>
                        @if((isset($session_module['Posts']) && ($session_module['Posts']['status_update'] == 1)) || ($user_type == 'SCHOOL'))
                        
                        @if($post['status'] == 'PENDING')
                        <a class="mr-1 float-right" href="#" onclick="confirmactivity({{$id}}, 'ACTIVE')"  title="Confirm post" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/confirm.png')}}"></a>
                        <a class="mr-1 float-right" href="#" onclick="confirmactivity({{$id}}, 'INACTIVE')"  title="Reject post" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/rejected.png')}}"></a>

                        <p class=" float-right d-none"> 
                        <select class="form-control ml-3" name="post_status" onchange="updatestatus(this, {{$id}});">
                            <option value="">Select Status</option>
                            <option value="ACTIVE">Approved</option>
                            <option value="INACTIVE">Rejected</option>
                        </select>
                        </p> 
                        @endif

                        <a class="col-md-1 float-right" href="#" onclick="deleteactivity({{$id}})"  title="Delete post" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/delete.png')}}"></a>
                        @endif
                    </div> 
                </div>

                <script>

                    var xValues = ["{{$post['survey_option1']}}", "{{$post['survey_option2']}}", "{{$post['survey_option3']}}", "{{$post['survey_option4']}}"];
                    var yValues = [{{$post['is_responded_option1']}}, {{$post['is_responded_option2']}}, {{$post['is_responded_option3']}}, {{$post['is_responded_option4']}}];
                    var barColors = ["red", "green","blue","orange"];
                    var elem = "myChart"+"{{$post['id']}}";
                    new Chart(elem, {
                      type: "bar",
                      data: {
                        labels: xValues,
                        datasets: [{
                          backgroundColor: barColors,
                          data: yValues
                        }]
                      },
                      options: {
                        legend: {display: false},
                        title: {
                          display: true,
                          text: "{{$post['survey_question']}}"
                        }
                      }
                    });
                </script> 

            </div>
            @endforeach 
        </div>
        <div style="margin:auto;width: 100%; ">
            {!! $links !!} 
        </div>
    </div>


    
</section>
@else
<div class="d-flex imageupload justify-content-center mt-3"> 
    <p>No Posts</p>
</div> 
@endif  