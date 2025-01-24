<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>{{ config("constants.site_name") }}</title>

    <link rel="stylesheet" href="{{asset('/public/dist/css/adminlte.min.css')}}">
  </head>

<body> 
  <div class="services section" id="services">
    <div class="container">
         
        <div class="row">
          <div class="col-lg-12 col-md-12 p-3">
            {!!$content!!}
          </div>
        </div>
    </div>
  </div>
     

  </body>
</html>