<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Log in to Stack Underflow</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <style>
            input {
                margin-bottom: 0.5em;
            }

            .dcsp-center{
                width: 25%;
                height: 60%;
                position: absolute;
                top:10%;
                bottom: 0;
                left: 0;
                right: 0;
                margin: auto;
            }   


        </style>
        <?php 
            require_once('dbfuncs/dbfunctions.php');
            require_once('dbfuncs/dblogin.php');
        ?>
        
        <?php   
            session_start();
            $conn = new mysqli($hn, $un, $pw, $db);
            if ($conn->connect_error)
                die($conn->connect_error);
            unset($_SESSION['postID']);

            function sanitizeInputs($var){
                $var = stripslashes($var);
                $var = strip_tags($var);
                $var = htmlentities($var);
                $var = trim($var);
                return $var;
            }
        ?>
    </head>
    <body style="background-color: #bfc9ca">
        <?php
            $sal1 = "zx&h^"; 
            $sal2 = "qp%@&";
            $errorVal = "";
            $userVal = "";
            $passVal = "";
            
            //Forward the user to the main page if they are logged in already.
        if(isset($_SESSION['currentUserType'])){
            if ($_SESSION['currentUserType'] == "admin"){
                header("Location: main.php");
            } else if($_SESSION['currentUserType'] == "user") {
                header("Location: main.php");
            }
        }

        
        if(isset($_POST['submit'])){
            if(isset($_POST['username'], $_POST['password'])){
                $username = $_POST['username'];
                //sanitizeInputs('username');
                //sanitizeInputs($_POST['password']);
                //Get the user who's name matches the username given
                $query = "SELECT * FROM users WHERE username = '$username'";
                //Put result into $userInfo
                $result = $conn->query($query);
                $userInfo = $result->fetch_array();
                $password = $_POST['password'];
                //If the username is in the database, the first part will be 1. 0 otherwise.
                //For the password, hash the password provided and compare it to the hashed password in the database.
                if($userInfo['username'] == $username && $userInfo['password'] == (hash('ripemd128', "$sal1$password$sal2"))){
                    if($userInfo['status'] == 'banned'){
                        $errorVal = "You are currently banned.";
                    }
                    else {
                        $displayName = $userInfo['username']; //****THIS WAS CHANGED TO USERNAME TO FIX ISSUE WITH CREATE POST****
                        $status = $userInfo['status'];
                        //Creadt session variables to keep track of the user's display name and status
                        $_SESSION['currentUser'] = $displayName;
                        $_SESSION['currentUserType'] = $status;
                        //Forward the user to the main page.
                        header("Location: main.php");
                    }
                } else {
                    $errorVal = "Your username or password is incorrect.";
                    //Retain the values the user entered for QOL.
                    $userVal = $_POST['username'];
                    $passVal = $_POST['password'];
                }
            } else {
                $errorVal = "Please enter a username and password.";
            }
        }
        ?>
        <div class="container-fullwidth sticky-top">
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
            </div>
            </nav>
        </div>
        <div class="text-center container" style="background-color: #abb2b9">
            <h1>Welcome to <span style="font-style:italic; font-weight:bold; color: maroon">
                    Stack Underflow</span>!</h1>
                    
            <p style="color: red">
            <?=$errorVal?>
            </p>
            
            <form class="form-signin" method="post" action="login.php">
                <label>Username: </label>
                <input type="text" name="username" value="<?=$userVal?>"> <br>
                <label>Password: </label>
                <input type="password" name="password" value="<?=$passVal?>"> <br>
                <input type="submit" name="submit" value="Log in">
            </form>
            
            <p style="font-style:italic">
                <a href="createaccount.php">Create Account</a>
            </p>
        </div>
    </body>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>

</html>