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
        <div class="row mb-5 mt-5 ">
            <input type="hidden" name="pagename" id="pagename" value="communcation_posts">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
            @foreach($posts_arr as $ak => $post)
            @php($id = $post['id'])
            <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
                <div class="d-flex activity activityimage">
                    <img src="{{$post['posted_user']->is_profile_image}}" class="img-responsive img-circle">
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
                    <p class="mt-2 ms-3">  {{$namecode}}</p> 
                </div>

                @if(!empty($post['homeworks_list']))
                    <table class="table table-striped table-bordered">
                    
                    @foreach($post['homeworks_list'] as $hk=>$hw)
                        @if($hk == 0)
                        <thead>
                            <tr><th colspan="2"><b>{{$hw['is_class_name']}} - {{$hw['is_section_name']}}</b> <br> 
                                    Date : {{date('d M, Y h:i A', strtotime($hw['hw_date']))}}
                            </th></tr>
                        </thead>
                        @endif  
                        <tr><td style="width: 20%">{{$hw['is_subject_name']}}</td>
                            <td style="width: 80%">{{$hw['hw_description']}} </td>
                        </tr> 
                    @endforeach
                    </table>

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
                        <div class="  col-md-3 float-right"> {{$post['is_created_ago']}}</div>

                        <?php if($post['approve_status'] == 'UNAPPROVED') {  ?> 
                        <div class=" col-md-1 ml-5 float-right"> 
                            @if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                            <a href="#" onclick="deleteactivity({{$id}})"  title="Delete Homework" style="padding-left:1%;"> <img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                            @endif
                            @if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                            <a href="#" class="ml-3" onclick="loadTopics({{$id}})"  title="Edit Homework" style="padding-left:1%;"> <img class="deleteact w-15" src="{{asset('/public/images/edit 1.png')}}"></a>
                            @endif
                        </div>
                        <?php } ?>
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