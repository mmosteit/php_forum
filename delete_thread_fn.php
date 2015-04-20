<?php

require_once('db.php');

function delete_thread($thread_id){

    try{
        $db = connect_db();
    }
    catch(mysqli_sql_exception $e){
        throw $e;
    }

    $int_thread_id = intval($thread_id);

    // Delete the thread
    $prepared = $db->prepare("delete from threads where thread_id = ?;");
    $prepared->bind_param('i', $int_thread_id);
    $prepared->execute();
    $prepared->close();

    // Delete all of the posts
    $prepared = $db->prepare("delete from posts where thread_id = ?;");
    $prepared->bind_param('i', $int_thread_id);
    $prepared->execute();
}

?>
