<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

    <title>DCSP Admin Page</title>

    <?php
        session_start();
        require_once('dbfuncs/dbfunctions.php');
        require_once('dbfuncs/dblogin.php');

        if(isset($_SESSION['currentUserType'])){
            if ($_SESSION['currentUserType'] == "admin"){
                $loggedin = true;
                $admin = true;
            } else if($_SESSION['currentUserType'] == "user") {
                $loggedin = true;
                $admin = false;
                header("Location: main.php");
            }
        } else {
            $loggedin = false;
            $admin = false;
            header("Location: main.php");
        }

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
    <div class="container-fullwidth">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="main.php">DCSP Forum</a>
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
                <a class="nav-link" href="#">Lorum Ipsum</a>
            </li>
            <?php
                if ($loggedin && $admin) {
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
        </nav>
    </div>
    <?php
            $userVal = "";
            $errorVal = "";
        	if(isset($_POST['ban'])){
                if(isset($_POST['username'])){
                    $username = $_POST['username'];
                    $query = "SELECT status FROM users WHERE username = '$username'";
                    $result = $conn->query($query);
                    $resultArr = $result->fetch_array();
                        if(!$resultArr){
                            $errorVal = "That username does not exist";
                            $userVal = $username;
                        }else{
                            if($resultArr['status'] == "user")
                                {
                                $query = "UPDATE users SET status = 'banned' WHERE username = '$username'";
                                $conn->query($query);
                                $errorVal = "";
                                $userVal = "";
                                }
                                else{
                                $errorVal = "That user is already banned";
                                $userVal = $username;
                                }
                        }
                }
            }
            if(isset($_POST['unban'])){
                if(isset($_POST['username'])){
                    $username = $_POST['username'];
                    $query = "SELECT status FROM users WHERE username = '$username'";
                    $result = $conn->query($query);
                    $resultArr = $result->fetch_array();
                        if(!$resultArr){
                            $errorVal = "That username does not exist";
                            $userVal = $username;
                        }else{

                            if($resultArr['status'] == "banned")
                                {
                                $query = "UPDATE users SET status ='user' WHERE username = '$username'";
                                $conn->query($query);
                                $errorVal = "";
                                $userVal = "";
                                }
                                else{
                                $errorVal = "That user is not currently banned";
                                $userVal = $username;
                                }
                        }
                }
            }
            if(isset($_POST['add'])){
                if(isset($_POST['username'])){
                    $username = $_POST['username'];
                    $query = "SELECT status FROM users WHERE username = '$username'";
                    $result = $conn->query($query);
                    $resultArr = $result->fetch_array();
                        if(!$resultArr){
                            $errorVal = "That username does not exist";
                            $userVal = $username;
                        }else{
                            if($resultArr['status'] == "user")
                                {
                                $query = "UPDATE users SET status = 'admin' WHERE username = '$username'";
                                $conn->query($query);
                                $errorVal = "";
                                $userVal = "";
                                }
                                else{
                                $errorVal = "That user is already an admin.";
                                $userVal = $username;
                                }
                        }
                }
            }
    ?>
    <form  method = "post" action = "admin_page.php" class = "text-center">
        <div class="text-center container" >
            <div class = "row">
                <div class = "col-md-12 text-center">
                    <h3> Enter the username of the user you would like to ban</h3>
                </div>
            </div>
            <div class = "row">
                <div class="form-group col-md-12 text-center">
                    
                        <input type="text" class="form-control" id="username" name= "username" value="<?=$userVal?>">
                    
                </div>
            </div>
            <div class = "row">
                <div class = "col-md-6">
                    <button type="submit" name = "ban" id = "ban" class="btn btn-primary btn-block ">Submit Username to Ban</button>
                </div>
                <div class = "col-md-6">
                    <button type="submit" name = "unban" id = "unban" class="btn btn-primary btn-block">Submit Username to Un-Ban</button>
                </div>
                <div class = "col-md-12">
                    <button type="submit" name = "add" id = "add" class="btn btn-primary btn-block mt-3">Promote User to Admin</button>
                </div>
            </div>
            <div class = "row">
                <div class = "col-md-12 text-center">
                    <p style="color: red" class = "text-center">
                        <?=$errorVal?>
                    </p>
                </div>
            </div>
        </div>
    </form>
  

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>