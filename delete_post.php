<?php
require_once('includes/header.php');
require_once('view_thread_fn.php');
$post_id = $_GET['post_id'];
$thread_id = $_GET['thread_id'];

if(!isset($post_id)  ){
    echo "<div id='wrapper'>";
    echo "<p>Error, no post has been selected. Please access this page by clicking the delete button on a post</p>";
    echo "<a href='index.php'>Return to main</a>";
    echo "</div>";
}
// The post does not consist entirely of numbers.
else if(!ctype_digit($post_id)){
    echo "<div id='wrapper'>";
    echo "<p>Invalid post id</p>";
    echo "<a href='index.php'>Return to main</a>";
    echo "</div>"; 
}
else{

    delete_post($post_id);
    echo "<script>window.location.replace('view_thread.php?thread_id=$thread_id');</script>";  

}


require_once('includes/footer.php');
?>
