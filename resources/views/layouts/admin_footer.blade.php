<!-- REQUIRED SCRIPTS -->
<input type="hidden" name="getBranchStoresURL" id="getBranchStoresURL" value="{{URL('/')}}/admin/getAjaxBranchStores">

<input type="hidden" name="loaddistricts" id="loaddistricts" value="{{URL('/')}}/admin/fetch-districts">
 
@if(isset($editor) && ($editor == 1))  
<!-- include jquery 
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>-->
@else   
<!-- jQuery -->
<script src="{{asset('/public/plugins/jquery/jquery.min.js')}}"></script> 
@endif

<!-- Bootstrap 4 -->
<script src="{{asset('/public/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('/public/dist/js/adminlte.min.js')}}"></script>

<input type="hidden" name="getChapterOptionsURL" id="getChapterOptionsURL"
	value="{{URL::to('admin/fetch-chapters')}}">

  <input type="hidden" name="getChapterTopicsOptionsURL" id="getChapterTopicsOptionsURL"
	value="{{URL::to('admin/fetch-chapterstopics')}}">

  <input type="hidden" name="checkChapterQbURL" id="checkChapterQbURL"
  value="{{URL::to('admin/check-chapterqb')}}"> 
  
  <input type="hidden" name="getFetchSubjectSectionsURL" id="getFetchSubjectSectionsURL"
  value="{{URL::to('admin/fetch-subjectsection')}}">


<script src="{{asset('/public/js/sweetalert.min.js') }}"></script>

  <script src="{{asset('/public/js/jquery-form.js') }}"></script>
  <script src="{{asset('/public/js/common.js?123')}}" type="text/javascript"></script>
  <script src="{{asset('/public/js/functions.js')}}" type="text/javascript"></script>

<!-- AdminLTE for demo purposes --> 

<!-- DataTables  & Plugins -->
<script src="{{asset('/public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('/public/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('/public/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('/public/js/bootstrap-datepicker.min.js')}}"></script>