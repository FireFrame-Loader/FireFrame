<?php
include_once('utils.php');

start_session();

session_regenerate_id();

check_login();

$username = $_SESSION['username'];

if (isset($_GET['logout'])) {
    header("Location: " . process_link("logout.php",true));
}

$code = 0;

$expires = $_SESSION['expires'];

$expire_text = get_expiration($expires);

$acc_type = get_acc_type($username,$expire_text);

function change_password($connection, $old_password, $new_password, $username){
    if(!validate_password($new_password))
        return 4;
    
    $query = $connection->query('SELECT * FROM users WHERE username=?', [$username]);   
    $quer_data = $query->fetch_assoc();

    if(!password_verify($old_password, $quer_data['password']))
        return 2;

    $new_password = password_hash($new_password, PASSWORD_DEFAULT);

    $connection->query('UPDATE users SET password=? WHERE username=?', [$new_password, $username]);

    return 3;
}

if(isset($_POST['oldpassword'], $_POST['newpassword'], $_POST['confirmpassword'])){
    $code = $_POST['newpassword'] === $_POST['confirmpassword'] ? change_password($connection, $_POST['oldpassword'], $_POST['newpassword'], $username) : 1;
}

if (isset($_POST['confirmdelpassword'])) {
    $confirm_password = $_POST['confirmdelpassword'];
    
    $query = $connection->query('SELECT * FROM users WHERE username=?', [$username]);

    $row = $query->fetch_assoc();

    if(password_verify($confirm_password, $row['password'])){
        delete_account($username,$connection);
        header("Location: " . process_link("logout.php",true));
    }

    $code = 2;
}

if (isset($_POST['options']) || isset($_POST['options1']))  {
    header("Location: https://shoppy.gg/product/E8y0550");
}

function use_license($connection, $sub_key, $username, $expires){
    $query = $connection->query('SELECT * FROM licenses WHERE code=?', [$sub_key]);

    if($query->num_rows === 0)
        return 5;

    $row = $query->fetch_assoc();

    $key_type = $row['type'];

    activate_license($username, $sub_key, $key_type, $connection, $expires);
    
    $expires = $_SESSION['expires']; // ??????

    $expire_text = get_expiration($expires); // ??????????

    return 6;
}

if(isset($_POST['subkey']))
   $code =  use_license($connection, $_POST['subkey'], $username, $expires);
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
        <a class="nav-item nav-link" href="https://discord.gg/xPtevhPHQp"><img src="Discord-Logo-White.png" width="30" height="30"></a>  
        <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="Telegram-Logo.png" width="28" height="28"></a> 
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Expires : <?php echo $expire_text; ?></div>
                        </div>
                        <div class="col-md-auto">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Account Type</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Type : <?php echo $acc_type; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container h-100 d-flex justify-content-center pt-5">
        <div class="col-lg-10">
        <?php
    switch($code) {
        case 1: //pass dont match
            echo '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>New passwords do not match!</strong>
          </div>';
        break;
        case 2: //wrong password
            echo '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Current password is wrong!</strong>
          </div>';
        break;
        case 3: //success
            echo '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Password changed successfully!</strong>
          </div>';
        break;
        case 4: //not enough strong pass
            echo '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</strong>
          </div>'; 
        break;
        case 5:
            echo '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Wrong license code!</strong>
          </div>';  //invalid license
        break;
        case 6:
            echo '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>License activated!</strong>
          </div>'; //activated license
        break;
    }
    ?>
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
