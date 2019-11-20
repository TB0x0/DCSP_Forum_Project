<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <title>DCSP Forum</title>

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
    <h3 text_align="center">Search Results</h3>
    <?php
        $srchvar = $_GET['search_var'];
        $query = "SELECT * FROM posts WHERE post_title LIKE '%$srchvar%' OR username LIKE '%$srchvar%'";
        $result = $conn->query($query);

        if($result){
            $tmpVal = 0;
            while(($resultArr = $result->fetch_array()) && $tmpVal < 5){
                echo "<div class=\"row border border-dark border-3 rounded ml-3 mr-3 pt-3 pb-3\" style=\"background-color: #bbbbbb\">
                <div class=\"col-md-6 text-truncate\">";
                    $postID = $resultArr['post_id'];
                    echo "<a href=\"post.php?post_id=" . $postID . "\" class=\"text-info stretched-link\" style=\"font-family: 'Roboto', sans-serif; font-size: 20px;\">" . $resultArr['post_title'] . "</a>";
                echo "</div>
                <div class=\"col-md-2 text-truncate\">";
                    echo "<p>Posted by: " . $resultArr['username'] . "</p>";
                echo "</div>
                <div class=\"col-md-2\">";
                    echo date("Y-M-d H:i:s", strtotime($resultArr['time']) - 6 * 3600 );
                echo "</div>
                <div class=\"col-md-2\">";
                    $postID = $resultArr['post_id'];
                    $queryComments = "SELECT * FROM comments WHERE post_id = '$postID'";
                    $resultComments = $conn->query($queryComments);
                    echo "<p>Comments: " . $resultComments->num_rows . "</p>";
                echo "</div>
                </div>";

                $tmpVal += 1;
            }
        }
    ?>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>