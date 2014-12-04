<?php

$contestID = "";
if( isset( $_GET['contestid'] ) )
{
	$contestID =  $_GET['contestid'];
}

$slug = basename( get_permalink( ) );

if( $contestID == "" )
{
	$content .="<div id='actions'>
		<table style='width:auto;'>
			<tr><td id='edit'>Edit Information</td><td id='logout'>Logout</td></tr>
		</table>
		</div>";
	$content .="<div id='presslaff_contests'></div>";
}
else
{
	$content .="<p><a href='" . $this->contestSlugPrivate . "'>Back to Contests</a></p>";
	$content .="<div id='presslaff_contest_" . $contestID . "'></div>";
}
