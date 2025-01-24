@if(isset($academies) && count($academies)>0)
    @foreach($academies as $acad)
    <!-- team item -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="team-box effect-item mt-50">
            <div class="team-item content-overlay">
                <img src="{{$acad->is_profile_image}}" alt="" style="width:350px; height:250px;">
                <div class="content-details fadeIn-top">
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                    <p>@if(!empty($acad->descriptiontext))
                        {!! $acad->descriptiontext !!}
                       @else  
                        Over 50 group training sessions per week There is no one type or way in our diverse community.
                       @endif 
                    </p>
                </div>
            </div>
            <div class="team-title text-center">
                <h3><a href="{{URL('/')}}/academiesdetails/{{$acad->enc_id}}">{{$acad->name}}</a></h3>
                <p>Fitness & Body</p>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-md-12 mt-50 pagediv" style="margin:auto;width: 100%;">
        {{ $academies->links() }} 
    </div>
@else 
    <div class="team-title text-center"> 
        <p>No Academies found</p>
    </div>
@endif 