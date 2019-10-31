<?php //Group 10 Project Database
    require_once 'dblogin.php'; require_once 'dbfunctions.php';
    $connection = new mysqli($hn, $un, $pw, $db);

    if ($connection->connect_error) die($connection->connect_error);

    $create_users_table = "CREATE TABLE users (
    username VARCHAR(32) NOT NULL UNIQUE,
    status VARCHAR(15) NOT NULL,
    display_name VARCHAR(32) NOT NULL,
    password VARCHAR(32) NOT NULL,
    PRIMARY KEY (username)
    )";
    $result = $connection->query($create_users_table);
    if (!$result) die($connection->error);
    echo "Users table created";

    $create_posts_table = "CREATE TABLE posts (
    post_id INT AUTO_INCREMENT,
    username VARCHAR(32) REFERENCES users(username),
    post_title VARCHAR(64) NOT NULL,
    date DATE NOT NULL,
    url VARCHAR(128) NOT NULL UNIQUE,
    PRIMARY KEY (post_id)
    )";
    $result = $connection->query($create_posts_table);
    if (!$result) die($connection->error);
    echo "<br>Posts table created";

    $create_comments_table = "CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT,
    post_title VARCHAR(64) REFERENCES posts(post_title),
    username VARCHAR(32) REFERENCES users(username),
    time DATETIME NOT NULL,
    contents varchar(500),
    PRIMARY KEY (comment_id)
    )";
    $result = $connection->query($create_comments_table);
    if (!$result) die($connection->error);
    echo "<br>Comments table created";

    $create_messages_table = "CREATE TABLE messages (
    message_id INT AUTO_INCREMENT,
    username VARCHAR(32) REFERENCES users(username),
    author VARCHAR(32) REFERENCES users(username),
    time DATETIME NOT NULL,
    message VARCHAR(250),
    PRIMARY KEY (message_id)
    )";
    $result = $connection->query($create_messages_table);
    if (!$result) die($connection->error);
    echo "<br>Messages table created";
    

    $sal1    = "zx&h^"; $sal2    = "qp%@&";
    $username = 'admin';
    $status = 'admin';
    $display_name = 'poqjwdpojqw';
    $password = 'password12';
    $token = hash('ripemd128', "$sal1$password$sal2");

    db_add_user($connection, $username, $status, $display_name, $token);

    $connection->close();
?>
