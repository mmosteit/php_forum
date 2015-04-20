<?php

require_once('db.php');

// This class reads a conversation tree out of the database and displays it
// When the user goes to view_thread.php
class post_tree{

    private $post_id;          // The id of the post.
    private $post_text;        // The message text
    private $username;         // The author of the message
    private $date_posted;      // The time and date posted
    private $children = NULL;  // All children of this post
    private $parent_id;        // The parent_id of this post as stored in the sql table
    private $deleted;          // Has this post been deleted by the moderator?
    private $thread_id;        // What thread does this post belong to?
    private $parent;      // The parent one level up in this tree.

    public function post_tree($db, $new_post_id, $parent){


        $this->post_id = $new_post_id;
        $post_id = $new_post_id;

        // Get the post content
        $querystring = 'select * from posts where post_id = '.$this->post_id; 
        $rs = $db->query($querystring);
        $this->parent = $parent;
   

        if($rs->num_rows != 1){
            // A user clicks on a thread right before a moderator deletes it
            if($s->num_rows == 0){
                throw new Exception("<p>This thread has been deleted</p>");	
            }
            // This should never happen. Database has been corrupted.
            else{
                throw new Exception("<p>There is more than one post with the post_id ".$this->post_id.". Please contact webmaster.</p>");
            }
        }
        else{
            $result            = $rs->fetch_assoc();
            $this->post_text   = $result['post_text'];
            $this->username    = $result['username'];
            $this->date_posted = $result['date_posted'];
            $this->parent_id   = $result['parent_id'];
            $this->deleted     = $result['deleted'];
            $this->thread_id   = $result['thread_id'];
        }

        // Get the content any children.
        $childquerystring = 'select * from posts where parent_id = '.$post_id." order by date_posted asc;";
        $rs = $db->query($childquerystring);

        if($rs->num_rows > 0){
            $index = 0;
            $this->children = array(); 
            while($row = $rs->fetch_assoc() ){ 
                $this->children[$index] = new post_tree($db, $row['post_id'], $this); 
                $index++;
            }
        }
    }

    public function display_posts(){

        // All of the posts have a parent node. The top level posts have a root
        // node with no text.

        if($this->post_text != NULL){ // We are not the root node

            // Display the content of this post
            echo "<li name='".$this->post_id."' class='post' id='".$this->post_id."' ><div class='post_div'>";
            echo "<p><strong>".htmlspecialchars($this->username)."</strong> on ".$this->date_posted."</p>"; 


            echo "<p class='post_text'>";

            if($this->deleted == false){
                echo htmlspecialchars($this->post_text);
                echo "<a class='delete_post' href='delete_post.php?post_id=$this->post_id&thread_id=$this->thread_id'>Delete</a>";
            }
            else{ 
                echo "This post has been deleted by the moderator";
            }

            echo "</p>";
            if($this->children == NULL){
                echo "<button type='button' class='reply_new_branch'>Reply to this post</button>";
            }
            echo "</div></li>";
        }

        // Display this post's children
        if($this->children != NULL){
            echo "<ul id='".$this->post_id."'>\r\n";
            for($i = 0; $i < count($this->children) ; $i++){
                $this->children[$i]->display_posts();
            }

            echo "</ul>\r\n";
        }
        else{


        }
        // Add a reply button to add new children 

        if($this->post_text != NULL){
            $num_children = $this->parent->num_children(); 

            if ($this->parent->children[$num_children-1] == $this){ // We are not the root node
                echo "<li>";
                echo "<button type='button' class='reply_current_branch'>Continue conversation</button>";
                echo "</li>";
            }
        }
    }

    public function num_children(){
        return count($this->children);
    }

}


function delete_post($post_id){

    $db = connect_db();

    $post_id_int = intval($post_id);

    $prepared = $db->prepare('update posts set deleted = true where post_id = ?');
    $prepared->bind_param('i',$post_id_int);   
    $prepared->execute();

    $prepared->close();
    $db->close(); 

} 
?>
