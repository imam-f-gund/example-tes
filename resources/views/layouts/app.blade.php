<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

     <!-- SweetAlert2 CSS -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

     <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
     <!-- SweetAlert2 JS -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
     
       <!-- Load jQuery from CDN -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <!-- Load DataTables JS from CDN -->
      <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

      <!-- Load Bootstrap JS if you're using it -->
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    
    <title>Home</title>
  </head>
  <body>
  <div>
    
  <nav class="navbar navbar-expand-lg navbar-light bg-dark">
    @if(Auth::user()->name == 'admin')

      <a class="navbar-brand text-white ho" href="{{url('/products')}}">Data Product</a>
      <a class="navbar-brand text-white" href="{{url('/customers')}}">Data Customer</a>
      <a class="navbar-brand text-white" href="{{url('/report')}}">Report</a>

    @elseif(Auth::user()->name == 'operator')

      <a class="navbar-brand text-white"  href="{{url('/operator')}}">Operator</a>
      <a class="navbar-brand text-white" href="{{url('/customers')}}">Data Customer</a>
      <a class="navbar-brand text-white" href="{{url('/report')}}">Report</a>

    @else

      <a class="navbar-brand text-white">Customer</a>
      
    @endif
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
    </ul>
    
  </div>
  <div class="navbar-collapse collapse w-50 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
           <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          {{ Auth::user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" id="logout"  href="{{ route('logout') }}">Logout</a>
        </div>
      </li>
        </ul>
  </div>
</nav>
<div>
<script type="text/javascript">
  $('#logout').click(function(){
    localStorage.removeItem("status-login");
  });
</script>

<!-- <body> -->
<body>
  @if (session('success'))
  <script>
      Swal.fire({
          icon: 'success',
          title: 'Success',
          text: '{{ session('success') }}',
      });
  </script>
@endif

@if (session('error'))
  <script>
      Swal.fire({
          icon: 'error',
          title: 'Error',
          text: '{{ session('error') }}',
      });
  </script>
@endif

    @yield('content')

</body>