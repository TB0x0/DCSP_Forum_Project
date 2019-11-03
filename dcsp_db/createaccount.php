<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Create an account</title>
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
            $displayVal = "";
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
			if(isset($_POST['username'], $_POST['displayname'], $_POST['password'])){
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
                        $displayVal = $_POST['displayname'];
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
                            $displayVal = $_POST['displayname'];
                            $passVal = $_POST['password'];
                        } else {
                            //If the password is within requirements, check the display name.
                            if(preg_match('/^[a-zA-Z0-9_-]*$/',$_POST['displayname']) && strlen($_POST['displayname']) > 5 && strlen($_POST['displayname']) <= 32){
                                //All entries good
                                $password = $_POST['password'];
                                $token = (hash('ripemd128', "$sal1$password$sal2"));
                                db_add_user($conn, $_POST['username'], "user", $_POST['displayname'], $token);
                                //Forward the user to log in.
                                echo "Account created, redirecting you to log in.";
                                header("refresh:3; url=login.php");
                            } else {
                                $errorVal = "Display name may only be 6-32 letters, numbers, underscores, and hyphens.";
                                $userVal = $_POST['username'];
                                $displayVal = $_POST['displayname'];
                                $passVal = $_POST['password'];
                            }
                        }
                    }                  
                } else {
                    $errorVal = "Username must be 6-32 upper and lowercase letters only.";
                    $userVal = $_POST['username'];
                    $displayVal = $_POST['displayname'];
                    $passVal = $_POST['password'];
                }
		    } else {
				$errorVal = "Please enter a username, display name, and password.";
			}
		}
        ?>
        <h1>Create an account on the <span style="font-style:italic; font-weight:bold; color: maroon">
                DCSP Forum</span>!</h1>
                
        <p style="color: red">
        <?=$errorVal?>
        </p>
        
        <form method="post" action="createaccount.php">
            <label>Username: </label>
            <input type="text" name="username" value="<?=$userVal?>"> <br>
            <label>Display Name: </label>
            <input type="text" name="displayname" value="<?=$displayVal?>"> <br>
            <label>Password: </label>
            <input type="password" name="password" value="<?=$passVal?>"> <br>
            <input type="submit" name="submit" value="Create Account">
        </form>
        
        <p style="font-style:italic">
            <a href="login.php">Already have an account? Log in.</a>
        </p>
	</body>
</html>