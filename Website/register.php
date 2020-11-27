<?php
require 'functions/includes.php';

if(isset($_POST['username'], $_POST['password'], $_POST['confirmpassword'])){
    if($_POST['password'] !== $_POST['confirmpassword']) {
        die('password is different from confirm password');
    }

    $code = auth\owner\register($connection, $_POST['username'], $_POST['password']);

    if($code !== 2){
        die('invalid password \'regex\' or already used name');
    }

    header('Location: login.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FireFrame</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
  </nav>

    <div class="container h-100 d-flex justify-content-center mt-5 pt-5 ">
        <div class="col-lg-4 pt-5">
        <div class="alert alert-dismissible alert-warning">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Registrations are currently disabled!</strong>
                  </div>
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                Register
                </div>
                <form method="POST" class="p-3">
                    <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp" placeholder="Username" disabled required>
                    </div>
                    <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" disabled required>
                    </div>
                    <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" disabled required>
                    </div>
                    <center>
                        <button type="submit" class="btn btn-light border w-100 mt-2 mb-2" disabled>Register</button>
                        <?php 
                            echo '<a href="' . process_link("login.php", true) . '" class="text-right">Already registered?</a>'; 
                        ?>
                    </center>
                </form>
            </div>
        </div>
    </div>
    <footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
      <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
    </footer>
</body>
</html>
