<?php


$output .= "
<form name='presslaffLogin' method='post' action='/wp-admin/admin-ajax.php' >
<input type='hidden' name='action' value='login' />
Enter E-mail Address:<br />
<input type='text' size='45' placeholder='Email Address' name='emailaddress' />
<br />
<p>
<input type='submit' name='login' value='Login' id='presslaffsubmit' />
</p>
<div id='transmit_message' ></div>

<div id='action'>Not a member? <a href='javascript: signup( );'>Click here</a> to sign up!</div>

</form>";?>


<?php $questions = $this->presslaffObj->getRegistrationQuestions( ); ?>

<?php
	
$output .= "
<div id='signupModalTmp' style=\"display:none\";>
	<span id='modal-close' class='close'></span>
	<div id='register_msg'></div>
	<form name='register' action='/wp-admin/admin-ajax.php' method='post'>
	<input type='hidden' name='action' value='createaccount' />
	
";?>
<?php
	
	foreach( $questions["questions"] as $question )
	{
		if( trim( $question["QuestionType"] ) != "Hidden Text" )
		{
			$output .="<label for='question_" . $question['QuestionNumber'] . "'>";
			if( (boolean)$question["Required"] )
			{
				$output .=$question["QuestionText"] . "*";	
				$output .="<input type='hidden' name='question_" . $question["QuestionNumber"] . "_required' value='1' />";
			}
			else
			{
				$output .=$question["QuestionText"];
				$output .="<input type='hidden' name='question_" . $question["QuestionNumber"] . "_required' value='0' />";
			}
			$output .="</label><br />";
		}
		
		switch( $question["QuestionType"] )
		{
			case "Text":
				if( trim( strtolower($question["QuestionType"] ) ) == "password" )
				{
					$output .="<input type='password' name='question_" . $question['QuestionNumber'] . "' size='40'/>";
				}
				else
				{
					$output .="<input type='text' name='question_" . $question['QuestionNumber'] . "' size='40'/>";	
				}
				$output .="<br />";
			break;
			case "Multiple Choice":
				
				$output .="<select name='question_" . $question['QuestionNumber'] . "' >";
				$output .="<option value=''>--Pick One--</option>";
				foreach( $question["choices"] as $choice )
				{
					$output .="<option value='" . $choice . "'>" . $choice . "</option>";
				}
				$output .="</select>";
				$output .="<br />";
			break;
			
			
		}
		$output .="<input type='hidden' name='nodes[]' value='" . $question["QuestionNumber"] . "' />";
	}
	$output .="<p>* Denotes Required Field</p>";
	$output .="<input type='submit' name='sub1' value='Sign Up!' />";
	$output .="<br />";


?>

<?php
	
	$output .= "
	</form>
	
</div>";
?>
