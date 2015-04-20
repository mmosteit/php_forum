<?php
require_once('includes/header.php');
require_once('db.php');

// Verify all of the info
$post_text   = $_POST['post_text'];
$username    = $_POST['username']; 
$parent_id   = $_POST['parent_id'];
$thread_id   = $_POST['thread_id'];
$url         = $_POST['url'];
$date_posted = date("Y-m-d h:i:s");

if (!(isset($post_text) && trim($post_text) != '')  || !(isset($username) && trim($username) != '')){
    echo "<div id='wrapper'>";
    echo "<p>Error: must set both username and post text</p>";
    echo "</div>";
}
if(!ctype_digit($parent_id)||!ctype_digit($thread_id)){
	echo "<div id='wrapper'>";
	echo "<p>invalid value for parent_id or thread_id</p>";
	echo "</div>";
}
else{

    $db = connect_db();

    if($db->connect_error || !isset($db)){
        echo "<div id='wrapper'><p>Database connection failure</p>";
        echo "<a href='index.php'><h1>Return to index</h1></a></div>";
    }
    else{

        $sqlstring = "insert into posts(username, parent_id, post_text, date_posted , thread_id) values(?,?,?,?,?);";
        $statement = $db->prepare($sqlstring);
        $statement->bind_param("sissi",$username, $parent_id, $post_text, $date_posted, $thread_id);
        $statement->execute();

        echo "<script>window.location.replace('".$url."');</script>";  

    }
}

require_once('scripts/footer.php');
?>
