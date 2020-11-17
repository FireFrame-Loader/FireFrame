<?php
require '../functions/includes.php';
require '../functions/session.php';

session_regenerate_id();

session_check();

$username = $_SESSION['username'];

$loader = loader\fetch_loader($connection, $username);

if ($loader !== 0)
    $_SESSION['loader'] = $loader;

$code_switcher = static function($code){
    switch($code){
        case 0:
            return 'success';

        case 1:
            return 'loader creation failed';

        case 2:
            return 'loader name is invalid or user already has a loader';

        case 3:
            return 'error occurred';

        case 4:
            return '8mb max';

        default:
            return '?';
    }
};

if(isset($_POST['create_loader'])) {
    $out = loader\create_loader($connection, $_POST['loader_name'], $username);

    die($code_switcher($out));
}


if(isset($_POST['process'], $_POST['name'], $_POST['groups'])){
    $out = module\update_module($connection, $_FILES['file'], $loader, $_POST['name'], $_POST['process'], $_POST['groups']);

    die($code_switcher($out));
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
            <a class="nav-item nav-link active" href="index.php">Loader <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="users.php">Users</a>
            <a class="nav-item nav-link" href="licenses.php">Licenses</a>
            <a class="nav-item nav-link" href="modules.php">Modules</a>
            <a class="nav-item nav-link" href="">|</a>
            <a class="nav-item nav-link" href="<?php echo (is_onion()) ? 'https://firefra.me' : 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php echo (is_onion()) ? 'Clearnet' : 'Tor'; ?></a>
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

<div class="container h-100 d-flex justify-content-center pt-5">
    <div class="col-lg-10">
        <div class="card rounded-0 text-white bg-dark">
            <div class="card-header">
                Manage loader - <?php if(isset($loader['name'])) { echo $loader['name']; } ?>
            </div>
            <div class="row">
                <?php if ($loader === 0) { ?>
                    <div class="col-md mr-2">
                        <form method="POST" class="p-3">
                            <div class="form-group">
                                <label for="loader_name">Loader Name</label>
                                <input type="text" class="form-control" id="loader_name" name="loader_name" aria-describedby="poo" placeholder="Loader Name" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="create_loader">Create</button>
                        </form>
                    </div>
                <?php } else { ?>
                    <div class="col-md mr-2">
                        <form class="p-3">
                            <div class="form-group">
                                <label for="loader_key">Loader key</label>
                                <input type="text" id="loader_key" name="loader_key" class="form-control" placeholder="<?= $loader['name'] ?>" readonly="readonly">
                            </div>
                        </form>
                        <form method="POST" class="p-3">
                            <div class="form-group">
                                <label for="loader_name">Loader Name</label>
                                <input type="text" class="form-control" id="loader_name" name="loader_name" aria-describedby="poo" placeholder="<?= $loader['name'] ?>" required>
                            </div>
                            <!-- <button type="submit" class="btn btn-primary">Rename</button> -->
                        </form>
                    </div>
                    <div class="col-md mr-2">
                        <form method="POST" enctype="multipart/form-data" class="p-3">
                            <div class="form-group">
                                <label>Add Loader Modules</label>
                                <input type="text" class="form-control" id="name" name="name" aria-describedby="poo" placeholder="Module Name" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="process" name="process" aria-describedby="poo" placeholder="Injection Process (csgo.exe)" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="groups" name="groups" aria-describedby="poo" placeholder="Allowed Groups (Default,VIP)" required>
                            </div>
                            <div class="form-group">
                                <div class="custom-file mt-3">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="8388608"/>
                                    <input type="file" class="custom-file-input" accept=".dll" id="file" name="file" required>
                                    <label class="custom-file-label" for="file">Choose file...</label>
                                </div>
                            </div>
                            <small class="text-secondary">Maximum module size: 8MB!<br>
                                It is recommended to pack your module with software like VMProtect or Themida as we support these!<br>
                                You can assign multiple groups to a module by separating the group names with a comma(,)<br>
                                Your module will be stored encrypted!</small>
                            <br>
                            <button type="submit" class="btn btn-primary mt-2">Upload</button>
                        </form>
                    </div> <?php } ?>               </div>
        </div>
    </div>
</div>
<footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
    <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
</footer>
</body>
</html>
