<?php
require_once('includes/header.php');
?>


<?php
require('db.php');
// Get a listing of all the threads that have been posted.
// List them in chronological order descending.

try{
    $db = connect_db();
}
catch(mysqli_sql_exception $e){
    echo "<div id='wrapper'>";
    echo "<h3>Could not connect to the database</h3>";
    echo "<h3>Please run mosteit_forum.sql before using this forum</h3>";
    echo "</div>";
}

// Header for creating new threads
echo '<div id="wrapper">';
echo '<form id="thread_form" method="post" action="new_thread.php">';
echo '<h1>Mosteit Forums</h1>';
echo '<label for="title" >Subject Title</label>';
echo '<input type="text" class="text_input" name="title" id="title"  maxlength="60"/>';
echo '<label for"username">Username</label>';
echo '<input type="text" class="text_input" name="username" id="username" maxlength="60"/>';
echo '<input type="submit" id="create_thread" value="Create Thread" />';
echo '</form>';
echo '<div id="post_div">';


$querystring = 'select * from threads order by date_posted desc;';
$rs = $db->query($querystring);

if($rs->num_rows == 0)
{
    echo "<p class='post'>There are no threads yet</p>";
}
else
{
	// Display the available threads
    echo "<ul>";
    while($row = $rs->fetch_assoc())
    {
        $thread_id    = $row['thread_id'];
        $thread_title = $row['thread_title'];
        $username     = $row['username'];
        $datetime     = $row['date_posted'];
        $date         = explode(' ',$datetime)[0];
      
        echo "<li class='post'>";
        echo "<a style='float:left;margin:.5em;' href='view_thread.php?thread_id=$thread_id'>$thread_title</a>";
        echo "<p style='float:left;margin:.5em;'>$username: $date</p>";
        echo "<a style='float:left;margin:.5em;' href = 'delete_thread.php?thread_id=$thread_id' class='delete'>Delete</a>"; 
        echo "</li>";
    }
    echo "</ul>";
}
$db->close();

?>
</div><!-- end #post_div -->
</div><!-- end #wrapper -->


<!-- Script functionality starts here -->

<script>

// Create a new text area 
var post_text = $("<br><textarea name='post_text' id='post_text' rows='7' cols='100'></textarea>");

// Add the text area to the form.
$('#thread_form').append(post_text);

// Hide the newly created text area 
$('#post_text').hide();


    // When the user focuses on either title or author, show the text area 
    $('#title, #username').focus(
        function(){
            $('#post_text').show('slow');
        }

    );

    // When the user leaves the text area, hide it if it is empty 
    $('#post_text').blur(
        function(){
        
            var text = $('#post_text').val();
            if (text.trim() === ''){
                $('#post_text').hide('slow');
            } 
        }
    ); 


    </script>
    <?php
    require_once('includes/footer.php');
    ?>
