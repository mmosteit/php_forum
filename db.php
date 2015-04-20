<?php

function connect_db(){
    try{
        $db = new mysqli('localhost','mosteit_forum','password','mosteit_forum');
        return $db;
    }
    catch(mysqli_sql_exception $e){
        throw $e;
    }
}

?>
