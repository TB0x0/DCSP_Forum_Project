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
        //Handle login state
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
                <a class="nav-link" href="#">Inbox</a>
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
                        //Direct user to login if they're a guest
                        if(!$loggedin){
                            echo "<div class=\"container text-center\">
                            <h3>You must be logged in to view the inbox!</h3>
                            <a href=\"login.php\" class=\"btn btn-primary btn-lg active\" role=\"button\" aria-pressed=\"true\">Log in</a>
                            </div>";
                        } else {
                            //Show total number of messages in inbox
                            echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                            <div class=\"col-md-3\">
                                <h3 class=\"dcsp-text-light\">Inbox</h3>
                            </div>
                            <div class=\"col-md-6\"></div>
                            <div class=\"col-md-3\">";
                            $recipient = $_SESSION['currentUser'];
                            $query = "SELECT * FROM messages WHERE username = '$recipient'";
                            $result = $conn->query($query);
                            if($result){
                                $resultArr = $result->fetch_array();
                                $row_cnt = $result->num_rows;
                                echo "<p class=\"text-right dcsp-text-light\">" . $row_cnt . " messages</p></div></div>";
                            } else {
                                echo "<p class=\"text-right dcsp-text-light\">No messages</p></div></div>";
                            }
                            
                            //Get every message sent to the user
                            $query = "SELECT * FROM messages WHERE username = '$recipient' ORDER BY message_id DESC";
                            $result = $conn->query($query);

                            if($result){
                                while($resultArr = $result->fetch_array()){
                                    //Display every message's author, timestamp, and contents
                                    echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                                    <div class=\"col-md-8\">
                                        <h3 class=\"dcsp-text-light\">From: " . $resultArr['author'] . "</h3>
                                    </div>
                                    <div class=\"col-md-4\">
                                        <h5 class=\"dcsp-text-light\">At: " . date("Y-M-d H:i:s", strtotime($resultArr['time']) - 6 * 3600 ) . "</h5>
                                    </div>
                                    </div>";
                                    
                                    echo "<div class=\"row border border-dark border-3 rounded ml-3 mr-3 pt-3 pb-3\" style=\"background-color: #bbbbbb\">
                                    <div class=\"col-md-12\">";
                                    echo "<p>" . nl2br($resultArr['message']) . "</p></div></div>";

                                }
                            }
                        }
                        
                        
                    ?>
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
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
