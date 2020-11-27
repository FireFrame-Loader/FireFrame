<?php

require '../functions/includes.php';
require '../functions/session.php';

session_regenerate_id();

if(!session_valid()) {
    header('Location: ' . process_link('login.php', false));
    die('invalid login');
}

$username = $_SESSION['username'];

if (isset($_GET['logout'])) {
    header("Location: " . process_link("logout.php",true));
}

if (isset($_POST['options']) || isset($_POST['options1']))  {
    header("Location: https://shoppy.gg/product/E8y0550");
}

$expiry = auth\owner\fetch($connection, $username)['expires'];

$account_type = get_account_type($expiry, $username);

$code_switcher = static function($code){
    switch($code){
        case 1:
            return 'old password is wrong';

        case 2:
            return 'success';

        case 3:
            return 'password isn\'t strong enough';

        case 4:
            return 'wrong license code';

        default:
            return '?';
    }
};

if(isset($_POST['oldpassword'], $_POST['newpassword'], $_POST['confirmpassword'])){
    if($_POST['newpassword'] !== $_POST['confirmpassword']) {
        die('your password and confirmation password don\'t match');
    }

    $code = auth\owner\change_password($connection, $username, $_POST['oldpassword'], $_POST['newpassword']);

    die($code_switcher($code));
}

if (isset($_POST['confirmdelpassword'])) {
    $code = auth\owner\delete_account($connection, $username, $_POST['confirmdelpassword']);

    if($code !== 2) {
        die($code_switcher($code));
    }

    header("Location: " . process_link("logout.php",true));
    exit;
}

if(isset($_POST['subkey'])) {
    $code = auth\owner\activate_license($connection, $username, $_POST['subkey']);

    die($code_switcher($code));
}

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
background: url(../background.jpg) no-repeat center center fixed;
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
            <a class="nav-item nav-link" href="index.php">Loader</a>
            <a class="nav-item nav-link" href="users.php">Users</a>
            <a class="nav-item nav-link" href="licenses.php">Licenses</a>
            <a class="nav-item nav-link" href="modules.php">Modules</a>
            <a class="nav-item nav-link" href="">|</a>
            <a class="nav-item nav-link" href="<?php if (is_onion()) echo 'https://firefra.me'; else echo 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php if (is_onion()) echo 'Clearnet'; else echo 'Tor'; ?></a>
            <a class="nav-item nav-link" href="<?php echo process_link("tos.php",false); ?>">ToS & PP</a>
            <a class="nav-item nav-link" href="https://discord.gg/2bAH7AsxFx"><img src="../Discord-Logo-White.png" width="30" height="30"></a>
            <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="../Telegram-Logo.png" width="28" height="28"></a>
        </div>
    </div>
    <form class="form-inline p-0 m-0" action="logout.php">
    <span class="navbar-text text-light text-right mr-3">
        User : <?php echo $username;?>
    </span>
        <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Logout</button>

        <button class="navbar-toggler ml-3 " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </form>
</nav>

<div class="container h-100 d-flex justify-content-center pt-5 ">
    <div class="col-lg-10">
        <div class="card text-white bg-dark shadow py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col-md mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Subscription</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Expires : <?php
                            switch($account_type){
                                case 'Normal':
                                    echo 'Expired';
                                    break;

                                case 'Premium':
                                    echo $expiry !== -1 ? date("F j, Y, g:i a", $expiry) : 'Never';
                                    break;

                                case 'Admin':
                                    echo 'Pogger';
                                    break;
                        } ?></div>
                    </div>
                    <div class="col-md-auto">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Account Type</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Type : <?php echo $account_type; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container h-100 d-flex justify-content-center pt-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Account Information
            </div>
            <div class="row">
                <div class="col-md mr-2">
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label for="exampleloadername">Reset Password</label>
                            <input type="password" class="form-control mt-2" id="oldpassword" name="oldpassword" aria-describedby="poo" placeholder="Old Password" required>
                            <input type="password" class="form-control mt-2" id="newpassword" name="newpassword" aria-describedby="poo" placeholder="New Password" required>
                            <input type="password" class="form-control mt-2" id="confirmpassword" name="confirmpassword" aria-describedby="poo" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change</button>
                    </form>
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label for="disabledTextInput">Delete Account</label>
                            <input type="password" class="form-control mt-2" id="confirmdelpassword" name="confirmdelpassword" aria-describedby="poo" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </div>
                <div class="col-md mr-2">
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label for="examplesubjey">Purchase FireFrame Subscription</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-secondary active">
                                    <input type="radio" name="options" id="option1" checked> 30 Days
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" name="options1" id="option2"> Lifetime
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Purchase</button>
                    </form>
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label for="examplesubjey">Redeem FireFrame Key</label>
                            <input type="text" class="form-control" id="subkey" name="subkey" aria-describedby="poo" placeholder="Subscription Key" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Activate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
    <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
</footer>
</body>
</html>
