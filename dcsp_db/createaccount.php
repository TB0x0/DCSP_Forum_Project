<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Create an account</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <style>
            input {
                margin-bottom: 0.5em;
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
		?>
    </head>
    <body style="background-color: #bfc9ca">
        <?php
            $sal1 = "zx&h^"; 
            $sal2 = "qp%@&";
			$errorVal = "";
            $userVal = "";
            $passVal = "";
            $passagainVal = "";
            
        //Forward the user to the main page if they are logged in already.
        if(isset($_SESSION['currentUserType'])){
            if ($_SESSION['currentUserType'] == "admin"){
                header("Location: main.php");
            } else if($_SESSION['currentUserType'] == "user") {
                header("Location: main.php");
            }
        }
         

        
		if(isset($_POST['submit'])){
			if(isset($_POST['username'], $_POST['password'], $_POST['passwordagain'])){
                //Check if the username is within the requirements.
                if(preg_match('/^[a-zA-Z]*$/',$_POST['username']) && strlen($_POST['username']) > 5 && strlen($_POST['username']) <= 32){
                    //If the username is valid, check to see if it is already in use.
                    $username = $_POST['username'];
                    $query = "SELECT * FROM users WHERE username = '$username'";
                    $result = $conn->query($query);
                    $resultArr = $result->fetch_array();
                    //If the name is already being used, display the error. Else, continue checking the input.
                    if($resultArr['username'] == $username){
                        $errorVal = "That username is already taken.";
                        $userVal = $_POST['username'];
                        $passagainVal = $_POST['passwordagain'];
                        $passVal = $_POST['password'];
                    } else {
                        //Password requirements
                        $uppercase = preg_match('@[A-Z]@', $_POST['password']); //Must have one uppercase
                        $lowercase = preg_match('@[a-z]@', $_POST['password']); //Must have one lowercase
                        $number    = preg_match('@[0-9]@', $_POST['password']); //Must have one number
                        $specialChars = preg_match('@[^\W]@', $_POST['password']); //Must have one special character
                        //True if the password does NOT comply with the requirements.
                        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($_POST['password']) > 32 || strlen($_POST['password']) < 8) {
                            $errorVal = "Password must be between 8 and 32 characters and have at least one of each: uppercase, lowercase, number, special character.";
                            $userVal = $_POST['username'];
                            $passagainVal = $_POST['passwordagain'];
                            $passVal = $_POST['password'];
                        } else {
                            //If the password is within requirements, check the display name.
                            if($_POST['password'] == $_POST['passwordagain']){
                                //All entries good
                                $password = $_POST['password'];
                                $token = (hash('ripemd128', "$sal1$password$sal2"));
                                db_add_user($conn, $_POST['username'], "user", $token);
                                //Forward the user to log in.
                                echo "Account created, redirecting you to log in.";
                                header("refresh:3; url=login.php");
                            } else {
                                $errorVal = "Passwords do not match.";
                                $userVal = $_POST['username'];
                                $passagainVal = $_POST['passwordagain'];
                                $passVal = $_POST['password'];
                            }
                        }
                    }                  
                } else {
                    $errorVal = "Username must be 6-32 upper and lowercase letters only.";
                    $userVal = $_POST['username'];
                    $passagainVal = $_POST['passwordagain'];
                    $passVal = $_POST['password'];
                }
		    } else {
				$errorVal = "Please enter a username, display name, and password.";
			}
		}
        ?>
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
            </div>
            </nav>
        </div>
        <div class="text-center container" style="background-color: #abb2b9">
        <h1>Create an account on the <span style="font-style:italic; font-weight:bold; color: maroon">
                DCSP Forum</span>!</h1>
                
        <p style="color: red">
        <?=$errorVal?>
        </p>
        
        <form method="post" action="createaccount.php">
            <label>Username: </label>
            <input type="text" name="username" value="<?=$userVal?>"> <br>
            <label>Password: </label>
            <input type="password" name="password" value="<?=$passVal?>"> <br>
            <label>Re-type Password: </label>
            <input type="passwordagain" name="passwordagain" value="<?=$passagainVal?>"> <br>
            <input type="submit" name="submit" value="Create Account">
        </form>
        
        <p style="font-style:italic">
            <a href="login.php">Already have an account? Log in.</a>
        </p>
        </div>
	</body>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</html>