<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>DCSP Forum</title>

    <?php
        session_start();
        require_once('dbfuncs/dbfunctions.php');
        require_once('dbfuncs/dblogin.php');

        if(isset($_SESSION['currentUserType'])){
            if ($_SESSION['currentUserType'] == "admin"){
                $loggedin = true;
            } else if($_SESSION['currentUserType'] == "user") {
                $loggedin = true;
            }
        } else {
            $loggedin = false;
        }

        $conn = new mysqli($hn, $un, $pw, $db);
			if ($conn->connect_error)
                die($conn->connect_error);
                

        $categories = array("Category1", "Category2", "Category3");
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
                if($loggedin){
                    echo "<li class=\"nav-item dropdown\">
                    <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Account
                    </a>
                    <div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                    <a class=\"dropdown-item\" href=\"#\">Edit Account</a>
                    <a class=\"dropdown-item\" href=\"#\">Lorem ipsum</a>
                    <a class=\"dropdown-item\" href=\"logout.php\">Log out</a>
                    </div>
                    </li>";
                } else {
                    echo "<li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"login.php\">Log In/Create Account</a>
                    </li>";
                }
            ?>
            </ul>
        </div>
        </nav>
    </div>
    <div class="container pt-5" style="background-color: #abb2b9">
        <div class="row">
            <div class="col-md-9">
                <div class="container-fullwidth border border-dark border-3 p-3">

                    <?php
                        $postID = $_GET['post_id'];
                        $query = "SELECT * FROM posts WHERE post_id = '$postID'";
                        $result = $conn->query($query);
                        if($result){
                            $resultArr = $result->fetch_array();
                            
                            echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                            <div class=\"col-md-12\">
                                <h3 class=\"dcsp-text-light\">" . $resultArr['post_title'] . "</h3>
                            </div></div>";
                            
                            echo "<div class=\"row border border-dark border-3 rounded ml-3 mr-3 pt-3 pb-3\" style=\"background-color: #bbbbbb\">
                            <div class=\"col-md-12\">";
                            echo "<p>" . $resultArr['contents'] . "</p></div></div>";
                        }    
                    ?>                 
                            
                    
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
    <script src="bootstrap/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>