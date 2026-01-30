<!DOCTYPE html>
<html>
<head>
  <title>EATSY</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
  <link rel="stylesheet" href="styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- begining of navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img  src="img/logo.png"  alt="" width="100" height="60"  class="left">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Help</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                More
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#">Popular</a></li>
                <li><a class="dropdown-item" href="login.html">Login</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="spin_wheel.html">Spin a wheel</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Offers</a>
            </li>
          </ul>
          <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
          </form>
        </div>
      </div>
    </nav>
<!-- end of navigation bar -->


<!-- carousel start  -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="false">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/1.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/2.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">

      </div>
    </div>
    <div class="carousel-item">
      <img src="img/3.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">

      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<!--carousel end -->





<!-- start of cards -->
<div class="card-grid">
  <?php
  $card = '
    <div class="card">
      <img src="img/3.png" alt="Food Image">
      <div class="card-body">
        <h3>Dish Name</h3>
        <h4>Delivery Time</h4>
        <h4>Price</h4>
        <h5>Rating</h5>
        <p>Location</p>
      </div>
    </div>';
  for ($i = 1; $i <= 9; $i++) {
    echo $card;
  }
  ?>
  <!-- stend  of cards -->
</div>
<!-- script of scrollCarousel -->
<script>
  function scrollCarousel(direction) {
    const container = document.getElementById('carousel');
    const scrollAmount = 160;
    container.scrollBy({
      left: direction * scrollAmount,
      behavior: 'smooth'
    });
  }
  
</script>
  <br>
  </br>
<!-- end of script of scrollCarousel -->

<!-- footer -->
<footer style="background-color:#c9d2d6; padding: 40px 20px; font-family: Arial, sans-serif;">
  <div style="max-width: 1200px; margin: auto;">
    <div style="text-align: center; margin-bottom: 20px;">
      <h2 style="font-weight: bold;">For better experience,<span style="color: #000;"> download the EATSY app now</span></h2>
      <a href="https://play.google.com/store/search?q=eatsy&c=apps"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play" style="height: 50px; margin: 10px;"></a>
      <a href="https://apps.apple.com/us/app/eatsy/id1234567890"><img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="Download on the App Store" style="height: 50px; margin: 10px;"></a>
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; text-align: left;">
      <div style="flex: 1; min-width: 180px; margin-bottom: 20px;">
        <img src="img/logo.png" alt="Logo" style="height: 40px;">
        <p style="margin-top: 10px; font-size: 14px;">© 2025 EATSY Limited</p>
      </div>

      <div style="flex: 1; min-width: 180px; margin-bottom: 20px;">
        <h4>Company</h4>
        <ul style="list-style: none; padding: 0; font-size: 14px;">
          <li><a href="#">About Us</a></li>
          <li><a href="#">EATSY Corporate</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Team</a></li>
          <li><a href="#">EATSY One</a></li>
          <li><a href="#">EATSY chefs</a></li>
          <li><a href="restaurant_login.html">Restaurant Login</a></li>
        </ul>
      </div>

      <div style="flex: 1; min-width: 180px; margin-bottom: 20px;">
        <h4>Contact us</h4>
        <ul style="list-style: none; padding: 0; font-size: 14px;">
          <li><a href="#">Help & Support</a></li>
          <li><a href="#">Partner with us</a></li>
          <li><a href="#">Ride with us</a></li>
        </ul>
      </div>

      <div style="flex: 1; min-width: 180px; margin-bottom: 20px;">
        <h4>Available in:</h4>
        <ul style="list-style: none; padding: 0; font-size: 14px;">
          <li>Bangalore</li>
          <li>Gurgaon</li>
          <li>Hyderabad</li>
          <li>Delhi</li>
          <li>Mumbai</li>
          <li>Pune</li>
     
        </ul>
      </div>

      <div style="flex: 1; min-width: 180px; margin-bottom: 20px;">
        <h4>Life at EATSY</h4>
        <ul style="list-style: none; padding: 0; font-size: 14px;">
          <li><a href="#">Explore with EATSY</a></li>
          <li><a href="#">EATSY News</a></li>
          <li><a href="#">Snackables</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>

  <!-- end of footer -->
</body>
</html>
