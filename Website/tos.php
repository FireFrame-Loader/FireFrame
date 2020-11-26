<?php 
function is_onion() {
  $http_host = $_SERVER['HTTP_HOST'];
  if ($http_host == "etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion")
      return true;
  return false;
}

function process_link($add,$dash) {
  if(is_onion())
      return 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/' . ($dash ? 'dash/' : '') . $add;

  return 'https://' . ($dash ? 'dash.' : '') . 'firefra.me/' . $add;
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

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
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
    <form class="form-inline p-0 m-0" action="<?php echo process_link("login.php",true);?>">
      <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Sign In</button>

      <button class="navbar-toggler ml-3 " type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </form>
  </nav>

    <div class="container h-100 d-flex justify-content-center mt-5 mb-5" id="features">
        <div class="col-lg-10">
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                Terms of Service
                </div>
                <div class="row p-3">
                    <div class="col-lg mr-2">
                        <article><b>Our terms of service, covers all services provided to you from firefra.me and all its sub-domains!</b><br><br>

<b>Exclusion Clause
If you are an employee or volunteer of, previously employed by or volunteered at, associated with, represent, or are acquaintances or family with a member of Valve Corporation, Bohemia Interactive a.s., BattlEye Innovations, CEVO LLC, EasyAntiCheat Ltd., Turtle Entertainment GmbH, E-Sports Entertainment LLC, or any legal or other firm representing the aforementioned organizations, you are explicitly not permitted to access this Site or any products or services offered by Hexui.</b>
<br><br>
<b>1. Acceptance of Terms of Use</b><br>

By accessing this web site, you are agreeing to be bound by these Terms and Conditions of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this web site are protected by applicable copyright and trade mark law.
<br><br><b>2. Use License</b><br>

a. Permission is granted to temporarily download one copy of the materials (information or software) on FireFrame's web site for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:

    <br><br><b>1, modify or copy the materials;</b>
    <br><b>2, use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</b>
    <br><b>3, attempt to decompile or reverse engineer any software contained on FireFrame's web site;</b>
    <br><b>4, remove any copyright or other proprietary notations from the materials; or</b>
    <br><b>5, transfer the materials to another person or "mirror" the materials on any other server.</b><br><br>

b. This license shall automatically terminate if you violate any of these restrictions and may be terminated by FireFrame at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.

<br><br><b>3. Disclaimer</b><br>

The materials on FireFrame's web site are provided "as is". FireFrame makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, FireFrame does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet web site or otherwise relating to such materials or on any sites linked to this site.
<br><br><b>4. Limitations</b><br>

In no event shall FireFrame or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on FireFrame's Internet site, even if FireFrame or a FireFrame authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
<br><br><b>5. Revisions and Errata</b><br>

The materials appearing on FireFrame's web site could include technical, typographical, or photographic errors. FireFrame does not warrant that any of the materials on its web site are accurate, complete, or current. FireFrame may make changes to the materials contained on its web site at any time without notice. FireFrame does not, however, make any commitment to update the materials.

<br><br><b>7. Site Terms of Use Modifications</b><br>

FireFrame may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use.
<br><br><b>8. Governing Law</b><br>

Any claim relating to FireFrame's web site shall be governed by the laws of the <b>Seychelles</b> without regard to its conflict of law provisions.
<br><br><b>9. Subscription</b><br>

    The subscription is valid for the time purchased. When expired, the user is required to renew it manually.
    Users caught abusing third party payment services to obtain free services will be immediately and permanently banned.
    Users caught selling subscriptions, cracking or alterating the behavior of the software will be immediately and permanently banned.
    Subscriptions may be terminated by FireFrame at any time without warning and without liability, whether financial or not.

<br><br><b>10. Online Gaming</b><br>

FireFrame accepts no responsibility to your online gaming accounts as a result of using this website or any software, or data contained within. You, the user, are solely responsible for your online gaming activities and any loss to any of your online gaming accounts, whether financial or not, is your responsibility.
<br><br><b>11. Payment Policy</b><br>

Fraudulent charges with an account that sends money without the consent of the account holder or/and any kind of frauds will result in your account being instantly disabled, permanently terminated and/or legal action will be taken. We do not allow the user to share or resell his account, it will result in your account being instantly disabled, permanently terminated and/or legal action will be taken. </div>
                </article></div>
            </div>
        </div>
    </div>    <div class="container h-100 d-flex justify-content-center mt-5 mb-5" id="features">
        <div class="col-lg-10">
            <div class="card rounded-0 text-white bg-dark">
                <div class="card-header">
                Privacy Policy
                </div>
                <div class="row p-3">
                    <div class="col-lg mr-2">
                        <article>
                        <b>What information do we store about loader owner accounts?</b><br>
                        a, username<br>
                        b, password (hashed using BCRYPT)<br>
                        c, subscription expire timestamp<br>
                        <br>
                        <b>What information do we store about loader user accounts?</b><br>
                        a, username<br>
                        b, password (hashed using BCRYPT)<br>
                        c, hardware unique identifier hash<br>
                        d, usergroup<br>
                        e, subscription expire timestamp<br>
                        d, to which loader they belong<br>
                        <br>
                        <b>What information do we store about uploaded loader modules?</b><br>
                        a, location to the encrypted module<br>
                        b, to which loader they belong<br>
                        <br>
                        <b>We don't collect any other information nor IP addresses!</b>
                        </article>
                   </div>
            </div>
        </div>
    </div>

    <footer class="page-footer font-small dark text-light bg-dark fixed-bottom">
      <div class="footer-copyright text-center py-3">© <?php echo date("Y");?> FireFrame</div>
    </footer>

</body>
</html>
