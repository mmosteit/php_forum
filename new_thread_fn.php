<?php

require_once('db.php'); // Allow for connecting to the database

function new_thread($thread_title, $username, $post_text){

    // Both the username and post id must be set
    if(	!(isset($thread_title)      && trim($thread_title) != "") || 
                !(isset($username)  && trim($username)     != "") || 
                !(isset($post_text) && trim($post_text)    != "")){
        throw new Exception("Error, must include thread title, username and post text when creating a new thread.");
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = connect_db(); 

    if($db->connect_error || !isset($db)){
        throw new Exception("Error: Database connection failure");  
    }

    // Create the thread 
    $date_posted = date("Y-m-d h:i:s");
    $prepared = $db->prepare("insert into threads(thread_title, username,date_posted) values (?,?,?);");
    $prepared->bind_param('sss',$thread_title, $username, $date_posted);
    if (! $prepared->execute()){
        $db->close();
        throw new Exception("There was an error inserting data into the thread table on line ".__LINE__." of file ".__FILE__);
    }
    $prepared->close();

    // Get the id of the new thread
    $prepared = $db->prepare( "select thread_id from threads where thread_title = ? and date_posted = ?;");
    $prepared->bind_param('ss', $thread_title, $date_posted);
    if (!$prepared->execute()){
        $db->close();
        throw new Exception("Error with the result set on line ".__LINE__."of file ".__FILE__);
    }   

    $prepared->bind_result($thread_id);
    $prepared->fetch();
    $prepared->close();

    // Create the dummy post. This post is the parent of all the top level
    // posts in this thread.  Being a dummy post, it contains no text
    $parent_id = 0;
    $prepared  = $db->prepare("insert into posts(parent_id, date_posted, thread_id) values (?, ?, ?);");
    $prepared->bind_param('isi', $parent_id, $date_posted, $thread_id);
    if(!$prepared->execute()){ 
        $db->close();
        throw new Exception("Error inserting dummy node on line".__LINE__." of file ".__FILE__);
    }
    $prepared->close();

    // Find the post_id of the dummy post
    // Since this query contains no external input, prepared statements are not needed.
    $sqlstring = "select post_id from posts where date_posted = '".$date_posted."' and thread_id = ".$thread_id.";";
    $result = $db->query($sqlstring);

    if($result == false){
        $db->close();
        throw new Exception("Error selecting post_id on line".__LINE__." of file ".__FILE__);
    }

    $row = $result->fetch_assoc();


    // Set the first_post_id for the newly created thread to be the dummy_post
    $first_post_id = $row['post_id'];
    $result = $db->query("update threads set first_post_id = ".$first_post_id." where thread_id = ".$thread_id.";");

    if($result == false){
        $db->close();
        throw new Exception("Error updating thread on line ".__LINE__." of file ".__FILE__);
    } 

    // Insert the actual first post into the table
    $prepared = $db->prepare("insert into posts(parent_id, username, post_text, date_posted, thread_id) values (?,?,?,?,?);");
    $prepared->bind_param("isssi", $first_post_id,$username, $post_text, $date_posted, $thread_id);
    if(! $prepared->execute()){
        $db->close();
        throw new Exception("There was an error inserting data into the post table on line".__LINE__." in file ".__FILE__);
    }

    $db->close();
}

?>
