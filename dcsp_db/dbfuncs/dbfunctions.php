<?php
// Use these functions to insert properties into the database dcsp_project_db

function db_add_user($connection, $un, $st, $dn, $pw){
    $insert_into_users  = "INSERT INTO users (username, status, display_name, password) ". "VALUES('$un', '$st', '$dn', '$pw')";
    $result = $connection->query($insert_into_users);
    if (!$result) die($connection->error);}

function db_add_post($connection, $un, $pt, $cn, $ct){
    $insert_into_posts = "INSERT INTO posts (username, post_title, contents, category, date)"."VALUES('$un','$pt','$cn','$ct',CURDATE())";
    $result = $connection->query($insert_into_posts);
    if (!$result) die($connection->error);}

function db_add_comment($connection, $pi, $un, $cn){
    $insert_into_comments = "INSERT INTO comments (post_id, username, time, contents)"."VALUES('$pi','$un',UTC_TIMESTAMP, '$cn')";
    $result = $connection->query($insert_into_comments);
    if (!$result) die($connection->error);}
    
function db_add_message($connection, $un, $au, $ti, $msg){
    $insert_into_messages = "INSERT INTO messages (username, author, time, message)"."VALUES('$un','$au','$ti','$msg')";
    $result = $connection->query($insert_into_messages);
    if (!$result) die($connection->error);}

function db_edit_contents($connection, $pi, $cn){
    $contents = "UPDATE posts SET contents = '".$cn."' WHERE post_id = '" .$pi. "'";
    $result = $connection->query($contents);
    if (!$result) die($connection->error);}

function db_edit_category($connection, $pi, $ct){
    $category = "UPDATE posts SET category = '" .$ct. "' WHERE post_id = '" .$pi. "'";
    $result = $connection->query($category);
    if (!$result) die($connection->error);}

function db_edit_comment($connection, $ci, $cn){
    $update_comment = "UPDATE comments SET contents = '".$cn."' WHERE comment_id='".$ci."'";
    $result = $connection->query($update_comment); 
    if (!$result) die($connection->error);}

function db_delete_post($connection, $pi){
    $delete_post = "DELETE FROM posts WHERE post_id = '".$pi."'";
    $result = $connection->query($delete_post);    
    if (!$result) die($connection->error);}

function db_delete_comment($connection, $pi, $ci){
    $delete_comment = "DELETE FROM comments WHERE post_id = '".$pi."' AND comment_id = '".$ci."'";
    $result = $connection->query($delete_comment);    
    if (!$result) die($connection->error);}

?>