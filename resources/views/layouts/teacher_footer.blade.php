<!-- REQUIRED SCRIPTS -->
<input type="hidden" name="getFetchSectionURL" id="getFetchSectionURL"
  value="{{ url('admin/fetch-section') }}">
  <input type="hidden" name="getFetchExamURL" id="getFetchExamURL"
  value="{{ url('admin/fetch-exams') }}">
  <input type="hidden" name="getFetchSubjectURL" id="getFetchSubjectURL"
  value="{{ url('admin/fetch-subject') }}">
  <input type="hidden" name="getFetchSubjectSectionsURL" id="getFetchSubjectSectionsURL"
  value="{{URL::to('admin/fetch-subjectsection')}}">
<!-- jQuery -->
<script src="{{asset('/public/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('/public/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('/public/dist/js/adminlte.min.js')}}"></script>

<script src="{{asset('/public/js/sweetalert.min.js') }}"></script>

  <script src="{{asset('/public/js/jquery-form.js') }}"></script>
  <script src="{{asset('/public/js/common.js')}}" type="text/javascript"></script>
  <script src="{{asset('/public/js/functions.js')}}" type="text/javascript"></script>

<!-- AdminLTE for demo purposes --> 

<!-- DataTables  & Plugins -->
<script src="{{asset('/public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('/public/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('/public/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('/public/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('/public/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('/public/js/bootstrap-datepicker.min.js')}}"></script>