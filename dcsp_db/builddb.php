<?php //Group 10 Project Database
    require_once 'login.php', 'dbfunctions.php';
    $connection = new mysqli($hn, $un, $pw, $db);

    if ($connection->connect_error) die($connection->connect_error);

    $create_users_table = "CREATE TABLE users (
    username VARCHAR(32) NOT NULL UNIQUE,
    status  VARCHAR(15) NOT NULL,
    display_name VARCHAR(32) NOT NULL,
    password VARCHAR(32) NOT NULL,
    PRIMARY_KEY (username)
    )";
    $result = $connection->query($create_users_table);
    if (!$result) die($connection->error);
    echo "Users table created";

    $create_posts_table = "CREATE TABLE posts (
    post_id INT AUTO_INCREMENT,
    username VARCHAR(32) NOT NULL UNIQUE,
    post_title VARCHAR(64) NOT NULL,
    date DATE NOT NULL,
    url VARCHAR(128) NOT NULL UNIQUE,
    PRIMARY_KEY (post_id),
    FOREIGN_KEY (username) REFERENCES users(username)
    )";
    $result = $connection->query($create_posts_table);
    if (!$result) die($connection->error);
    echo "Posts table created";

    $create_comments_table = "CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT,
    post_title VARCHAR(64) NOT NULL,
    username VARCHAR(32) NOT NULL UNIQUE,
    time SMALLDATETIME NOT NULL,
    contents varchar(500),
    PRIMARY_KEY (comment_id),
    FOREIGN_KEY (post_title) REFERENCES posts(post_title),
    FOREIGN_KEY (username) REFERENCES users(username))";
    $result = $connection->query($create_comments_table);
    if (!$result) die($connection->error);
    echo "Comments table created";

    $create_messages_table = "CREATE TABLE messages (
    message_id INT AUTO_INCREMENT,
    username VARCHAR(32) NOT NULL,
    author VARCHAR(32) NOT NULL,
    time SMALLDATETIME NOT NULL,
    message VARCHAR(250),
    PRIMARY_KEY (message_id),
    FOREIGN_KEY (username) REFERENCES users(username),
    FOREIGN_KEY (author) REFERENCES users(username))";
    $result = $connection->query($create_messages_table);
    if (!$result) die($connection->error);
    echo "Messages table created";
    

    $sal1    = "zx&h^"; $sal2    = "qp%@&";
    $username = 'admin';
    $status = 'admin';
    $display_name = 'poqjwdpojqw';
    $password = 'password12';
    $token = hash('ripemd128', "$sal1$password$sal2");

    add_user($connection, $username, $status, $display_name, $token);

    $connection->close();
?>
