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
            <input type="hidden" name="pagename" id="pagename" value="communcation_posts">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-1 p-3 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                <div class="d-flex activity activityimage">
                    <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                    <p class="mt-2 ml-3 col-md-8"><b>{{$post['post_category']}} </b><br> {{$post['posted_user']['name_code']}} <br> 
                    Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p>  
                    
                    <?php if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) {  ?>
                    @if((isset($session_module['Posts']) && ($session_module['Posts']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                    <a class="col-md-1" href="{{URL('/')}}/admin/editposts?id={{$post['id']}}" title="Edit post" style="padding-left:60%;display: none;"><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                    @endif
                    <?php } ?>

                    @if((isset($session_module['Posts']) && ($session_module['Posts']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                    <a class="col-md-1" href="#" onclick="deleteactivity({{$id}})"  title="Delete post" style="padding-left:1%;text-align: right;"><img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                    @endif
                    
                </div>
                <div class="activitycontent mt-3 center">
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
                @if(isset($post['youtube_link']) && !empty($post['youtube_link']))   
                <div class="activitycontent mt-3 center">
                    <p>{{$post['youtube_link']}}</p>
                </div>  
                @endif 
                @if(isset($post['is_youtube_link']) && !empty($post['is_youtube_link']))   
                <div class="col-md-12 justify-content-between center mt-3 ms-4">
                    <iframe width="90%" height="320" src="{{$post['is_youtube_link']}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe> 
                </div>  
                @endif 
                  @if(!empty($post['image_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">


                    <div id="demo{{$post['id']}}" class="carousel slide" data-ride="carousel">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                        @foreach($post['is_image_attachment'] as $bk => $bv)
                          @php($active = '')
                          @if($bk == 0) @php($active = 'active')  @endif
                          <li data-target="#demo{{$post['id']}}" data-slide-to="{{$post['id']}}_{{$bk}}" class="{{$active}}"></li> 
                        @endforeach
                        </ul>

                        <!-- The slideshow/carousel -->
                        <div class="carousel-inner">
                          @foreach($post['is_image_attachment'] as $bk => $bv)
                          @php($active = '')
                          @if($bk == 0) @php($active = 'active')  @endif
                          <div class="carousel-item {{$active}} center justify-content-center">
                             <img src="{{$bv['img']}}" alt="{{$post['id']}}_{{$bk}}"  style="height: 500px;margin-top: 10px;">
                          </div>
                          @endforeach 
                        </div>

                        <!-- Left and right controls/icons -->
                        <a class="carousel-control-prev" href="#demo{{$post['id']}}" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"><b><<</b></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#demo{{$post['id']}}" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"><b>>></b></span>
                          <span class="sr-only">Next</span>
                        </a>
                    </div>   
                  </div>
                  @endif
                  @if(!empty($post['video_attachment']))
                  <div class="col-md-12 center likeicon mt-3 ms-4">
                  <video width="400" controls>
                    <source src="{{$post['is_video_attachment']}}" type="video/mp4">
                    <source src="{{$vogg}}" type="video/ogg">
                    Your browser does not support HTML video.
                  </video>
                  </div>
                  @endif
                  @if(!empty($post['media_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <audio controls>
                      <source src="{{$ogg}}" type="audio/ogg">
                      <source src="{{$post['is_attachment']}}" type="audio/mpeg">
                    Your browser does not support the audio element.
                    </audio>
                  </div>
                  @endif
                  @if(!empty($post['files_attachment']))
                    <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                     @foreach($post['is_files_attachment'] as $imga)
                        <?php $extension = pathinfo($imga['img'], PATHINFO_EXTENSION); 
                        $extension = trim(strtolower($extension));
                        $iurl = "asset('/public/images/file.png')";
                        if($extension == 'pdf')  {
                            $iurl = asset('/public/images/pdf2.png');
                        } else if($extension == 'ppt' || $extension == 'pptx') {
                            $iurl = asset('/public/images/ppt.png');
                        } else if($extension == 'doc' || $extension == 'docx') {
                            $iurl = asset('/public/images/doc.png');
                        } else if($extension == 'xls' || $extension == 'xlsx') {
                            $iurl = asset('/public/images/xls.png');
                        } else {
                            $iurl = "asset('/public/images/file.png')";
                        }
                        ?><!-- asset('/public/images/freefile.png') -->
                        <a href="{{$imga['img']}}" target="_blank"><img src="{{$iurl}}" height="40" width="40"></a>
                     @endforeach
                    </div>
                  @endif
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class="activitycontent {{$class}}" style="{{$style}}">
                      <p>{!! $post['message'] !!}</p>
                    </div> 
                  </div> 
                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">
                       <div class="likeact float-left" id="likeact_{{$id}}" > 
                        <!--  href="{{URL('/')}}/admin/poststatus?id={{$post['id']}}" -->
                        <a href="javascript:void(0);" onclick="openpoststatus({{$post['id']}});" data-toggle="tooltip" data-placement="top" title="Post Status">
                         <p>{{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['users_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['acknowledged_count']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                         </a>
                        </div>  
 
                        @if((isset($session_module['Posts']) && ($session_module['Posts']['status_update'] == 1)) || ($user_type == 'SCHOOL'))
                        @if($post['status'] == 'PENDING')
                        <p class=" float-right"> 
                        <select class="form-control ml-3" name="post_status" onchange="updatestatus(this, {{$id}});">
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

<!--  @foreach($post['is_image_attachment'] as $imga)
                        <div class="center"><img src="{{$imga['img']}}" style="width:80%;"></div>
                     @endforeach -->