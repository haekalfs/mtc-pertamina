<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Site Maintenances</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>
<body>
<!-- Error 404 Template 1 - Bootstrap Brain Component -->
<section class="py-3 py-md-5 min-vh-100 d-flex justify-content-center align-items-center">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="text-center">
            <img src="{{ asset('img/mtc-logo.png') }}" class="mb-2 mr-2" style="height: 40px; width: 40px;" /> <span class="font-weight-bold">MTC Performance</span>
            <h2 class="d-flex justify-content-center align-items-center gap-2 mb-4">
              <span class="display-1 fw-bold">5</span>
              <span class="display-1 fw-bold bsb-flip-h">0</span>
              <span class="display-1 fw-bold bsb-flip-h">3</span>
            </h2>
            <h3 class="h2 mb-2">Site Maintenances!</h3>
            <p>This is not error from website, This site is under maintenances.</p>
            <p class="mb-5">Please contact the administrator if you believe this is an error.</p>
            <a class="btn bsb-btn-5xl btn-dark rounded-pill px-5 fs-6 m-0" href="{{ route('dashboard') }}" role="button">Back to Home</a>
          </div>
        </div>
      </div>
    </div>
  </section>
    <!-- Optional Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
