<?php
require_once('includes/header.php');

require_once('db.php'); // Allow for connecting to the database
require_once('new_thread_fn.php');


$title     = trim($_POST['title']);
$username  = trim($_POST['username']);
$post_text = trim($_POST['post_text']);
try{
    new_thread($title, $username, $post_text );
    echo "<script>document.location.replace('index.php')</script>";
}
catch(Exception $e){
    echo "<div id='wrapper'>";
    echo "<p>".$e->getMessage()."</p>";
    echo "<a href='index.php'>Click here to return to main</a>";
    echo "</div>";
}


require_once('includes/footer.php');
?>
