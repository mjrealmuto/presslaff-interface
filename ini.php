<?php
	
	$referer = $_SERVER['HTTP_REFERER'];

	$paths 	= explode("/", $_SERVER['PHP_SELF']);
	
	array_pop( $paths );
	
	$savepath = $_SERVER['DOCUMENT_ROOT'] . implode("/", $paths ) . "/assets/presslaff.ini";
	
	if( $_SERVER['REQUEST_METHOD'] == "POST" )
	{
		$regUserName 		= $_POST['regUserName'];
		$regPassWord 		= $_POST['regPassWord'];
		$contestUserName 	= $_POST['contestUserName'];
		$contestPassWord	= $_POST['contestPassWord'];
		$regUrl				= $_POST['regUrl'];
		$contestUrl			= $_POST['contestUrl'];
		$stationID			= $_POST['stationID'];
		$loginSlug			= $_POST['loginSlug'];
		$contestSlug		= $_POST['contestSlug'];
		
		$iniContent = "regUserName:" . urlencode( $regUserName ) . "\n";
		$iniContent .= "regPassWord:" . urlencode( $regPassWord ) . "\n";
		$iniContent .= "contestUserName:" . urlencode( $contestUserName ) . "\n";
		$iniContent .= "contestPassWord:" . urlencode( $contestPassWord ) . "\n";
		$iniContent .= "regUrl:" . urlencode( $regUrl ) . "\n";
		$iniContent .= "contestUrl:" . urlencode( $contestUrl ) . "\n";
		$iniContent .= "stationid:" . $stationID . "\n";
		$iniContent .= "loginSlug:" . $loginSlug . "\n";
		$iniContent .= "contestSlug:" . $contestSlug;
		
		if( file_exists( $savepath ) )
		{
			unlink( $savepath );
		}
		
		echo $savepath;
		
		$fh = fopen( $savepath, "w+");
		
		if( fputs($fh, $iniContent) )
		{
			echo "rue";
			
		}
		else
		{
			echo "false;";
		}
		
		fclose( $fh );
		
		//header("Location: " . $referer );	
	}
	else
	{
		echo "no dice, man!";
	}
	
	