<?php

$contestID = "";
if( isset( $_GET['contestid'] ) )
{
	$contestID =  $_GET['contestid'];
}

$slug = basename( get_permalink( ) );



if( $contestID == "" )
{
	$content .="<div id='presslaff_contests'></div>";
}
else
{
	$content .="<p><a href='" . $this->contestSlugPublic . "'>Back to Contests</a></p>";
	$content .="<div id='presslaff_contest_" . $contestID . "'></div>";
}
