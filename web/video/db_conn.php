<?php 



	$other=1;
	include('../../nab/common_files/down/goifup_denyifdown.php');

	if($upordown==1)
	{die("db temporarily down for maintenance, come back in a few mintues");}

	if(!@$conn = mysql_connect("localhost","root","gigangraine"))
	{die("db connect fail");}


	mysql_select_db("movies",$conn);

?>
