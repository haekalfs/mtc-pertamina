<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>

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
              <span class="display-1 fw-bold">4</span>
              <span class="display-1 fw-bold bsb-flip-h">1</span>
              <span class="display-1 fw-bold bsb-flip-h">9</span>
            </h2>
            <h3 class="h2 mb-2">Oops! You're idle to long & page is now expired, reload/refresh the page first to continue...</h3>
            <p class="mb-5">No need to contact administrator, just reload the page in the browser.</p>
            <a class="btn bsb-btn-5xl btn-dark rounded-pill px-5 fs-6 m-0" href="/login" role="button">Relog in</a>
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
