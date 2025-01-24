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
                    <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle">
                    <p class="mt-2 ml-3"><b>{{$post['post_category']}} </b><br> {{$post['posted_user']['name_code']}} <br> 
                    Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p> 
                    <?php if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) {  ?>
                    @if((isset($session_module['SMS']) && ($session_module['SMS']['edit'] == 1)) || ($user_type == 'SCHOOL'))    
                    <a href="{{URL('/')}}/admin/editpostsms?id={{$post['id']}}" title="Edit post" style="padding-left:60%;display: none;"><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                    @endif
                    @if((isset($session_module['SMS']) && ($session_module['SMS']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                    <a href="#" onclick="deletepostsms({{$id}})"  title="Delete post" style="padding-left:1%;"><img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                    @endif
                    <?php } ?>
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
                        @if((isset($session_module['SMS']) && ($session_module['SMS']['status_update'] == 1)) || ($user_type == 'SCHOOL'))
                        @if($post['status'] == 'PENDING')
                        <p class=" float-right"> 
                        <select class="form-control ml-3" name="post_status" onchange="updatesmsstatus(this, {{$id}});">
                            <option value="">Select Status</option>
                            <option value="ACTIVE">Approved</option>
                            <option value="INACTIVE">Rejected</option>
                        </select>
                        </p>
                        @endif
                        @endif
                        <p class=" float-right">{{$post['is_created_ago']}}</p> 
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