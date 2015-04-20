<?php
require_once('db.php');


function new_post($post_text, $username,$parent_id,$thread_id, $url){

    $date_posted = date("Y-m-d h:i:s");

    // Verify all of the info
    if (!(isset($post_text) && trim($post_text) != '')  || !(isset($username) && trim($username) != '')){
        throw new Exception("Error: must set both username and post");
    }

    $db = connect_db();

    if($db->connect_error || !isset($db)){
        throw new Exception("Database connection failure");
    }

    $sqlstring = "insert into posts(username, parent_id, post_text, date_posted ) values(?,?,?,?);";
    $statement = $db->prepare($sqlstring);
    $statement->bind_param("siss",$username, $parent_id, $post_text, $date_posted);
    $statement->execute();


    echo "<script>window.location.replace('".$url."');</script>";  
}
?>
