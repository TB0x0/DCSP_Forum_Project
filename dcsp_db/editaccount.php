<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <title>Stack Underflow Edit Account</title>
    <?php
        session_start();
        require_once('dbfuncs/dbfunctions.php');
        require_once('dbfuncs/dblogin.php');

        //Login state Vars
        if(isset($_SESSION['currentUserType'])){
            if ($_SESSION['currentUserType'] == "admin"){
                $loggedin = true;
                $admin = true;
            } else if($_SESSION['currentUserType'] == "user") {
                $loggedin = true;
                $admin = false;
            }
        } else {
            $loggedin = false;
            $admin = false;
            header("Location: login.php");
        }
        unset($_SESSION['postID']);
        $conn = new mysqli($hn, $un, $pw, $db);
			if ($conn->connect_error)
                die($conn->connect_error);
                
    ?>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto&display=swap');

        .border-3{
            border-width: 3px !important;
        }
        .dcsp-text-light{
            color: #dddddd !important;
        }
    </style>

  </head>
  <body style="background-color: #bfc9ca">
    <div class="container-fullwidth sticky-top">
        <!--NavBar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="main.php">
            <img src="stackunderflow.png" width="30" height="30" alt="">Stack Underflow
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="main.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="createpost.php">New Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inbox_page.php">Inbox</a>
            </li>
            <?php
                if($loggedin && !$admin){
                    echo "<li class=\"nav-item dropdown\">
                    <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Account
                    </a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                    <a class=\"dropdown-item\" href=\"#\">Edit Account</a>
                    <a class=\"dropdown-item\" href=\"logout.php\">Log out</a>
                    </div>
                    </li>";
                } else if ($loggedin && $admin) {
                    echo "<li class=\"nav-item dropdown\">
                    <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Account
                    </a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                    <a class=\"dropdown-item\" href=\"#\">Edit Account</a>
                    <a class=\"dropdown-item\" href=\"admin_page.php\">Admin Page</a>
                    <a class=\"dropdown-item\" href=\"logout.php\">Log out</a>
                    </div>
                    </li>";
                }else {
                    echo "<li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"login.php\">Log In/Create Account</a>
                    </li>";
                }
            ?>
            </ul>
        </div>
        <form class="navbar-form navbar-right" method="get" action="search.php">
            <div class="input-group">
                <input type="text" class="form-control" name="search_var" placeholder="Search Posts">
                <div class="input-group-btn">
                <button class="btn btn-default btn-light" type="submit">
                    <i class="fa fa-search"></i>
                </button>
                </div>
            </div>
        </form>
        </nav>
    </div>
    
    <!--Main-->
    <div class="container pt-5" style="background-color: #abb2b9">
        <div class="row">
            <div class="col-md-9">
                <div class="container-fullwidth border border-dark border-3 p-3">

                    <?php
                        //Vars
                        $oldPassVal ="";
                        $successVal ="";
                        $errorVal ="";
                        $passVal ="";
                        $passagainVal ="";
                        $sal1 = "zx&h^"; 
                        $sal2 = "qp%@&";

                        if(isset($_POST['submit'])){
                            if(isset($_POST['currentpassword'], $_POST['password'], $_POST['passwordagain'])){
                                //Get the data entered. Hash old and new passwords
                                $password = $_POST['currentpassword'];
                                $newPassword = $_POST['password'];
                                $username = $_SESSION['currentUser'];
                                $hashedPass = hash('ripemd128', "$sal1$password$sal2");
                                $hashedNewPass = hash('ripemd128', "$sal1$newPassword$sal2");
                                $query = "SELECT * FROM users WHERE username = '$username'";
                                $result = $conn->query($query);
                                $resultArr = $result->fetch_array();
                                if($hashedPass == $resultArr['password']){
                                    //Password requirements
                                    $uppercase = preg_match('@[A-Z]@', $_POST['password']); //Must have one uppercase
                                    $lowercase = preg_match('@[a-z]@', $_POST['password']); //Must have one lowercase
                                    $number    = preg_match('@[0-9]@', $_POST['password']); //Must have one number
                                    $specialChars = preg_match('@[^\W]@', $_POST['password']); //Must have one special character
                                    //True if the password does NOT comply with the requirements.
                                    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($_POST['password']) > 32 || strlen($_POST['password']) < 8) {
                                        $errorVal = "New password must be between 8 and 32 characters and have at least one of each: uppercase, lowercase, number, special character.";
                                        $successVal = "";
                                        $oldPassVal = $_POST['currentpassword'];
                                        $passagainVal = $_POST['passwordagain'];
                                        $passVal = $_POST['password'];
                                    } else {
                                        //Check if new password matched old password
                                        if($hashedNewPass == $resultArr['password']){
                                            $errorVal = "New password must not match old password.";
                                            $successVal = "";
                                            $oldPassVal = $_POST['currentpassword'];
                                            $passagainVal = $_POST['passwordagain'];
                                            $passVal = $_POST['password'];
                                        } else {
                                            //If the password is within requirements, check the retyped password.
                                            if($_POST['password'] == $_POST['passwordagain']){
                                                //All entries good
                                                $password = $_POST['password'];
                                                $token = (hash('ripemd128', "$sal1$password$sal2"));
                                                db_edit_password($conn, $username, $token);
                                                //Forward the user to log in.
                                                $successVal = "Password changed, redirecting you to log out.";
                                                header("refresh:3; url=logout.php");
                                            } else {
                                                $errorVal = "Passwords do not match.";
                                                $successVal = "";
                                                $oldPassVal = $_POST['currentpassword'];
                                                $passagainVal = $_POST['passwordagain'];
                                                $passVal = $_POST['password'];
                                            }
                                        }
                                    }                  
                                } else {
                                    $errorVal = "The current password you entered is incorrect.";
                                    $successVal = "";
                                    $oldPassVal = $_POST['currentpassword'];
                                    $passagainVal = $_POST['passwordagain'];
                                    $passVal = $_POST['password'];
                                }
                            } else {
                                $errorVal = "Please enter your current and new password.";
                                $successVal = "";
                            }
                        }
                    ?>                 
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <h3 class="text-center">Update Password</h3>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                    </div>
                    
                    <?php
                        //Handle error and success messages
                        if($errorVal != ""){
                            echo "<div class=\"alert alert-danger\" role=\"alert\">$errorVal</div>";
                        } else if($successVal != ""){
                            echo "<div class=\"alert alert-success\" role=\"alert\">$successVal</div>";
                        }
                    ?>

                    <!--Form-->
                    <form method="post" action="editaccount.php">
                        <div class="form-group form-row">
                            <label>Current Password: </label>
                            <input type="password" name="currentpassword" class="form-control" value="<?=$oldPassVal?>"> 
                        </div>
                        <br>
                        <div class="form-group form-row">
                            <label>New Password: </label>
                            <input type="password" name="password" class="form-control" value="<?=$passVal?>">
                        </div>
                        <br>
                        <div class="form-group form-row">
                            <label>Re-type New Password: </label>
                            <input type="password" name="passwordagain" class="form-control" value="<?=$passagainVal?>">
                        </div>
                        <br>
                        <div class="form-group form-row">
                            <input type="submit" name="submit" class="btn btn-primary" value="Update Password">
                        </div>
                    </form> 
                </div>
            </div>

            <div class="col-md-3">
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <a href="createpost.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">New Post</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>