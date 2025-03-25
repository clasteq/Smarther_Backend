<?php $user_type = Auth::User()->user_type;
    $prsize = sizeof($posts); 
    $links = $posts->links();
    if($prsize > 0) {
        $arr = $posts->toArray();
        if(isset($arr['data']) ){
            $posts_arr = $arr['data']; // echo "<pre>"; print_r($posts_arr); exit;
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

            @if($post['type_no'] == 7)

            <div class="col-md-12 post mt-1 p-3 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                <div class="d-flex activity activityimage">
                    <?php //$post['posted_user']['profile_image'] = '';    ?>
                    @if(empty($post['post_posted_user']['profile_image']))
                    <?php $shortcode = $post['post_posted_user']['is_shortname']; ?>
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
                    <img src="{{$post['post_posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                    @endif
                    <p class="mt-2 ml-3 col-md-4"> {{$post['post_posted_user']['name_code']}} <br> 
                    Expiry At: {{date('d M, Y', strtotime($post['notify_date']))}}</p>   
                </div>
                <div class="activitycontent mt-1 center">
                    <h6>{{$post['is_details']->survey_question}}</h6>
                </div>    

                <div class="activitycontent mt-1 center">
                     <p class=" float-left">Expires At : {{date('d M Y', strtotime($post['is_details']->expiry_date)) }}</p> 
                </div>  

                @if($post['notify_response'] > 0) 

                <div class="activitycontent mt-1">
                    @if(!empty($post['is_details']->survey_option1))
                    <div class="col-md-12 float-left"> 
                     <p class=""> <label for="survey_{{$post['id']}}_1" class="ml-2 @if($post['notify_response']  == 1) text-green @endif">{{$post['is_details']->survey_option1}}</label></p> 
                    </div> 
                    @endif 
                    @if(!empty($post['is_details']->survey_option2))
                    <div class="col-md-12 float-left"> 
                     <p class=""> <label for="survey_{{$post['id']}}_2" class="ml-2 @if($post['notify_response']  == 2) text-green @endif">{{$post['is_details']->survey_option2}}</label></p> 
                    </div>  
                    @endif 
                    @if(!empty($post['is_details']->survey_option3))
                    <div class="col-md-12 float-left"> 
                     <p class=""> <label for="survey_{{$post['id']}}_3" class="ml-2 @if($post['notify_response']  == 3) text-green @endif">{{$post['is_details']->survey_option3}}</label></p> 
                    </div>  
                    @endif 
                    @if(!empty($post['is_details']->survey_option1))
                    <div class="col-md-12 float-left"> 
                     <p class=""> <label for="survey_{{$post['id']}}_4" class="ml-2 @if($post['notify_response']  == 4) text-green @endif">{{$post['is_details']->survey_option4}}</label></p> 
                    </div>  
                    @endif 
                </div>  

                @else 

                <div class="activitycontent mt-1">
                    @if(!empty($post['is_details']->survey_option1))
                    <div class="col-md-12 float-left"> 
                     <p class=""><input type="radio" name="survey[{{$post['id']}}]" id="survey_{{$post['id']}}_1" value="1" onclick="updatesurvey({{$post['id']}}, 1);"><label for="survey_{{$post['id']}}_1" class="ml-2">{{$post['is_details']->survey_option1}}</label></p> 
                    </div> 
                    @endif 
                    @if(!empty($post['is_details']->survey_option2))
                    <div class="col-md-12 float-left"> 
                     <p class=""><input type="radio" name="survey[{{$post['id']}}]" id="survey_{{$post['id']}}_2" value="2" onclick="updatesurvey({{$post['id']}}, 2);"><label for="survey_{{$post['id']}}_2" class="ml-2">{{$post['is_details']->survey_option2}}</label></p> 
                    </div>  
                    @endif 
                    @if(!empty($post['is_details']->survey_option3))
                    <div class="col-md-12 float-left"> 
                     <p class=""><input type="radio" name="survey[{{$post['id']}}]" id="survey_{{$post['id']}}_3" value="3" onclick="updatesurvey({{$post['id']}}, 3);"><label for="survey_{{$post['id']}}_3" class="ml-2">{{$post['is_details']->survey_option3}}</label></p> 
                    </div>  
                    @endif 
                    @if(!empty($post['is_details']->survey_option1))
                    <div class="col-md-12 float-left"> 
                     <p class=""><input type="radio" name="survey[{{$post['id']}}]" id="survey_{{$post['id']}}_4" value="4" onclick="updatesurvey({{$post['id']}}, 4);"><label for="survey_{{$post['id']}}_4" class="ml-2">{{$post['is_details']->survey_option4}}</label></p> 
                    </div>  
                    @endif 
                </div>  

                @endif

                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" "> 
                        <p class=" float-right">{{$post['post_created_ago']}}</p> 
                    </div> 
                </div> 

            </div>

            @elseif($post['type_no'] == 6 || $post['type_no'] == 4)
            
            <div class="col-md-12 post mt-1 p-3 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                <div class="d-flex activity activityimage">
                    <?php //$post['posted_user']['profile_image'] = '';    ?>
                    @if(empty($post['post_posted_user']['profile_image']))
                    <?php $shortcode = $post['post_posted_user']['is_shortname']; ?>
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
                    <img src="{{$post['post_posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                    @endif

                    <p class="mt-2 ml-3 col-md-4"><b>{{$post['is_details']['post_category']}} </b><br> {{$post['post_posted_user']['name_code']}} <br> 
                    Notify At: {{date('d M, Y h:i A', strtotime($post['is_details']['notify_datetime']))}}</p>   
                    
                </div>
                <div class="activitycontent mt-1 center">
                    <p>{{$post['is_details']['title']}}</p>
                </div>  
                <?php $img = $post['is_details']['post_theme']['is_image'];
                    $style = "background-image:url('".$img."'); background-size: cover;  background-repeat: no-repeat;";
                    if(!empty($is_category_text_color)) {
                        $style .= " color:".$is_category_text_color." !important; ";
                    }
                    $class = "offerolympiaimg";
                    if(!empty($post['is_details']['image_attachment'])) {
                      $style = ''; $class = 'offerolympia';
                    }
                    $ogg = '';
                    if(!empty($post['is_details']['media_attachment'])) {
                      $infoext = pathinfo($post['is_details']['media_attachment']);
                      $ogg = $infoext['filename']. '.ogg';
                    }

                    $vogg = '';
                    if(!empty($post['is_details']['video_attachment'])) {
                      $infoext = pathinfo($post['is_details']['video_attachment']);
                      $vogg = $infoext['filename']. '.ogg';
                    }

                ?> 
                @if(isset($post['is_details']['is_youtube_link']) && !empty($post['is_details']['is_youtube_link']))   
                <div class="col-md-12 justify-content-between center mt-3 ms-4">
                    <iframe width="90%" height="320" src="{{$post['is_details']['is_youtube_link']}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe> 
                </div>  
                @endif 
                @if(isset($post['is_details']['youtube_link']) && !empty($post['is_details']['youtube_link']))   
                <div class="activitycontent mt-3 center">
                    <p>{{$post['is_details']['youtube_link']}}</p>
                </div>  
                @endif 
                  @if(!empty($post['is_details']['image_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">


                    <div id="demo{{$post['is_details']['id']}}" class="carousel slide" data-ride="carousel">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                        @foreach($post['is_details']['is_image_attachment'] as $bk => $bv)
                          @php($active = '')
                          @if($bk == 0) @php($active = 'active')  @endif
                          <li data-target="#demo{{$post['is_details']['id']}}" data-slide-to="{{$post['is_details']['id']}}_{{$bk}}" class="{{$active}}"></li> 
                        @endforeach
                        </ul>

                        <!-- The slideshow/carousel -->
                        <div class="carousel-inner">
                          @foreach($post['is_details']['is_image_attachment'] as $bk => $bv)
                          @php($active = '')
                          @if($bk == 0) @php($active = 'active')  @endif
                          <div class="carousel-item {{$active}} center justify-content-center">
                             <img src="{{$bv['img']}}" alt="{{$post['is_details']['id']}}_{{$bk}}"  style="height: 500px;margin-top: 10px;">
                          </div>
                          @endforeach 
                        </div>

                        <!-- Left and right controls/icons -->
                        <a class="carousel-control-prev" href="#demo{{$post['is_details']['id']}}" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"><b><<</b></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#demo{{$post['is_details']['id']}}" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"><b>>></b></span>
                          <span class="sr-only">Next</span>
                        </a>
                    </div>   
                  </div>
                  @endif
                  @if(!empty($post['is_details']['video_attachment']))
                  <div class="col-md-12 center likeicon mt-3 ms-4">
                  <video width="400" controls>
                    <source src="{{$post['is_details']['is_video_attachment']}}" type="video/mp4">
                    <source src="{{$vogg}}" type="video/ogg">
                    Your browser does not support HTML video.
                  </video>
                  </div>
                  @endif
                  @if(!empty($post['is_details']['media_attachment']))
                  <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <audio controls>
                      <source src="{{$ogg}}" type="audio/ogg">
                      <source src="{{$post['is_details']['is_attachment']}}" type="audio/mpeg">
                    Your browser does not support the audio element.
                    </audio>
                  </div>
                  @endif
                  @if(!empty($post['is_details']['files_attachment']))
                    <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                     @foreach($post['is_details']['is_files_attachment'] as $imga)
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
                    <div class="activitycontent {{$class}}" style="{{$style}}">
                      <p>{!! $post['is_details']['message'] !!}</p>
                    </div> 
                  </div> 
                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" "> 
                        <p class=" float-right">{{$post['is_details']['is_created_ago']}}</p> 

                    </div> 
                </div>
            </div>

            @endif 
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