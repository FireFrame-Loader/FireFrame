<?php
require 'functions/includes.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FireFrame</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>

</head>
<body style="
background: url(background.jpg) no-repeat center center fixed;
background-size: auto;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;">

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <?php
    echo '<a href="' . process_link("",false) . '" class="navbar-brand">FireFrame - Cheat Loaders</a>' ;
  ?>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-item nav-link" href="<?php if (is_onion()) echo 'https://firefra.me'; else echo 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php if (is_onion()) echo 'Clearnet'; else echo 'Tor'; ?></a>
          <a class="nav-item nav-link" href="<?php echo process_link("tos.php",false); ?>">ToS & PP</a>
        <a class="nav-item nav-link" href="https://discord.gg/2bAH7AsxFx"><img src="Discord-Logo-White.png" width="30" height="30"></a>  
        <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="Telegram-Logo.png" width="28" height="28"></a>  
        </div>
    </div>
    <form class="form-inline p-0 m-0" action="<?php echo process_link("login.php",false);?>">
      <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Sign In</button>

      <button class="navbar-toggler ml-3 " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </form>
  </nav>

  <div class="container h-100 d-flex justify-content-center pt-5">
      <div class="col-lg-10">
        <div class="jumbotron bg-dark text-light">
          <h1 class="display-4">FireFrame Cheat Loaders</h1>
          <hr>
            <p class="lead">FireFrame is a secure module loader for games.
            Our main focus is security as well as privacy for our users. Days of development
            has gone into making this project easy to use, effective, secure and undetectable.</p>
        </div>
      </div>
  </div>

  <div class="container h-100 d-flex justify-content-center pt-5" id="about">
      <div class="col-lg-10">
        <div class="row">
          <div class="col-sm-6 mb-4">
            <ul class="list-group">
              <li class="list-group-item bg-dark text-light text-center lead">
                <svg width="5em" height="5em" viewBox="0 0 16 16" class="bi bi-cpu" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M5 0a.5.5 0 0 1 .5.5V2h1V.5a.5.5 0 0 1 1 0V2h1V.5a.5.5 0 0 1 1 0V2h1V.5a.5.5 0 0 1 1 0V2A2.5 2.5 0 0 1 14 4.5h1.5a.5.5 0 0 1 0 1H14v1h1.5a.5.5 0 0 1 0 1H14v1h1.5a.5.5 0 0 1 0 1H14v1h1.5a.5.5 0 0 1 0 1H14a2.5 2.5 0 0 1-2.5 2.5v1.5a.5.5 0 0 1-1 0V14h-1v1.5a.5.5 0 0 1-1 0V14h-1v1.5a.5.5 0 0 1-1 0V14h-1v1.5a.5.5 0 0 1-1 0V14A2.5 2.5 0 0 1 2 11.5H.5a.5.5 0 0 1 0-1H2v-1H.5a.5.5 0 0 1 0-1H2v-1H.5a.5.5 0 0 1 0-1H2v-1H.5a.5.5 0 0 1 0-1H2A2.5 2.5 0 0 1 4.5 2V.5A.5.5 0 0 1 5 0zm-.5 3A1.5 1.5 0 0 0 3 4.5v7A1.5 1.5 0 0 0 4.5 13h7a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 11.5 3h-7zM5 6.5A1.5 1.5 0 0 1 6.5 5h3A1.5 1.5 0 0 1 11 6.5v3A1.5 1.5 0 0 1 9.5 11h-3A1.5 1.5 0 0 1 5 9.5v-3zM6.5 6a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                </svg>
              </li>
              <li class="list-group-item bg-dark text-light text-center lead"><b>High Performance</b></li>
              <li class="list-group-item bg-dark text-light h-40 text-center">High speed servers</li>
              <li class="list-group-item bg-dark text-light h-40 text-center">Unique code and practices</li>
              <li class="list-group-item bg-dark text-light h-40 text-center">Secure platform</li>
              <li class="list-group-item bg-dark text-light h-40 text-center">24/7 Support</li>
            </ul>
          </div>
          <div class="col-sm-6 mb-4">
            <ul class="list-group">
              <li class="list-group-item bg-dark text-light text-center lead">
                <svg width="5em" height="5em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                </svg>
              </li>
              <li class="list-group-item bg-dark text-light text-center lead"><b>Privacy Focused</b></li>
              <li class="list-group-item bg-dark text-light text-center">Tor network mirror</li>
              <li class="list-group-item bg-dark text-light text-center">Cryptocurrency support</li>
              <li class="list-group-item bg-dark text-light text-center">Minimum information required</li>
              <li class="list-group-item bg-dark text-light text-center">No logs</li>
            </ul>
          </div>
        </div>
      </div>
  </div>
    <div class="container h-100 d-flex justify-content-center pt-5 mb-5" id="pricing">
        <div class="col-lg-10">
          <div class="row">
            <div class="col-sm-6 mb-4">
              <div class="card mb-4 box-shadow bg-dark text-light">
                <div class="card-header">
                  <h4 class="my-0 font-weight-normal">Monthly</h4>
                </div>
                <div class="card-body">
                  <h1 class="card-title pricing-card-title">€?? <small class="text-muted">/ mo</small></h1>
                  <ul class="list-unstyled mt-3 mb-4">
                    <li>Unlimited users</li>
                    <li>Unlimited licenses</li>
                    <li>Unlimited modules</li>
                    <li>x86 & x64 injection</li>
                    <li>24/7 support</li>
                    <li>30 days dashboard access</li>
                  </ul>
                  <?php
                  $btn = process_link("register.php", false);
                  $btn = "window.location.href='" . $btn . "'";
                  echo '<button type="button" onclick="'. $btn . '" class="btn btn-lg btn-block btn-primary">Get started</button>';
                  ?>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mb-4">
              <div class="card mb-4 box-shadow bg-dark text-light">
                <div class="card-header">
                  <h4 class="my-0 font-weight-normal">Lifetime</h4>
                </div>
                <div class="card-body">
                  <h1 class="card-title pricing-card-title">€??</h1>
                  <ul class="list-unstyled mt-3 mb-4">
                    <li>Unlimited users</li>
                    <li>Unlimited licenses</li>
                    <li>Unlimited modules</li>
                    <li>x86 & x64 injection</li>
                    <li>24/7 support</li>
                    <li>Lifetime dashboard access</li>
                  </ul>
                  <?php
                  $btn = process_link("register.php", false);
                  $btn = "window.location.href='" . $btn . "'";
                  echo '<button type="button" onclick="'. $btn . '" class="btn btn-lg btn-block btn-primary">Get started</button>';
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
      <div class="footer-copyright text-center py-3">© 2020 FireFrame</div>
    </footer>
</body>
</html>
