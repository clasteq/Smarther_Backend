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
                    <?php //$post['posted_user']['profile_image'] = '';    ?>
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
                    <p class="mt-2 ml-3 col-md-4"><b>{{$post['post_category']}} </b><br> 
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

                    @if(!empty($post['is_post_cc_receivers']))
                     <br/> CC :   @foreach($post['is_post_cc_receivers'] as $receivers)
                     <p class=" btn-info" style="display: inline-block; padding: 2px;">{{$receivers->name}}  {{$receivers->name1}}</p> 
                        @endforeach 
                    @endif 

                    </div>   
                </div>
                <div class="activitycontent mt-1 center">
                    <h6>{{$post['title']}}</h6>
                </div>  
                <?php 
                    if(isset($post['post_theme'])) {
                        $img = $post['post_theme']['is_image'];
                    } else {
                        $img = '';
                    }
                    $is_category_text_color = $post['is_category_text_color'];
                    $style = "background-image:url('".$img."'); background-size: cover;  background-repeat: no-repeat;";
                    if(!empty($is_category_text_color)) {
                        $style .= " color:".$is_category_text_color." !important; ";
                    }
                    if(empty($img)) {
                        $style .= " color:#000 !important; ";
                    }
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
                @if(isset($post['is_youtube_link']) && !empty($post['is_youtube_link']))   
                <div class="col-md-12 justify-content-between center mt-3 ms-4">
                    <iframe width="90%" height="320" src="{{$post['is_youtube_link']}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe> 
                </div>  
                @endif 

                @if(isset($post['youtube_link']) && !empty($post['youtube_link']))   
                <div class="activitycontent mt-3 center">
                    <p>{{$post['youtube_link']}}</p>
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

                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class="activitycontent {{$class}}" style="{{$style}}">
                      <p>{!! $post['message'] !!}</p>
                    </div> 
                </div> 

                @if(!empty($post['files_attachment']))
                    <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                     @foreach($post['is_files_attachment'] as $imga)
                        <?php $extension = pathinfo($imga['img'], PATHINFO_EXTENSION); 
                        $extension = trim(strtolower($extension));
                        $iurl = asset('/public/images/file.png');
                        if($extension == 'pdf')  {
                            $iurl = asset('/public/images/pdf2.png');
                        } else if($extension == 'ppt' || $extension == 'pptx') {
                            $iurl = asset('/public/images/ppt.png');
                        } else if($extension == 'doc' || $extension == 'docx') {
                            $iurl = asset('/public/images/doc.png');
                        } else if($extension == 'xls' || $extension == 'xlsx') {
                            $iurl = asset('/public/images/xls.png');
                        } else {
                            $iurl = asset('/public/images/file.png');
                        }
                        ?><!-- asset('/public/images/freefile.png') -->
                        <a href="{{$imga['img']}}" target="_blank"><img src="{{$iurl}}" height="40" width="40"></a>
                     @endforeach
                    </div>
                @endif
                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">
                       <div class="likeact float-left" id="likeact_{{$id}}" > 
                        <!--  href="{{URL('/')}}/admin/poststatus?id={{$post['id']}}" -->
                        <a href="javascript:void(0);" onclick="openpoststatus({{$post['id']}});" data-toggle="tooltip" data-placement="top" title="Post Status">
                         <p>{{$post['is_read_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['acknowledged_count']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                         </a>
                        </div>   
                        
                        <p class=" float-right">{{$post['is_created_ago']}}</p>
                        <?php //if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) { 
                        //if($post['status'] == 'PENDING')  { ?>
                        @if((isset($session_module['Posts']) && ($session_module['Posts']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                        <a class="col-md-1 float-right" href="{{URL('/')}}/admin/editposts?id={{$post['id']}}" title="Edit post" ><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
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