create database if not exists mosteit_forum;
  
use  mosteit_forum;
  
create table if not exists threads
(
	thread_id int primary key not null auto_increment,
	thread_title varchar(60) not null,
    first_post_id int not null, /* Used to build the post tree */
	date_posted datetime,
	username varchar(60)
);

create table if not exists posts
(
	post_id int primary key not null auto_increment,
	username varchar(60), 
	parent_id int,
	post_text text,        
	date_posted datetime not null,
    thread_id int not null,    /* The thread that this post belongs to */
    deleted bool default false /* Used so that moderators can delete offensive posts */
);
  
grant select, insert, update, delete
on mosteit_forum.*
to mosteit_forum@localhost identified by "password";
