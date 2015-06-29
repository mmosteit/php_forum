<?php

require_once('db.php');

// This class reads a conversation tree out of the database and displays it
// When the user goes to view_thread.php
class post_tree{

    protected $post_id;          // The id of the post.
    protected $post_text;        // The message text
    protected $username;         // The author of the message
    protected $date_posted;      // The time and date posted
    protected $children = NULL;  // All children of this post
    protected $parent_id;        // The parent_id of this post as stored in the sql table
    protected $deleted;          // Has this post been deleted by the moderator?
    protected $thread_id;        // What thread does this post belong to?
    protected $parent_node;      // The parent one level up in this tree.
    protected $int_post_id;
    
    public function post_tree($db, $new_post_id, $parent_node)
    {


        $this->post_id = $new_post_id;
        $post_id = $new_post_id;
        $this->parent_node = $parent_node;

        // Get the post content
        $querystring = 'select post_text, username, date_posted, parent_id, deleted, thread_id from posts where post_id = ?'; //$this->post_id; 
        $statement = $db->prepare($querystring);
        
        $this->int_post_id = intval($post_id);
        $statement->bind_param("i",$this->int_post_id);
        $statement->execute(); 
 
        $statement->bind_result(
            $this->post_text,
            $this->username,
            $this->date_posted,
            $this->parent_id,
            $this->deleted,
            $this->thread_id   
            );
 
        if(!($statement->fetch())){
            throw new Exception("<p>This thread has been deleted</p>");	
        }
    
        $statement->close();

        // Get the content any children.
        $childquerystring = 'select post_id from posts where parent_id = ? order by date_posted asc';
        $statement = $db->prepare($childquerystring);
 
        $statement->bind_param("i",$this->int_post_id);
        if( ! $statement->execute())
        {
            echo "Houston, we have a problem";
        }
        
        
        $statement->bind_result($child_id);
        $result = $statement->fetch();
    
       
        
        // Add any children 
        if($result == true){
    
            $children_id = array();
             array_push($children_id, $child_id);  
            
             while($statement->fetch()){ 
                array_push($children_id, $child_id);  
              }
            $statement->close();
        
            $this->children = array(); 
            foreach($children_id as $id)
            {
                array_push($this->children, new post_tree($db, $id, $this)); 
              
            }
        }
        else
        {
            $statement->close();
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
            $num_children = $this->parent_node->num_children(); 

            if ($this->parent_node->children[$num_children-1] == $this){ // We are not the root node
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
