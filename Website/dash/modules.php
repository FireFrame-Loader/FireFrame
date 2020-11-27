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

if(isset($_POST['pause']) || isset($_POST['unpause'])){
    module\pause($connection, $loader, $_POST['pause'] ?? $_POST['unpause'], isset($_POST['pause']));
}

if(isset($_POST['delete'])){
    module\delete($connection, $loader, $_POST['delete']);
}

if(isset($_POST['uid'], $_POST['name'], $_POST['process'])){
    module\update($connection, $loader, $_POST['uid'], [
        'name' => $_POST['name'],
        'process' => $_POST['process'],
        'file' => $_FILES['file']
    ]);
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
            <a class="nav-item nav-link" href="users.php">Users</a>
            <a class="nav-item nav-link" href="licenses.php">Licenses</a>
            <a class="nav-item nav-link active" href="modules.php">Modules <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="">|</a>
            <a class="nav-item nav-link" href="<?php if (is_onion()) echo 'https://firefra.me'; else echo 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/'; ?>"><?php if (is_onion()) echo 'Clearnet'; else echo 'Tor'; ?></a>
            <a class="nav-item nav-link" href="<?php echo process_link("tos.php",false); ?>">ToS & PP</a>
            <a class="nav-item nav-link" href="https://discord.gg/2bAH7AsxFx"><img src="../Discord-Logo-White.png" width="30" height="30"></a>
            <a class="nav-item nav-link" href="https://t.me/fire_frame"><img src="../Telegram-Logo.png" width="28" height="28"></a>
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
                Modules
            </div>
            <div class="row p-3">
                <div class="col-lg mr-2">
                    <table class="table table-dark w-100">
                        <thead>
                        <tr>
                            <th scope="col">Module Name</th>
                            <th scope="col">Process</th>
                            <th scope="col">Allowed Groups</th>
                            <th scope="col">Paused</th>
                            <th scope="col" style="text-align:center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <form method="post">
                        <?php
                        $modules = module\fetch_all($connection, $loader);

                        foreach($modules as $module){
                            $upd_link = process_link('modules.php?update='.$module['uid'], true);
                            ?>
                            <tr>
                                <th scope="row"><?= htmlentities($module['name']) ?></th>
                                <td><?= htmlentities($module['process']) ?></td>
                                <td><?= htmlentities($module['groups']) ?></td>
                                <td><?= $module['paused'] ? 'true' : 'false' ?></td>
                                <td>
                                    <button name="<?= $module['paused'] ? 'unpause' : 'pause' ?>" class="btn btn-warning" value="<?= $module['uid'] ?>">Pause/Unpause</button>
                                    <button name="update" onclick="<?= $upd_link ?>" class="btn btn-success" value="<?= $module['uid'] ?>">Update</button>
                                    <button name="delete" class="btn btn-danger" value="<?= $module['uid'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                        </form>
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_GET['update'])) {
    $module_data = module\fetch($connection, $loader, $_POST['update']); ?>
    <div class="container h-100 d-flex justify-content-center pt-5 mb-5">
        <div class="col-lg-10">
            <div class="alert alert-dismissible alert-warning">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Always pause the module before updating it!</strong>
            </div>
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                    Update Module - <?= htmlentities($module['name']) ?>
                </div>
                <div class="row p-3">
                    <div class="col-lg mr-2">
                        <form method="POST" enctype="multipart/form-data" class="p-3">
                            <div class="form-group">
                                <label>Module Name</label>
                                <input type="text" class="form-control" id="name"  name="name" aria-describedby="emailHelp" value="<?= htmlentities($module['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Injection process</label>
                                <input type="text" class="form-control" id="process" name="process" value="<?= htmlentities($module['process']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Allowed Groups</label>
                                <input type="text" class="form-control" id="groups" name="groups" value="<?= htmlentities($module['groups']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Module File</label>
                                <div class="custom-file mt-3">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="8388608"/>
                                    <input type="hidden" name="uid" id="uid" value="<?= htmlentities($module['uid']) ?>"/>
                                    <input type="file" class="custom-file-input" id="file" name="file" accept=".dll" required>
                                    <label class="custom-file-label" for="file">Choose file...</label>
                                </div>
                            </div>
                            <small class="text-secondary">Maximum module size: 8MB!<br>
                                It is recommended to pack your module with software like VMProtect or Themida as we support these!<br>
                                You can assign multiple groups to a module by separating the group names with a comma(,)<br>
                                Your module will be stored encrypted!</small>
                            <center>
                                <button type="submit" class="btn btn-light border w-100 mt-2 mb-2">Upload</button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
    <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
</footer>
</body>
</html>
