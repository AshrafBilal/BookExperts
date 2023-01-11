@section('after_footer')
@if ($message = Session::get('success'))
    <script>
      Swal.fire({
      icon: 'success',
      text: "{{ $message }}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
    Swal.fire({
      icon: 'error',
      text: "{{ $message }}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
   	Swal.fire({
      icon: 'warning',
      text: "{{ $message }}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif




@if($status = session('status'))
      <script>
   	Swal.fire({
      icon: 'success',
      text: "{{  $status  }}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif

@if ($message = Session::get('info'))
    <script>
    Swal.fire({
      icon: 'info',
      text: "{{ $message }}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif

@if (session()->has('error'))
	<script>
    Swal.fire({
      icon: 'error',
      text: "{{session()->get('error')}}",
      showConfirmButton: false,
      timer: "{{env('MESSAGE_ALERT_TIMEOUT')}}"
	});
    </script>
@endif
@endsection
