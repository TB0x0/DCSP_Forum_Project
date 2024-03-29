<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">


    <?php
        session_start();
        require_once('dbfuncs/dbfunctions.php');
        require_once('dbfuncs/dblogin.php');
        //Handle login states
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
            header("Location: main.php");
            $admin = false;
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
            <li class="nav-item active">
                <a class="nav-link" href="inbox_page.php">Inbox</a>
            </li>
            <?php
                if($loggedin && !$admin){
                    echo "<li class=\"nav-item dropdown\">
                    <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Account
                    </a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                    <a class=\"dropdown-item\" href=\"editaccount.php\">Edit Account</a>
                    <a class=\"dropdown-item\" href=\"logout.php\">Log out</a>
                    </div>
                    </li>";
                } else if ($loggedin && $admin) {
                    echo "<li class=\"nav-item dropdown\">
                    <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Account
                    </a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                    <a class=\"dropdown-item\" href=\"editaccount.php\">Edit Account</a>
                    <a class=\"dropdown-item\" href=\"admin_page.php\">Ban User</a>
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
                <input type="text" class="form-control" placeholder="Search Posts">
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
                        //Init Vars
                        $msgErr = "";
                        $msgErrBool = false;
                        $recipient = "";
                        $msgcontents = "";
                        if(isset($_GET['submit'])){
                            if(isset($_GET['recipient'], $_GET['msgcontents'])){
                                //Message contents must be any string 6-250 characters, and not only whitespace
                                if(preg_match('/^(.*)$/ms',$_GET['msgcontents']) && !(ctype_space($_GET['msgcontents'])) && strlen($_GET['msgcontents']) > 5 && strlen($_GET['msgcontents']) <= 250){
                                    $msgErr = "";
                                    $msgErrBool = false;
                                    //Get the recipient
                                    $recipient = $_GET['recipient'];
                                    if(preg_match('/^(.*)$/ms',$_GET['recipient']) && !(ctype_space($_GET['recipient'])) && strlen($_GET['recipient']) > 4 && strlen($_GET['recipient']) <= 32){
                                        $query = "SELECT * FROM users WHERE username='$recipient'";
                                        $result = $conn->query($query);
                                        if($result){
                                            $resultArr = $result->fetch_array();
                                            //If user exists...
                                            if($recipient == $resultArr['username']){
                                                //Send the message to that user
                                                $msgErr = "";
                                                $msgErrBool = false;
                                                $recipientWithSlashes = addslashes($recipient);
                                                $msgcontentsWithSlashes = addslashes($_GET['msgcontents']);
                                                db_add_message($conn, $recipientWithSlashes, $_SESSION['currentUser'], $msgcontentsWithSlashes);
                                                //Go back to inbox
                                                header("Location: inbox_page.php");
                                            } else {
                                                $msgErr = "User does not exist.";
                                                $msgErrBool = true;
                                                $author = $_GET['recipient'];
                                                $msgcontents = $_GET['msgcontents'];
                                            }
                                        } else {
                                            $msgErr = "User does not exist.";
                                            $msgErrBool = true;
                                            $author = $_GET['recipient'];
                                            $msgcontents = $_GET['msgcontents'];
                                        }
                                    } else {
                                        $msgErr = "Recipient must be 6-32 characters.";
                                        $msgErrBool = true;
                                        $author = $_GET['recipient'];
                                        $msgcontents = $_GET['msgcontents'];
                                    }
                                } else {
                                    $msgErr = "Message must be between 5 and 250 characters.";
                                    $msgErrBool = true;
                                    $author = $_GET['recipient'];
                                    $msgcontents = $_GET['msgcontents'];
                                }
                            } else {
                                $msgErr = "Please enter a message and recipient.";
                                $msgErrBool = true;
                                $author = $_GET['recipient'];
                                $msgcontents = $_GET['msgcontents'];
                            }
                        }
                    ?>  
                    
                    <?php
                        //Handle error messages
                        if($msgErrBool){
                            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: $msgErr</div>";
                        }
                    ?>
                    
                    <!--Form-->
                    <form action="Send_Msg.php" method="get">
                        <div class="form-group">
                            <label for="recipient">Recipient: </label>
                            <input type="text" class="form-control" name="recipient" id="recipient" aria-describedby="Help" value="<?=$recipient?>" placeholder="Recipient">
                            <small id="Help" class="form-text text-muted">Enter a username that you want to send a message to</small>
                        </div>
                        <div class="form-group">
                            <label for="msgcontents">Contents: </label>
                            <textarea class="form-control" name="msgcontents" id="msgcontents" rows="7" placeholder="Message Contents"><?=$msgcontents?></textarea>
                        </div>
                            <button type="submit" id="submit" name="submit" class="btn btn-primary">Submit</button>
                    </form>
                    
                </div>
            </div>
            <div class="col-md-3">
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <a href = "Send_Msg.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Send a Message</a>  
                </div>
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <a href="createpost.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">New Post</a>
                </div>
            </div>
                    
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
