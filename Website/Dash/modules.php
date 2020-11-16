<?php 
include_once('utils.php');

start_session();

session_regenerate_id();

check_login();

$username = $_SESSION['username'];

$loader = loader_check();

$modules = fetch_modules($loader->key,$username,$connection);

$code = 0;

if (isset($_GET['pause'])) {
    $connection->query('UPDATE loader_modules SET paused=\'1\' WHERE uid=? AND loader_key=? AND owner=?', [$_GET['pause'], $loader->key, $username]);

  header("Location: " . process_link("modules.php",true));
}

if (isset($_GET['unpause'])) {
    $connection->query('UPDATE loader_modules SET paused=\'0\' WHERE uid=? AND loader_key=? AND owner=?', [$_GET['unpause'], $loader->key, $username]);

    header("Location: " . process_link("modules.php",true));
}

if (isset($_GET['delete'])){
    $to_delete = $_GET['delete'];

    $query = $connection->query('SELECT * FROM loader_modules WHERE uid=? AND loader_key=? AND owner=?', [$to_delete, $loader->key, $username]);

  $server_name = $query->fetch_assoc()['server_name'];

  $connection->query('DELETE FROM loader_modules WHERE uid=? AND loader_key=? AND owner=?', [$to_delete, $loader->key, $username]);

  $server_path = '/var/www/html/dash.firefra.me/modules/' . $server_name; //TODO: fix, hardcoded

  unlink($server_path);

  header("Location: " . process_link("modules.php", true));
}

function update_module($loader, $connection, $uid, $name, $process, $username, $modules){
    if(!contains('.exe', $process))
        $process .= '.exe';

    $connection->query('UPDATE loader_modules SET name=?, process=? WHERE uid=? AND loader_key=? AND owner=?', [$name, $process, $uid, $loader->key, $username]);
    
    $c_module = null;

    foreach($modules as $module){
        if($module['uid'] !== $uid)
            continue;

        $c_module = $module;
    }

    $file_path = dirname(__FILE__).'/modules/'.$c_module['server_name'];

    unlink($file_path);

    if(empty($_FILES['file']))
        return 1;

    $file_name = basename($_FILES['file']['name']);

    $file_extension = substr($file_name, strrpos($file_name,'.') + 1);

    if($file_extension !== 'dll' || $_FILES['file']['size'] > 8388608)
        return 2;

    if(move_uploaded_file($_FILES['file']['tmp_name'], $file_path)){
        file_put_contents($file_path,encrypt_file($file_path,$module['server_key']));
         header("Location: " . process_link("modules.php?success",true));
    }
    return 0;
}

if(isset($_POST['uid'], $_POST['name'], $_POST['process'])){
    $code = update_module($loader, $connection, $_POST['uid'], $_POST['name'], $_POST['process'], $username, $modules);
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
        <a class="nav-item nav-link active" href="modules.php">Modules <span class="sr-only">(current)</span></a>
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
        <?php
        if (isset($_GET['success'])) {
          echo '<div class="alert alert-dismissible alert-success">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Module successfully updated!</strong>
        </div>';
        }
        switch ($code) {
          case 1:
            echo '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error occured while uploading your file, please contact us!</strong>
          </div>';
          break;
          case 2:
            echo '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You can only upload .dll files; max 8MB!</strong>
          </div>';
          break;
        }

        ?>
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
                              <?php
                              if ($modules !== []) {
                                foreach($modules as $module) {
                                  if ($module['paused'] == 0) {
                                    $paused_text = "false";
                                    $pause = process_link("modules.php?pause=" . $module['uid'], true);
                                    $pause = "window.location.href='" . $pause . "'";
                                    $pause = '<button onclick="' . $pause . '" class="btn btn-warning">Pause</button>  ';
                                  }
                                  else {
                                    $paused_text = "true";
                                    $pause = process_link("modules.php?unpause=" . $module['uid'], true);
                                    $pause = "window.location.href='" . $pause . "'";
                                    $pause = '<button onclick="' . $pause . '" class="btn btn-warning">Unpause</button>  ';
                                  }
                                  
                                  $update = process_link("modules.php?update=" . $module['uid'], true);
                                  $update = "window.location.href='" . $update . "'";
                                  $update = '<button onclick="' . $update . '" class="btn btn-success">Update</button>  ';

                                  $delete = process_link("modules.php?delete=" . $module['uid'], true);
                                  $delete = "window.location.href='" . $delete . "'";
                                  $delete = '<button onclick="' . $delete . '" class="btn btn-danger">Delete</button>  ';

                                  echo '<tr>
                                  <th scope="row">' . $module['name'] . '</th>
                                  <td>' . $module['process'] . ' </td>
                                  <td>' . $module['groups'] . '</td>
                                  <td>' . $paused_text . '</td>
                                  <td>' . $update . $pause . $delete .  '</td>
                                </tr>';
                                }
                              }
                              ?>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    if (isset($_GET['update'])) {
      foreach($modules as $module) {
        if ($module['uid'] !== $_GET['update'])
          continue;
          echo '<div class="container h-100 d-flex justify-content-center pt-5 mb-5">
          <div class="col-lg-10">
          <div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Always pause the module before updating it!</strong>
          </div>
              <div class="card rounded-0 text-white bg-dark">
                  <div class="card-header">
                  Update Module - ' . $module['name'] . ' 
                  </div>
                  <div class="row p-3">
                      <div class="col-lg mr-2">
                        <form method="POST" enctype="multipart/form-data" class="p-3">
                            <div class="form-group">
                            <label>Module Name</label>
                            <input type="text" class="form-control" id="name"  name="name" aria-describedby="emailHelp" value="' . $module['name'] .  '" required>
                            </div>
                            <div class="form-group">
                            <label>Injection process</label>
                            <input type="text" class="form-control" id="process" name="process" value="' . $module['process'] . '" required>
                            </div>
                            <div class="form-group">
                            <label>Allowed Groups</label>
                            <input type="text" class="form-control" id="groups" name="groups" value="' . $module['groups'] . '" required>
                            </div>
                            <div class="form-group">
                            <label>Module File</label>
                            <div class="custom-file mt-3">
                            <input type="hidden" name="MAX_FILE_SIZE" value="8388608"/>
                            <input type="hidden" name="uid" id="uid" value="' . $module['uid'] . '"/>
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
      </div>';
      }
    }
    ?>
    <footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
      <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
    </footer>
</body>
</html>
