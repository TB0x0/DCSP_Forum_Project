<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Log in to Website</title>
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
		?>
    </head>
    <body>
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
				//Get the user who's name matches the username given
				$query = "SELECT * FROM users WHERE username = '$username'";
                //Put result into $userInfo
				$result = $conn->query($query);
				$userInfo = $result->fetch_array();
                $password = $_POST['password'];
                //If the username is in the database, the first part will be 1. 0 otherwise.
                //For the password, hash the password provided and compare it to the hashed password in the database.
				if($userInfo['username'] == $username && $userInfo['password'] == (hash('ripemd128', "$sal1$password$sal2"))){
					$displayName = $userInfo['display_name'];
                    $status = $userInfo['status'];
                    //Creadt session variables to keep track of the user's display name and status
					$_SESSION['currentUser'] = $displayName;
                    $_SESSION['currentUserType'] = $status;
                    //Forward the user to the main page.
                    header("Location: main.php");
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
        <h1>Welcome to <span style="font-style:italic; font-weight:bold; color: maroon">
                DCSP Forum</span>!</h1>
                
        <p style="color: red">
        <?=$errorVal?>
        </p>
        
        <form method="post" action="login.php">
            <label>Username: </label>
            <input type="text" name="username" value="<?=$userVal?>"> <br>
            <label>Password: </label>
            <input type="password" name="password" value="<?=$passVal?>"> <br>
            <input type="submit" name="submit" value="Log in">
        </form>
        
        <p style="font-style:italic">
            <a href="createaccount.php">Create account</a>
        </p>
	</body>
</html>