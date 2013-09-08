// 1- open the database connection

require "conection.php";

<?php

// using url paramters to build custom query (if needed)
// as you may wanna execute a query like select * from articles where c_id = $cid 
// where c_id is the categoty id belongs to this article

$cid = (int)@$_GET['c_id'];

// setting the configuration associative array

    $config = array(
	  "sql" => "SELECT * FROM articles2 ORDER BY id ASC",  // define your sql
	  "perPage" =>  5,  //define results number per page
	  "numLinks" => 5,  //define how many links should appear in the navigation 
	  "urlParams"=>"c_id=$cid",  //passing additional paramters for the url and for the sql 
	  "buttonsText" =>array(     // customizing the buttons of the navigation
			 "first"    => "<i class='icon-double-angle-left'></i> ",
			 "last"     => "<i class='icon-double-angle-right'></i>",
			 "next"     => "<i class='icon-angle-right'></i>",
             "previous"  => "<i class='icon-angle-left'></i>"
	                     )
	 "showStatus":false,  //enabling or disabling showing the current page number eg: page 1 of 30
	 "showJumpToPage":false  //enabling or disabling jumping to page option
	);
	
   $p = new Pagination($config); // instaniating the class passing the configuration array
   $obs = $p->getObjects();     //getting results objects  array
 
   foreach($obs as $o):   // looping through the results obejcts array
   ?>
  <h3><?=$o->title?></h3>
  <p><?=$o->details?></p>
  <hr/>
 <?php endforeach;?>
 <?php echo $p->nav(); ?>  // displaying the navigation 
