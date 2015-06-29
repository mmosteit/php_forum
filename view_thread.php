<?php
require_once('includes/header.php');
require_once('db.php');
require_once('view_thread_fn.php');

$thread_id = $_GET['thread_id'];

// If the thread_id is not set, the user did not come here from the index.
if(!isset($thread_id)){
    echo "<div id='wrapper' >";
    echo "<p>No thread selected</p>";
    echo "<p>Please access this forum through the <a href='index.php'>main site</a></p>";
    echo "</div>";
}
// Possible sql injection attempt.
else if(!is_numeric($thread_id)){
    echo "<div id='wrapper'>";
    echo "<p>Error: Invalid thread id.</p>";
    echo "</div>"; 
}
else{

    echo "<div id='wrapper' thread_id='$thread_id'>";

    try{
        $db = connect_db();
    }
    catch(mysqli_sql_exception $e){
        echo "<p>There was an error connecting to the database. Please contact the webmaster</p>"; 
        echo "</div>";
    }

    echo "<a href='index.php'>Back to index</a>\r\n";

    $sqlstring = "select first_post_id, thread_title from threads where thread_id = ?";
    $statement = $db->prepare($sqlstring);

    $int_thread_id = intval($thread_id);
    $statement->bind_param("i",$int_thread_id);
    $statement->execute(); 

    $statement->bind_result($first_post_id,$thread_title);

    // The thread has been deleted between the time the user views the index
    // and clicks on the first link


    if(!($statement->fetch())){
        echo "<p>This thread has been deleted.";
        echo "</div>";
    }
    else{
        $statement->close();
        try{
            $tree = new post_tree($db, $first_post_id, null); // We are passing in null because the top level node has no parent    

            echo "<h2 class='thread_title'>".htmlspecialchars($thread_title)."</h2>";

            // This is where the majority of the page is generated
            $tree->display_posts();
        }
        catch(Exception $e){
            echo "<p>".$e->getMessage()."</p>"; 
        } 


    } 
    echo "</div>";
    $db->close();
}

?>
<script>

// Add a new post to the existing branch
$('.reply_current_branch, .reply_new_branch').click(
        function(){

        var id           = $(this).parent().parent().attr('id');
        var textarea     = $("<textarea name='post_text' id='post_text"+id+"'rows='7' cols='100'></textarea>");
        var username_div = $("<div class='username_div' </div>");
        var username     = $("<label for='username"+id+"'>Username</label><input type='text' class='text_input'  name='username' id='username"+id+"' >");
        var button       = $("<input type='submit' class='post_button'  id='reply_button"+id+"' value='post'>");
        var parent_id    = $("<input type='hidden' name='parent_id' value ='"+id+"'>");
        var thread_id    = $("<input type='hidden' name='thread_id' value ='"+$("#wrapper").attr('thread_id')+"'>");
        var url          = $("<input type='hidden' name='url' value='"+document.location.href+"'>");

        // Hide the reply button 
        $(this).hide();

        // Create a form with the textarea and button
        form = $("<form method='post' action='new_post.php' accept-charset='utf-8'>")
        $(form).append(textarea);

        $(username_div).append(username);
        $(username_div).append(button);
        $(form).append(username_div);

        // Create the hidden elements used in the post data
        $(form).append(parent_id);
        $(form).append(thread_id);
        $(form).append(url);

        $(this).parent().append(form);
        }
);



</script>
<?php
require_once('includes/footer.php');
?>
