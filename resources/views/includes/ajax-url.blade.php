<script>
    // global app routes
    var config = {
        routes: {
            adminLogout: "{{ route('logout') }}",
            activateOrSuspendUser: "{{ route('activateOrSuspendUser') }}",
            rejectAccount: "{{ route('rejectAccount') }}",
            approveAccount: "{{ route('approveAccount') }}",
            changeProfileStatus:"{{route('changeProfileStatus')}}",
            changeWorkProfileStatus:"{{route('changeWorkProfileStatus')}}",
            rejectCommon:"{{route('rejectCommon')}}",
            activateOrSuspendUsers: "{{ route('activateOrSuspendUsers') }}",
                
        }
    };
   
</script>

