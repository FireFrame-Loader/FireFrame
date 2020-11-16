<?php 
include_once('utils.php');

start_session();

session_regenerate_id();

check_login();

$username = $_SESSION['username'];

$expired = $_SESSION['expired'];

$loader = has_loader($username,$connection);

$code_switcher = function($code){
  switch ($code) {
          case 1:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Loader creation failed! Your account is expired!</strong>
          </div>';

          case 2:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Loader name must be a valid string!</strong>
          </div>';  

          case 3:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Errour occured while uploading your while, please contact us!</strong>
          </div>'; 

          case 4:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You can only upload .dll files; max 8MB!</strong>
          </div>'; 

          case 5:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Module successfully uploaded! <a href="' . process_link("modules.php",true) . '">You can view it here.</a></strong>
          </div>'; 

        default:
            return '';
        }
};

$code = 0;

if ($loader !== "") {
    $loader = json_decode($loader); 
    
    $_SESSION['loader'] = $loader;
}

function update_loader($connection, $loader, $loader_name, $username){
    if(strlen($loader_name) === 0)
        return 2;

    if($loader !== ""){
        $connection->query('UPDATE loaders SET `name`=? WHERE owner=?', [$loader_name, $username]);
        header("Refresh:0");
        return 0;
    }

    if($expired)
	    return 1;

    $loader_key = rnd_string_secure(3) . '-' . rnd_string_secure(3) . '-' . rnd_string_secure(3);

    $connection->query('INSERT INTO loaders (`name`, loader_key, owner) VALUES(?, ?, ?)', [$loader_name, $loader_key, $username]);
    header("Refresh:0");
    return 0;
}

if(isset($_POST['loader_name']))
    $code = update_loader($connection, $loader, $_POST['loader_name'], $username);

function upload_module($connection, $name, $process, $groups, $username, $loader) {
    $server_name = rnd_string_secure(32).'.dll';

    $server_key = rnd_string_secure(64);

    $uid = rnd_string_secure(32);

    if (!contains('.exe',$process))
        $process .= '.exe';

    $file_path = dirname(__FILE__).'/modules/'.$server_name;

    if(empty($_FILES['file']))
        return 3;

    $file_name = basename($_FILES['file']['name']);

    $file_extension = substr($file_name, strrpos($file_name,'.') + 1);

    if($file_extension !== 'dll' || $_FILES['file']['size'] > 8388608)
        return 4;

    if(!move_uploaded_file($_FILES['file']['tmp_name'], $file_path))
        return 3;

    file_put_contents($file_path, encrypt_file($file_path, $server_key));

    $connection->query('INSERT INTO loader_modules(`name`, process, `groups`, `server_name`, server_key, uid, paused, loader_key, owner) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
        $name, $process, $groups, $server_name, $server_key, $uid, 0, (string) $loader->key, $username
    ));

    return 5;
}

if(isset($_POST['process'], $_POST['name'], $_POST['groups']))
    $code = upload_module($connection, $_POST['name'], $_POST['process'], $_POST['groups'], $username, $loader);

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
        <?php echo $code_switcher($code); ?>
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                Manage loader - <?php if (isset($loader->name)) echo $loader->name; ?>
                </div>
                <div class="row">
                <?php if ($loader === "") { ?>
                  <div class="col-md mr-2">
                  <form method="POST" class="p-3">
                      <div class="form-group">
                      <label for="loader_name">Loader Name</label>
                      <input type="text" class="form-control" id="loader_name" name="loader_name" aria-describedby="poo" placeholder="Loader Name" required>
                      </div>
                      <button type="submit" class="btn btn-primary">Create</button>
                  </form>
              </div>
                <?php } else { ?>
                   <div class="col-md mr-2">
                   <form class="p-3">
                   <div class="form-group">
                       <label for="loader_key">Loader key</label>
                       <input type="text" id="loader_key" name="loader_key" class="form-control" placeholder="<?php echo $loader->key ?>" readonly="readonly">
                     </div>
               </form>
                   <form method="POST" class="p-3">
                       <div class="form-group">
                       <label for="loader_name">Loader Name</label>
                       <input type="text" class="form-control" id="loader_name" name="loader_name" aria-describedby="poo" placeholder="<?php echo $loader->name;?>" required>
                       </div>
                       <button type="submit" class="btn btn-primary">Rename</button>
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
