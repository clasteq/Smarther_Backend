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
            <input type="hidden" name="pagename" id="pagename" value="communcation_postsms">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                <div class="d-flex m-3 activity activityimage">
                    <?php $shortcode = $post['posted_user']['is_shortname']; ?>
                    @if(empty($post['posted_user']['profile_image']))
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
                    <p class="mt-2 ml-3"><b>{{$post['post_category']}} </b><br> 
                    @if(!empty($post['posted_user']['name_code'])) {{$post['posted_user']['name_code']}} @else {{$shortcode}} @endif <br> 
                    Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p> 

                    <div class=" float-left col-md-6 receiverslist" id="likeact_{{$id}}" >  
                    @if(!empty($post['is_post_receivers']))
                        @foreach($post['is_post_receivers'] as $receivers)
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">{{$receivers->name}}  {{$receivers->name1}}</p> 
                        @endforeach
                    @else 
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">All</p> 
                    @endif  

                    </div>   
                </div>  
                <div class="activitycontent offerolympia" >
                    <p>{!! $post['content'] !!}</p>
                </div>  
                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">  <!--  href="{{URL('/')}}/admin/postsmsstatus?id={{$post['id']}}" target="_blank" -->
                        <div class="likeact float-left" id="likeact_{{$id}}" > 
                            <a href="javascript:void(0);" onclick="opensmsstatus({{$id}});" data-toggle="tooltip" data-placement="top" title="Post Status">
                                <p>{{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['users_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">   
                                </p> 
                            </a>
                        </div> 
                        <p class=" float-right">{{$post['is_created_ago']}}</p> 

                        @if((isset($session_module['Posts']) && ($session_module['Posts']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                        <a class="col-md-1 float-right" href="{{URL('/')}}/admin/editpostsms?id={{$post['id']}}" title="Edit sms" ><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                        @endif 

                        @if((isset($session_module['Posts']) && ($session_module['Posts']['status_update'] == 1)) || ($user_type == 'SCHOOL'))
                        
                        @if($post['status'] == 'PENDING')
                        <a class="mr-1 float-right" href="#" onclick="confirmactivity({{$id}}, 'ACTIVE')"  title="Confirm sms" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/confirm.png')}}"></a>
                        <a class="mr-1 float-right" href="#" onclick="confirmactivity({{$id}}, 'INACTIVE')"  title="Reject sms" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/rejected.png')}}"></a> 
                        @endif

                        <a class="col-md-1 float-right" href="#" onclick="deletepostsms({{$id}})"  title="Delete sms" style="padding-left:1%;text-align: right;"><img class="deleteact w-15 pointer" src="{{asset('/public/images/delete.png')}}"></a> 
                        @endif
    
                    </div> 
                </div>
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