<?php
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
        <div class="row mb-5 mt-5 ">
            <input type="hidden" name="pagename" id="pagename" value="communcation_posts">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
                <div class="d-flex activity activityimage">
                    <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle">
                    <p class="mt-2 ms-3"><b>{{$post['post_category']}} </b><br> {{$post['posted_user']['name_code']}} <br> 
                    Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p> 
                    <?php if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) {  ?>
                    <a href="{{URL('/')}}/teacher/editposts?id={{$post['id']}}" title="Edit post" style="padding-left:60%;display: none;"><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                    
                    <a href="#" onclick="deleteactivity({{$id}})"  title="Delete post" style="padding-left:1%;"><img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                    <?php } ?>
                </div>
                <div class="activitycontent mt-3">
                    <p>{{$post['title']}}</p>
                </div>  
                <?php $img = $post['post_theme']['is_image'];
                    $style = "background-image:url('".$img."'); background-size: cover;  background-repeat: no-repeat;";
                    $class = "offerolympiaimg";
                    if(!empty($post['image_attachment'])) {
                      $style = ''; $class = 'offerolympia';
                    }
                    $ogg = '';
                    if(!empty($post['media_attachment'])) {
                      $infoext = pathinfo($post['media_attachment']);
                      $ogg = $infoext['filename']. '.ogg';
                    }

                    $vogg = '';
                    if(!empty($post['video_attachment'])) {
                      $infoext = pathinfo($post['video_attachment']);
                      $vogg = $infoext['filename']. '.ogg';
                    }

                ?>
                  <div class="activitycontent {{$class}}" style="{{$style}}">
                      <p>{!! $post['message'] !!}</p>
                  </div>  
                  @if(!empty($post['media_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <audio controls>
                      <source src="{{$ogg}}" type="audio/ogg">
                      <source src="{{$post['is_attachment']}}" type="audio/mpeg">
                    Your browser does not support the audio element.
                    </audio>
                  </div>
                  @endif
                  @if(!empty($post['image_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                     @foreach($post['is_image_attachment'] as $imga)
                        <img src="{{$imga['img']}}" height="100" width="100">
                     @endforeach
                  </div>
                  @endif
                  @if(!empty($post['files_attachment']))
                    <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                     @foreach($post['is_files_attachment'] as $imga)
                        <a href="{{$imga['img']}}" target="_blank"><img src="{{asset('/public/images/freefile.png')}}" height="30" width="30"></a>
                     @endforeach
                    </div>
                  @endif
                  @if(!empty($post['video_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                  <video width="400" controls>
                    <source src="{{$post['is_video_attachment']}}" type="video/mp4">
                    <source src="{{$vogg}}" type="video/ogg">
                    Your browser does not support HTML video.
                  </video>
                  </div>
                  @endif
                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">
                       <div class="likeact float-left" id="likeact_{{$id}}" > <a href="{{URL('/')}}/teacher/poststatus?id={{$post['id']}}" target="_blank">
                         <p>{{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['users_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['acknowledged_count']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                         </a>
                        </div>  
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