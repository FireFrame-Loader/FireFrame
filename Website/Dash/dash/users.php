<?php
require '../functions/includes.php';
require '../functions/session.php';

session_regenerate_id();

if(!session_valid() or !isset($_SESSION['loader'])) {
    header('Location: ' . process_link('index.php', true));
    die('invalid login or loader not set');
}

$loader = $_SESSION['loader'];

$username = $_SESSION['username'];

//TODO : functions with all of these ( if we're doing an admin api )
if (isset($_POST['delete'])) {
    $connection->query('DELETE FROM loader_users WHERE username=? AND loader_key=? AND owner=?', [$_POST['delete'], $loader['key'], $username]);
}

if (isset($_POST['reset_pass'])) {
    $new_pass = rnd_string_secure(12);

    $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);

    $connection->query('UPDATE loader_users SET password=? WHERE username=? AND loader_key=? AND owner=?', [$new_pass_hash, $_POST['reset_pass'], $loader['key'], $username]);

    die($new_pass);
}

if (isset($_POST['add_month'])) {
    $user = $_POST['add_month'];

    $query = $connection->query('SELECT expires FROM loader_users WHERE username=? AND loader_key=? AND owner=?', [$user, $loader['key'], $username]);

    $expiry = $query->fetch_assoc()['expires'];

    $exp_calc = function($expiry){
        if($expiry === -1)
            return $expiry;

        if($expiry > time())
            return $expiry + 2592000;

        return time() + 2592000;
    };

    $new_expire = $exp_calc($expiry);

    $connection->query('UPDATE loader_users SET expires=? WHERE username=? AND loader_key=? AND owner=?', [$new_expire, $user, $loader['key'], $username]);
}

if (isset($_POST['reset'])) {
    $connection->query('UPDATE loader_users SET hwid=NULL WHERE username=? AND loader_key=? AND owner=?', [$_POST['reset'], $loader['key'], $username]);
}

if (isset($_POST['make_life'])) {
    $connection->query('UPDATE loader_users SET expires=\'-1\' WHERE username=? AND loader_key=? AND owner=?', [$_POST['make_life'], $loader['key'], $username]);
}

$code_switcher = static function($code){
  switch($code){
      case 1:
          return 'passwords do not match';
      case 2:
          return 'user already exists';
      case 3:
          return 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
      case 0:
          return 'success';
      default:
          return '?';
  }
};

if(isset($_POST['username'], $_POST['password'], $_POST['confirmpassword'])) {
    if ($_POST['password'] !== $_POST['confirmpassword']) {
        die("pass mismatch");
    }

    $code = auth\user\add($connection, $loader, $_POST['username'], $_POST['password'], $_POST['usergroup']);
    die($code_switcher($code));
}


if(!empty($_POST))
    header("Refresh:0");

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
            <a class="nav-itedm nav-link active" href="users.php">Users <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="licenses.php">Licenses</a>
            <a class="nav-item nav-link" href="modules.php">Modules</a>
            <a class="nav-item nav-link" href="">|</a>
            <a class="nav-item nav-link" href="<?php if (is_onion()) echo 'https://firefra.me'; else echo 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php if (is_onion()) echo 'Clearnet'; else echo 'Tor'; ?></a>
            <a class="nav-item nav-link" href="<?php echo process_link("tos.php",false); ?>">ToS & PP</a>
            <a class="nav-item nav-link" href="https://discord.gg/xPtevhPHQp"><img src="Discord-Logo-White.png" width="30" height="30"></a>
            <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="Telegram-Logo.png" width="28" height="28"></a>
        </div>
    </div>
    <form class="form-inline p-0 m-0" action="account.php">
      <span class="navbar-text text-light text-right mr-3">
          User : <?php echo $username;?>
      </span>
        <button class="btn btn-outline-light my-2 my-sm-0 mr-3" type="submit">Account</button>
        <button class="btn btn-outline-light my-2 my-sm-0" name="logout" id="logout">Logout</button>

        <button class="navbar-toggler ml-3 " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </form>
</nav>

<div class="container h-100 d-flex justify-content-center pt-5 mb-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Users
            </div>
            <div class="row p-3">
                <div class="col-lg mr-2">
                    <table class="table table-dark w-100">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Group</th>
                            <th scope="col">Expires</th>
                            <th scope="col" style="text-align:center">Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $users = auth\user\fetch_all($connection, $loader['key']);
                        if($users === 0){
                            echo 'none';
                        } else {
                            foreach($users as $user){
                                $usern = htmlentities($user['username']);
                                ?>
                                <tr>
                                    <form method="post">
                                    <th scope="row"><?= $usern ?></th>
                                    <td><?= htmlentities($user['usergroup']) ?></td>
                                    <td><?php echo ($user['expires'] != '-1') ? date("F j, Y, g:i a", $user['expires']) : 'Lifetime'; ?></td>
                                    <td>
                                        <button name="delete" class="btn btn-danger" value="<?= $usern ?>">Delete</button>
                                        <button name="reset" class="btn btn-primary" value="<?= $usern ?>">Reset HWID</button>
                                        <button name="add_month" class="btn btn-success" value="<?= $usern ?>">Add 1 Month</button>
                                        <button name="make_life" class="btn btn-warning" value="<?= $usern ?>">Make Lifetime</button>
                                        <button name="reset_pass" class="btn btn-info" value="<?= $usern ?>">Reset Password</button>
                                    </td>
                                    </form>
                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container h-100 d-flex justify-content-center pt-5 mb-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Add User
            </div>
            <div class="row p-3">
                <div class="col-lg mr-2">
                    <form method="POST" class="p-3">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" id="username"  name="username" aria-describedby="emailHelp" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm password" required>
                        </div>
                        <div class="form-group">
                            <label>Usergroup</label>
                            <input type="text" class="form-control" id="usergroup" name="usergroup" placeholder="Usergroup (Default,VIP)">
                        </div>
                        <small class="text-secondary">If you leave the Usergroup field empty, Default group will be automatically applied.<br>
                            You can also assign multiple groups to a user by separating the group names with a comma (,).</small>
                        <center>
                            <button type="submit" class="btn btn-light border w-100 mt-2 mb-2">Add User</button>
                        </center>
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
