<?php
require_once('includes/header.php');
require_once('delete_thread_fn.php');

$thread_id = $_GET['thread_id'];

echo "<div id='wrapper'>";
if(!isset($thread_id)){
    echo "<p>No thread selected, please come here from the <a href='index.php' main page</a><p>";
}
// Possible sql injection attempt
else if(!ctype_digit($thread_id)){

    echo "<p>Invalid thread_id</p>";
    echo "<a href='index.php'>Return to main</a>";
    echo "</div>";
    
}
else{

    try{
        delete_thread($thread_id);
        echo "<script>document.location.replace('index.php')</script>";
        echo "</div>"; 
    }
    catch(exception $e){
        echo "<p>".$e->getMessage()."</p>";
        echo "<a href='index.php'>Click here to return to main</a>";
        echo "</div>";
    }

}


require_once('includes/footer.php');
?>
