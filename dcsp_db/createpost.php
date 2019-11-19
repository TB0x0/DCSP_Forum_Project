<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <title>Placeholder Create Post Page</title>

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
                
        $categories = array("Questions", "General", "Off-Topic");
        
    ?>

    <style>
        .border-3{
            border-width: 3px !important;
        }
        .dcsp-text-light{
            color: #dddddd !important;
        }
    </style>

  </head>
  <body style="background-color: #bfc9ca">
        <?php
            

            function alert($msg) {
                echo "<script type='text/javascript'>alert('$msg');</script>";
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
            <li class="nav-item">
                <a class="nav-link active" href="#">New Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Ipsum</a>
            </li>
            <?php
                if($loggedin && !$admin){
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
                } else if($loggedin &&  $admin) {
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
                } else {
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

    <?php
        $postErr = "";
        $postErrBool = false;
        $title = "";
        $contents = "";
        if(isset($_GET['submit'])){
			if(isset($_GET['postcategory'], $_GET['posttitle'], $_GET['postcontents'])){
                if(preg_match('/^(.*)$/ms',$_GET['posttitle']) && !(ctype_space($_GET['posttitle'])) && strlen($_GET['posttitle']) > 5 && strlen($_GET['posttitle']) <= 64){
                    $postErr = "";
                    $postErrBool = false;
                    //Title is good
                    if(preg_match('/^(.*)$/ms',$_GET['postcontents']) && !(ctype_space($_GET['postcontents'])) && strlen($_GET['postcontents']) > 5 && strlen($_GET['postcontents']) <= 500){
                        $postErr = "";
                        $postErrBool = false;
                        //Contents is good

                        // $query = "SELECT DISTINCT category FROM posts";
                        // $result = $conn->query($query);
                        // if($result){
                        //     while($resultArr = $result->fetch_array()){
                            foreach($categories as $category){
                                if($_GET['postcategory'] == $category){
                                    $postErr = "";
                                    $postErrBool = false;
                                    //ALL INFO IS GOOD, ADD THE POST AND FORWARD USER TO IT'S PAGE
                                    db_add_post($conn, $_SESSION['currentUser'], $_GET['posttitle'], $_GET['postcontents'], $_GET['postcategory']);
                                    $username = $_SESSION['currentUser'];
                                    $query2 = "SELECT post_id FROM posts where username = '$username'";
                                    $result2 = $conn->query($query2);
                                    if($result2){
                                        $newestPost = 0;
                                        while($resultArr2 = $result2->fetch_array()){
                                            if($resultArr2['post_id'] > $newestPost){
                                                $newestPost = $resultArr2['post_id'];
                                            }
                                        }
                                    }
                                    header("Location: post.php?post_id=$newestPost");
                                }
                            }
                            $postErr = "Invalid category.";
                            $postErrBool = true;
                            $title = $_GET['posttitle'];
                            $contents = $_GET['postcontents'];
                        //}
                        
                    } else {
                        $postErr = "Your post contents must be between 5 and 500 characters.";
                        $postErrBool = true;
                        $title = $_GET['posttitle'];
                        $contents = $_GET['postcontents'];
                    }
                } else {
                    $postErr = "You must enter a title for your post between 5 and 64 characters.";
                    $postErrBool = true;
                    $title = $_GET['posttitle'];
                    $contents = $_GET['postcontents'];
                }
			} else {
                $postErr = "You must choose a category, and enter a title and contents.";
                $postErrBool = true;
                $title = $_GET['posttitle'];
                $contents = $_GET['postcontents'];
            }
        }
    ?>


    <div class="container pt-5" style="background-color: #abb2b9">
        <div class="row">
            <div class="col-md-9">
                <div class="container-fullwidth border border-dark border-3 p-3">

                    <?php

                        if(!$loggedin){
                            echo "<div class=\"container text-center\">
                            <h3>You must be logged in to create a post!</h3>
                            <a href=\"login.php\" class=\"btn btn-primary btn-lg active\" role=\"button\" aria-pressed=\"true\">Log in</a>
                            </div>";
                        } else {
                            echo "<div class=\"container\">";
                            if($postErrBool){
                            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: $postErr</div>";
                            }
                                
                            echo "<form action=\"createpost.php\" method=\"get\">
                                <div class =\"form-group\">
                                    <label for=\"postcategory\">Category: </label>
                                    <select class=\"category-select\" name=\"postcategory\" id=\"postcategory\">";
                                        // $query = "SELECT DISTINCT category FROM posts";
                                        // $result = $conn->query($query);
            
                                        // if($result){
                                        //     while($resultArr = $result->fetch_array()){
                                        //         echo "<option value=\"" . $resultArr['category'] . "\">" . $resultArr['category'] . "</option>";
                                        //     }
                                        // }
                                        
                                        foreach($categories as $category){
                                            echo "<option value=\"" . $category . "\">" . $category . "</option>";
                                        }
                                        
                                    echo "</select>
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"posttitle\">Title: </label>
                                    <input type=\"text\" class=\"form-control\" name=\"posttitle\" id=\"posttitle\" aria-describedby=\"postTitleHelp\" value=\"$title\">
                                    <small id=\"postTitleHelp\" class=\"form-text text-muted\">Enter a name for your new post</small>
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"postcontents\">Contents: </label>
                                    <textarea class=\"form-control\" name=\"postcontents\" id=\"postcontents\" rows=\"7\">$contents</textarea>
                                </div>
                                <button type=\"submit\" id=\"submit\" name=\"submit\" class=\"btn btn-primary\">Submit</button>
                            </form>
                        </div>";
                        }
                    ?>
                    
                    
                </div>
            </div>

            <div class="col-md-3">
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. 
                    Perferendis numquam beatae optio aliquam asperiores quod veniam 
                    adipisci repellendus reiciendis exercitationem, quas aperiam natus 
                    odio! Ut magnam tempora quod dicta exercitationem.</p>
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