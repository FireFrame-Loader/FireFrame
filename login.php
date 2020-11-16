<?php
include_once('utils.php');

start_session();

$fail = false;

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($connection->get_connection(), $_POST['username']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username='$username'";

    $query = $connection->query($sql);

    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            if (password_verify($password,$row['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['expires'] = $row['expires'];
                if ($row['expires'] <= time())
                    $_SESSION['expired'] = true;
                else
                    $_SESSION['expired'] = false;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                header("Location: " . process_link("index.php",true));
            } else {
                $fail = true;
            }
        }
    } else {
        $fail = true;
    }
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
        <a class="nav-item nav-link" href="https://discord.gg/xPtevhPHQp"><img src="Discord-Logo-White.png" width="30" height="30"></a>     
        <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="Telegram-Logo.png" width="28" height="28"></a> 
        </div>
    </div>
  </nav>

    <div class="container h-100 d-flex justify-content-center mt-5 pt-5">
       <div class="col-lg-4 pt-5">
        <?php 
            if ($fail) {
                echo '<div class="alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Wrong credentials!</strong>
              </div>';
            }
        ?>
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                Login
                </div>
                <form method="POST" class="p-3">
                    <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <center>
                        <button type="submit" class="btn btn-light border w-100 mt-2 mb-2">Login</button>
                        <?php 
                            echo '<a href="' . process_link("register.php", false) . '" class="text-right">No Account?</a>'; 
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
