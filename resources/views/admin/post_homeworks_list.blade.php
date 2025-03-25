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
        <div class="row mb-5 mt-5 ">
            <input type="hidden" name="pagename" id="pagename" value="communcation_posts">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-1 p-3 ms-md-5 ms-sm-2 elevation-5 br-10"  style="    background: #fff;">
                

                @if(!empty($post['homeworks_list']))
                    <table class="table table-striped table-bordered">
                    
                    @foreach($post['homeworks_list'] as $hk=>$hw)
                        @if($hk == 0)
                        <thead>
                            <tr>
                                <th colspan="2">
                                <div class="d-flex activity activityimage">
                                    <?php //$post['posted_user']['profile_image'] = '';    ?>
                                    @if(empty($post['posted_user']->profile_image))
                                    <?php $shortcode = $post['posted_user']->is_shortname; ?>
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
                                    <img src="{{$post['posted_user']->is_profile_image}}" class="img-responsive img-circle">
                                    @endif
                                    <?php 
                                        $namecode = $post['posted_user']->name_code;
                                        if(empty($namecode)) {
                                            $namecode = $post['posted_user']->name;
                                            $v = '';
                                            if(preg_match_all('/\b(\w)/',strtoupper($namecode),$m)) {
                                                $v = implode('',$m[1]); 
                                            }
                                            if(!empty($v)) {
                                                $namecode = $v;
                                            }
                                        }
                                    ?>
                                    <p class="mt-2 ml-3 col-md-4 text-left"><b>@if($hw['is_all'] == 1) All @endif Homework</b> <br> 
                                    Date : {{date('d M, Y h:i A', strtotime($hw['hw_date']))}} <br> {{$namecode}}</p> 

                                    <div class=" float-left col-md-6 receiverslist" id="likeact_{{$id}}" >  
                                    @if(!empty($post['is_post_receivers']))
                                        @foreach($post['is_post_receivers'] as $receivers) <?php //echo "<pre>"; print_r($receivers['name']); exit; ?>
                                     <p class=" btn-info" style="display: inline-block; padding: 2px;">{{$receivers['name']}}  {{$receivers['name1']}}</p> 
                                        @endforeach 
                                    @endif  

                                    </div>  
                                </div>  

                                </th>
                            </tr>
                        </thead>
                        @endif 
                        @if($hw['main_ref_no'] == $hw['ref_no']) 
                        <tr><td style="width: 20%">{{$hw['is_subject_name']}}</td>
                            <td style="width: 80%">{{$hw['hw_description']}} </td>
                        </tr> 
                        @endif 
                    @endforeach
                    </table>

                        @if(!empty($hw['is_file_attachments']))
                            <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                             @foreach($hw['is_file_attachments'] as $imga)
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

                        @if(!empty($hw['is_hw_attachment']))
                            <div class="col-md-12 justify-content-between likeicon mt-3 ms-4"> 
                                <label>Homework File</label>
                                <a href="{{$hw['is_hw_attachment']}}" target="_blank"><img src="{{asset('/public/images/freefile.png')}}" height="30" width="30"></a> 
                            </div>
                        @endif 
                        @if(!empty($hw['is_dt_attachment']))
                            <div class="col-md-12 justify-content-between likeicon mt-3 ms-4"> 
                                <label>Daily Task File</label>
                                <a href="{{$hw['is_dt_attachment']}}" target="_blank"><img src="{{asset('/public/images/freefile.png')}}" height="30" width="30"></a> 
                            </div>
                        @endif 

                @endif  

                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                    <div class=" ">  
                        <div class="likeact float-left" id="likeact_{{$id}}" >  
                            <a href="javascript:void(0);" onclick="openpoststatus({{$post['id']}});" data-toggle="tooltip" data-placement="top" title="Post Status">
                                <p>{{$post['is_read_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['acknowledged_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['hw_submitted_count']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                            </a>
                        </div>   

                        <div class="  col-md-3 float-right"> {{$post['is_created_ago']}}</div>

                        <?php //if($post['approve_status'] == 'UNAPPROVED') {  ?> 
                        <div class=" col-md-2 ml-5 float-right"> 
                            @if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                            <a href="#" onclick="deleteactivity({{$id}})"  title="Delete Homework" style="padding-left:1%;"> <img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                            @endif
                            @if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                            <a href="javascript:void(0);" class="ml-3" onclick="loadTopics({{$id}})"  title="Edit Homework" style="padding-left:1%;"> <img class="deleteact w-15" src="{{asset('/public/images/edit 1.png')}}"></a>
                            @endif
                        </div>
                        <?php //} ?>
                        @if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['status_update'] == 1)) || ($user_type == 'SCHOOL'))
                        @if($hw['approve_status'] == 'UNAPPROVED')
                        <div class=" float-right col-md-3 mr-3"> 
                        <select class="form-control ml-3" name="post_status" onchange="updatestatus(this, {{$id}});">
                            <option value="">Select Status</option>
                            <option value="APPROVED">Approved</option>
                            <option value="UNAPPROVED">Un Approved</option>
                        </select>
                        </div>
                        @endif 
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