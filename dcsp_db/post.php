<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <title>Stack Underflow</title>

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
    <div class="container pt-5" style="background-color: #abb2b9">
        <div class="row">
            <div class="col-md-9">
                <div class="container-fullwidth border border-dark border-3 p-3">

                    <?php
                        if(isset($_GET['deleteComment'])){
                            $postID = $_SESSION['postID'];
                            $commentID = $_GET['deleteComment'];
                            db_delete_comment($conn, $postID, $commentID);
                            header("Location: post.php?post_id=$postID");
                            
                        }
                        if(isset($_GET['deletePost'])){
                            $postID = $_SESSION['postID'];
                            $queryDelComm = "SELECT * FROM comments WHERE post_id = '$postID'";
                            $resultDelComm = $conn->query($queryDelComm);
                            if($resultDelComm){
                                while($resultArrDelComm = $resultDelComm->fetch_array()){
                                    db_delete_comment($conn, $postID, $resultArrDelComm['comment_id']);
                                }
                                db_delete_post($conn, $postID);
                                header("Location: main.php");
                            }
                        }
                        if(isset($_GET['submitCommEdit'])){
                            $postID = $_SESSION['postID'];
                            $commentID = $_GET['submitCommEdit'];
                            $commentErr = "";
                            $commentErrBool = false;
                            $contents = "";
                            if(isset($_GET['submitCommEdit'])){
                                if(isset($_GET['commentcontents'])){
                                    if(preg_match('/^(.*)$/ms',$_GET['commentcontents']) && !(ctype_space($_GET['commentcontents'])) && strlen($_GET['commentcontents']) > 5 && strlen($_GET['commentcontents']) <= 500){
                                        $commentErr = "";
                                        $commentErrBool = false;
                                        //Contents is good
                            
                                        //ALL INFO IS GOOD, ADD THE POST AND FORWARD USER TO IT'S PAGE
                                        db_edit_comment($conn, $commentID, $_GET['commentcontents']);
                                        header("Location: post.php?post_id=$postID");
                                        
                                    } else {
                                        $commentErr = "Your comment contents must be between 5 and 500 characters.";
                                        $commentErrBool = true;
                                        $contents = $_GET['commentcontents'];
                                    }
                                }
                            }
                        }
                        if(isset($_GET['submitEdit'])){
                            $postID = $_SESSION['postID'];
                            $postErr = "";
                            $postErrBool = false;
                            $contents = "";
                            if(isset($_GET['submitEdit'])){
                                if(isset($_GET['postcontents'])){
                                    if(preg_match('/^(.*)$/ms',$_GET['postcontents']) && !(ctype_space($_GET['postcontents'])) && strlen($_GET['postcontents']) > 5 && strlen($_GET['postcontents']) <= 500){
                                        $postErr = "";
                                        $postErrBool = false;
                                        //Contents is good
                            
                                        //ALL INFO IS GOOD, ADD THE POST AND FORWARD USER TO IT'S PAGE
                                        db_edit_contents($conn, $postID, $_GET['postcontents']);
                                        header("Location: post.php?post_id=$postID");
                                        
                                    } else {
                                        $postErr = "Your post contents must be between 5 and 500 characters.";
                                        $postErrBool = true;
                                        $title = $_GET['posttitle'];
                                        $contents = $_GET['postcontents'];
                                    }
                                }
                            }
                        }
                        if(isset($_GET['editPost'])){
                            $postID = $_SESSION['postID'];
                            $query = "SELECT * FROM posts WHERE post_id = '$postID'";
                            $result = $conn->query($query);
                            if($result){
                                $resultArr = $result->fetch_array();
                            }
                            echo "<form action=\"post.php\" method=\"get\">
                                    <div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                                    <div class=\"col-md-12\">
                                    <h3 class=\"dcsp-text-light\">" . $resultArr['post_title'] . "</h3>
                                    </div></div>
                                <div class=\"form-group\">
                                    <label for=\"postcontents\">Contents: </label>
                                    <textarea class=\"form-control\" name=\"postcontents\" id=\"postcontents\" rows=\"7\">" . $resultArr['contents'] . "</textarea>
                                </div>
                                <button type=\"submitEdit\" id=\"submitEdit\" name=\"submitEdit\" class=\"btn btn-primary\">Update Post</button>
                            </form>";
                        } else {
                            if(!(isset($_SESSION['postID']))){
                                $_SESSION['postID'] = $_GET['post_id'];
                            }
                            $postID = $_SESSION['postID'];
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
                                echo "<p>" . nl2br($resultArr['contents']) . "</p></div></div>";
                            }
                        }
                         
                    ?>                 
                            
                    
                </div>
                
                <div class="container-fullwidth border border-dark border-3 p-3">
                    <?php
                        $commentErr = "";
                        $commentErrBool = false;
                        $contents = "";
                        if(isset($_GET['submit'])){
                            if(isset($_GET['commentcontents'])){
                                if(preg_match('/^(.*)$/ms',$_GET['commentcontents']) && !(ctype_space($_GET['commentcontents'])) && strlen($_GET['commentcontents']) > 5 && strlen($_GET['commentcontents']) <= 500){
                                    $commentErr = "";
                                    $commentErrBool = false;

                                    $time = time();
                                    $mysqltime = date("Y-m-d H:i:s", $phptime);
                                    db_add_comment($conn, $postID, $_SESSION['currentUser'], $_GET['commentcontents']);
                                    $username = $_SESSION['currentUser'];
                                    $query2 = "SELECT post_id FROM posts where username = '$username'";
                                    $result2 = $conn->query($query2);
                                    header("Location: post.php?post_id=$postID");
                                    
                                } else {

                                    $commentErr = "Your comment must be between 5 and 500 characters.";
                                    $commentErrBool = true;
                                    $contents = $_GET['commentcontents'];
                                }
                            } else {
                                $commentErr = "You must enter a comment.";
                                $commentErrBool = true;
                                $contents = $_GET['commentcontents'];
                            }
                        }
                    ?>
                    <div class="container-fullwidth p-3">
                        <?php

                            if(!$loggedin){
                                echo "<div class=\"container text-center\">
                                <h3>You must be logged in to comment!</h3>
                                <a href=\"login.php\" class=\"btn btn-primary btn-lg active\" role=\"button\" aria-pressed=\"true\">Log in</a>
                                </div>";
                            } else {
                                echo "<div class=\"container\">";
                                if($commentErrBool){
                                echo "<div class=\"alert alert-danger\" role=\"alert\">Error: $commentErr</div>";
                                }
                                    
                                echo "<form action=\"post.php?post_id=$postID\" method=\"get\">
                                    <div class=\"form-group\">
                                        <label for=\"commentcontents\">Comment: </label>
                                        <textarea class=\"form-control\" name=\"commentcontents\" id=\"commentcontents\" rows=\"7\">$contents</textarea>
                                    </div>
                                    <button type=\"submit\" id=\"submit\" name=\"submit\" class=\"btn btn-primary\">Submit</button>
                                </form>
                            </div>";
                            }
                        ?>
                    </div>

                    <?php
                        if(isset($_GET['editComment'])){
                            $postID = $_SESSION['postID'];
                            $commentID = $_GET['editComment'];
                            $queryComm = "SELECT * FROM comments WHERE comment_id = '$commentID'";
                            $resultComm = $conn->query($queryComm);
                            $resultArrComm = $resultComm->fetch_array();
                            echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                                    <div class=\"col-md-9 text-truncate\">
                                        <h3 class=\"dcsp-text-light\">" . $resultArrComm['username'] . "</h3>
                                        <h5 class=\"dcsp-text-light\">" . date("Y-M-d H:i:s", strtotime($resultArrComm['time']) - 6 * 3600 ) . "</h5>
                                    </div>";
                                    echo "<div class=\"col-md-3\">";   
                                        
                                    echo "</div>
                                    </div>";
                                    
                                    echo "<div class=\"row border border-dark border-3 rounded ml-3 mr-3 pt-3 pb-3\" style=\"background-color: #bbbbbb\">
                                    <div class=\"col-md-12\">";

                                    echo "<form action=\"post.php?post_id=$postID\" method=\"get\">
                                    <div class=\"form-group\">
                                        <label for=\"commentcontents\">Comment: </label>
                                        <textarea class=\"form-control\" name=\"commentcontents\" id=\"commentcontents\" rows=\"7\">" . $resultArrComm['contents'] . "</textarea>
                                    </div>
                                    <button type=\"submitCommEdit\" id=\"submitCommEdit\" name=\"submitCommEdit\" value=\"" . $commentID . "\" class=\"btn btn-primary\">Submit</button>
                                     </form></div></div>";
                        } else {


                            $queryComm = "SELECT * FROM comments WHERE post_id = '$postID' ORDER BY comment_id DESC";
                            $resultComm = $conn->query($queryComm);
                            if($resultComm){
                                while($resultArrComm = $resultComm->fetch_array()){
                                    echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                                    <div class=\"col-md-9 text-truncate\">
                                        <h3 class=\"dcsp-text-light\">" . $resultArrComm['username'] . "</h3>
                                        <h5 class=\"dcsp-text-light\">" . date("Y-M-d H:i:s", strtotime($resultArrComm['time']) - 6 * 3600 ) . "</h5>
                                    </div>";
                                    echo "<div class=\"col-md-3\">";   
                                        if($loggedin){
                                            if($_SESSION['currentUser'] == $resultArrComm['username'] || $_SESSION['currentUserType'] == "admin"){
                                                echo "<div class=\"row text-center\">
                                                        <div class=\"col-md-6\"><form method=\"get\" action=\"post.php\" onsubmit=\"return confirm('Do you really want to delete this comment?');\"><div class=\"pb-2\"><button type=\"deleteComment\" id=\"deleteComment\" value=\"" . $resultArrComm['comment_id'] . "\" name=\"deleteComment\" class=\"btn btn-danger\">Delete</button></div></form></div>";
                                                        if($_SESSION['currentUser'] == $resultArrComm['username']){
                                                            echo "<div class=\"col-md-6\"><form method=\"get\" action=\"post.php\"><div class=\"pb-2\"><button type=\"editComment\" id=\"editComment\" value=\"" . $resultArrComm['comment_id'] . "\" name=\"editComment\" class=\"btn btn-primary\">Edit</button></div></form></div>";
                                                        }
                                                    echo "</div>";
                                            }
                                        }
                                    echo "</div>
                                    </div>";
                                    
                                    echo "<div class=\"row border border-dark border-3 rounded ml-3 mr-3 pt-3 pb-3\" style=\"background-color: #bbbbbb\">
                                    <div class=\"col-md-12\">";
                                    echo "<p>" . nl2br($resultArrComm['contents']) . "</p></div></div>";
                                }
                            }  
                        }  
                    ?>                 
                            
                    
                </div>
            </div>

            <div class="col-md-3">
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <a href="createpost.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">New Post</a>
                </div>
                <div class="container-fullwidth border border-dark border-3 p-3 text-center">
                    <?php
                        $query = "SELECT * FROM posts WHERE post_id = '$postID'";
                        $result = $conn->query($query);
                        if($result){
                            $resultArr = $result->fetch_array();
                            
                            echo "<div class=\"row border border-dark border-3 rounded pt-3 pb-3\" style=\"background-color: #171717\">
                            <div class=\"col-md-12\">
                                <h3 class=\"dcsp-text-light\">Posted By:</h3>
                                <h5 class=\"dcsp-text-light\">" . $resultArr['username'] . "</h5>
                                <h3 class=\"dcsp-text-light\">On:</h3>
                                <h5 class=\"dcsp-text-light\">" . date("Y-M-d H:i:s", strtotime($resultArr['time']) - 6 * 3600 ) . "</h5>
                                ";
                            
                            if($loggedin){
                                if($_SESSION['currentUser'] == $resultArr['username'] || $_SESSION['currentUserType'] == "admin"){
                                    echo "<div class=\"container-fullwidth border border-dark border-3 p-3 text-center\">
                                            <form method=\"get\" action=\"post.php\" onsubmit=\"return confirm('Do you really want to delete this post?');\"><div class=\"pb-2\"><button type=\"deletePost\" id=\"deletePost\" name=\"deletePost\" class=\"btn btn-danger\">Delete Post</button></div></form>";
                                            if($_SESSION['currentUser'] == $resultArr['username']){
                                                echo "<form method=\"get\" action=\"post.php\"><div class=\"pb-2\"><button type=\"editPost\" id=\"editPost\" name=\"editPost\" class=\"btn btn-primary\">Edit Post</button></div></form>";
                                            }
                                            
                                            echo "</div>";
                                }
                            }
                                echo "</div></div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="ajax.js"></script>
  </body>
</html>