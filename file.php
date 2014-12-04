<?php
	
	
	$path = "assets";
	
	$content = "L-U-V Madonna";
	
	if( ! $fh = fopen($path . "/test.txt", "w+") )
	{
		print_r( error_get_last( ) );
	}
	
	fwrite( $fh, $content );
	
	fclose( $fh );
	


	