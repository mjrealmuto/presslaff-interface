<?php
libxml_use_internal_errors(true);

class Presslaff{

	private $stationID			= "";

	//private $regUserName 		= "WTmx";
	private $regUserName 		= "";
	
	//private $regPassWord 		= "w7mz1300";
	private $regPassWord 		= "";
	
	//private $contestUserName 	= "Bonneville";
	private $contestUserName 	= "";
	
	//private $contestPassWord 	= "156@bei$";
	private $contestPassWord 	= "";
	
	//private $regURL 			= "https://www.1.dat-e-baseonline.com/admin/admin_net/corp_api/pircorpwebservice.asmx";
	private $regURL 			= "https://www.1.dat-e-baseonline.com/admin/admin_net/corp_api/pircorpwebservice.asmx";
	
	//private $contestURL			= "https://www.1.dat-e-baseonline.com/contestingapi/service.asmx";
	private $contestURL			= "https://www.1.dat-e-baseonline.com/contestingapi/service.asmx";
	
	private $soapHeaders		= array( );
	
	function __construct( $r_UName, $r_PWord, $c_UName, $c_PWord, $r_Url, $c_Url, $sID )
	{
		//$this->regSoap = new soap_transport_http( $this->regURL, array( ), true);
		
		$this->regUserName 		= $r_UName;
		$this->regPassWord  	= $r_PWord;
		$this->contestUserName 	= $c_UName;
		$this->contestPassWord  = $c_PWord;
		$this->regURL			= $r_Url;
		$this->contestUrl		= $c_Url;
		$this->stationID 		= $sID;
		
		$this->headers = array( 
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"CacheControl: no-cache",
			"Pragma: no-cache"
		);
	}
	
	/*
	 * Function Name: getSOAPStartReg( )
	 * Arguments: n/a
	 * Function Description: Returns the string for starting of the SOAP call.
	 * Pre-Conditions: Start of soap call is not created
	 * Post-Conditions: Start of soap call is created
	 */
	private function getSOAPStartReg( )
	{
		$data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$data .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
		$data .= "<soap:Header>\n";
		$data .= "<AuthenticationHeader xmlns=\"http://tempuri.org/\">\n";
		//username & password defined in presslaff_config.php
		$data .= "<UserName>" . $this->regUserName . "</UserName>\n";
		$data .= "<Password>" . $this->regPassWord . "</Password>\n";
		$data .= "</AuthenticationHeader>\n";
		$data .= "</soap:Header>\n";
		$data .= "<soap:Body>\n";
		
		return $data;
		
	}
	
	/*
	 * Function Name: getSOAPStartContest( )
	 * Arguments: n/a
	 * Function Description: Returns the string for starting SOAP call of the Contest System
	 * Pre-conditions: Start of Contest SOAP Call is not created
	 * Post-conditions: Start of the Contest SOAP Call is created 
	 */
	private function getSOAPStartContest( )
	{
		$data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$data .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
		$data .= "<soap:Header>\n";
		$data .= "<AuthenticationHeader xmlns=\"http://tempuri.org/\">\n";
		//username & password defined in presslaff_config.php
		$data .= "<UserName>" . $this->contestUserName . "</UserName>\n";
		$data .= "<Password>" . $this->contestPassWord . "</Password>\n";
		$data .= "</AuthenticationHeader>\n";
		$data .= "</soap:Header>\n";
		$data .= "<soap:Body>\n";
		
		return $data;
	}
	
	/*
	 * Function Name: getSOAPEnd( )
	 * Arguments: n/a
	 * Function Description: Returns a generic end to the SOAP String
	 * Pre-conditions: End of SOAP call is not created
	 * Post-conditions: End of SOAP call is created
	 */
	private function getSOAPEnd( )
	{
		$data = "</soap:Body>\n";
		$data .= "</soap:Envelope>\n";
		
		return $data;
	}
	
	/*
	 * Function Name: getRegistrationQuestions
	 * Arguments: n/a
	 * Function Description: Returns the Registration for the specific stations.  A SOAP Call is made to the normal
	 * presslaff system to retrieve the system registration questions.  The XML will be returned and send through
	 * the function 'parseXML' and returned to the calling program.
	 * Pre-conditions: No registration questions will be displayed
	 * Post-conditions: The registration questions will be displayed for the give station.
	 */
	protected function getRegistrationQuestions( ){
				
		$soap = "";
		
		$this->resetHeaders( );
		
		$this->headers[] = "SOAPAction: http://tempuri.org/getRegistrationQuestions";
		
		//call getSOAPStart function and return beginning of soap call.
		$soap = $this->getSOAPStartReg( );
		
		//start getRegistrationQuestions parameters
		$soap .= "<getRegistrationQuestions xmlns=\"http://tempuri.org/\">\n";
		
		//'stationID is defined in presslaff_config.php
		$soap .= "<stationID>" . $this->stationID . "</stationID>\n";
		
		//End Parameters
		$soap .= "</getRegistrationQuestions>\n";
		
		//call getSOAPEnd function and return end of soap call.
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		//$soapResp = $GLOBALS['soapObj']->send($soap);
		//$fh = fopen("/domain/sharedcode/presslaffRegistrationResponse.txt","w+");
		//fputs($fh,$soapResp);
		//fclose($fh);
		/*
		 *The soap nodes have need to have specific characters replaced inorder to be parsed 
		 * The piece of code below will parse out the unwanted characters within the xml/soap nodes
		 */
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		//Load simple xml from string
		$xml = simplexml_load_string($xmlString);
		//Call parseXML and return delimited string of responses.
		//echo $this->parseXML($xml,"getRegistrationQuestions");
		
		return $this->parseXML($xml,"getRegistrationQuestions");
	}
	
	/*
	 * Function Name: getAccountIDbyEmail
	 * Arguments: email - string
	 * Function Description: getAccountIDbyEmail takes in an email address and returns a return code 
	 * telling whether or not the retrieval was successfull or not as well as the ID (If it was successful).
	 * Pre-conditions: Presslaff ID in the main program will be empty
	 * Post-conditions: Presslaff ID in the main program will either still be empty or populated depending 
	 * on whether or not it was found.  A return code 100 signifies the email address was found and the ID is  
	 * returned.  A return code of 200 indicates that the email was not found.*/
	protected function getAccountIDbyEmail( $email )
	{
	
		
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/getAccountIDbyEmail";
		
		$soap = $this->getSOAPStartReg( );
		
		$soap .= "<getAccountIDbyEmail xmlns=\"http://tempuri.org/\">\n";
		
		$soap .= "<email>" . $email . "</email>\n";
		
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		
		$soap .= "</getAccountIDbyEmail>\n";
		
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"getAccountIDbyEmail");
	}
	
	 /*
	 * Function Name: createAccount
	 * Arguments: Associative array.  This will have fifty indexes all called 'Data_n' where 'n' is 1-50
	 * Function Description: createAccount will take in an Associative array with fifty indexes and loop 
	 * through extracting all the data and placing it into an XML format--> <data_n>[value]</data_n>
	 * Pre-conditions: User will not be created.
	 * Post-conditions: If successfull, user will be created. Following return codes signify what 
	 * action was taken:
	 * 100 - Account Created Successfully
	 * 200 - Email address is not in valid format
	 * 205 - Date of birth Supplied is not a valid date -- > this will, more than likely never be used.  
	 * The date is formatted in main program with drop downs for month, day and year.  The values are then
	 * formatted properly upon submittion. Also, there is a validation on the front end.
	 * 400 - Account already exists
	 * 500 - Permission Denied
	 * 601 - Missing Required Data
	 * 700 - Account Created - error occured while saving to lists
	 * 900 - System Error 
	 */
	protected function createAccountComplete( $values )
	{
		$nodeCnt = 1;
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/createAccountComplete";
		
		$soap = $this->getSOAPStartReg( );
		
		$soap .= "<createAccountComplete xmlns=\"http://tempuri.org/\">\n";
		
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		$soap .= "<listID></listID>\n";
		for( $i=1 ; $i <= 50 ; $i++ )
		{
			$nodevalue = "";
			foreach( $values as $key=>$value )
			{
				if($key == "Data_" . $i)
				{
					$nodevalue = $value;
				}
			}
			$soap .= "<data_" . $i . ">" . $nodevalue . "</data_" . $i . ">\n";
		}
		$soap .= "</createAccountComplete>\n";
		
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"createAccountComplete");
	}
	
	protected function createAccount( $fName, $lName, $email, $zip = "", $dob = "", $gender = "" )
	{
		$nodeCnt 	= 1;
		$soap 		= "";
		
		$this->resetHeaders( );
		
		$this->headers[] = "SOAPAction: http://tempuri.org/createAccount";
		
		$soap .= $this->getSOAPStartReg( );
		
		$soap .= "<createAccount xmlns=\"http://tempuri.org/\">\n";
		
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		$soap .= "<listID></listID>\n";
		$soap .= "<email>" . $email . "</email>\n";
		$soap .= "<firstName>" . $fName . "</firstName>\n";
		$soap .= "<lastName>" . $lName . "</lastName>\n";
		$soap .= "<zipCode>" . $zip . "</zipCode>\n";
		$soap .= "<dateOfBirth>" . $dob . "</dateOfBirth>\n";
		$soap .= "<gender>" . $gender . "</gender>\n";
		$soap .= "<optIn>FALSE</optIn>\n";
		
		for( $i=1 ; $i <= 50 ; $i++ )
		{
			$soap .= "<data_" . $i . "></data_" . $i . ">\n";
		}
		
		$soap .= "</createAccount>\n";
		$soap .= $this->getSOAPEnd( );
			
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers );
		
		//call server and return soap response
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml, "createAccount");
	}
	
	/*
	 * Function Name: getAccountbyID
	 * Arguments: presslaffID
	 * Function Description:  getAccountbyID takes in the presslaffID and returns all values (Data nodes 1 - 50) 
	 * related to this account
	 * Pre-conditions: Account information will be absent
	 * Post-conditions: Account information will be retrieved. 
	 * Following return codes signify what action was taken:
	 * 100 - Subsriber Found
	 * 200 - No Subscriber Found
	 * 300 - Missing Arguments
	 * 500 - Permission Denied
	 * 900 - System Error.
	 */
	protected function getAccountbyID( $pID )
	{
		
		$this->headers[] = "SOAPAction: http://tempuri.org/getAccountInfo";
	
		$soap = "";
		$soap = $this->getSOAPStartReg( );
		$soap .= "<getAccountInfo xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<accountID>" . $pID . "</accountID>\n";
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		$soap .= "</getAccountInfo>\n";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"getAccountbyID");
		
	}
	
	/*
	 * Function Name: modifyAccount
	 * Arguments: Associative array ->  This will have fifty indexes all called 'Data_n' where 'n' is 1-50 and Presslaff ID
	 * Function Description: modifyAccount will take in the Preslaff ID an Associative array with fifty indexes and loop 
	 * through extracting all the data and placing it into an XML format--> <data_n>[value]</data_n>
	 * Pre-conditions: User will not be modified.
	 * Post-conditions: If successfull, user will be modified. Following return codes signify what 
	 * action was taken:
	 * 100 - Account Modified Successfully
	 * 200 - Email address is not in valid format
	 * 205 - Date of birth Supplied is not a valid date -- > this will, more than likely never be used.  
	 * The date is formatted in main program with drop downs for month, day and year.  The values are then
	 * formatted properly upon submittion. Also, there is a validation on the front end.
	 * 400 - Field Settings not set up on station
	 * 700 - Account not found
	 * 900 - System Error 
	 */
	protected function modifyAccount($values, $pID)
	{
		$nodeCnt = 1;
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/modifyAccount";
		
		$soap = $this->getSOAPStartReg( );
		$soap .= "<modifyAccount xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		$soap .= "<accountID>" . $pID . "</accountID>\n";
		
		for( $i = 1 ; $i < 51 ; $i++ )
		{
			$nodevalue = "";
			foreach($values as $key=>$value)
			{
				if($key == "Data_" . $i)
				{
					$nodevalue = $value;
				}
			}
			$soap .= "<data_" . $i . ">" . $nodevalue . "</data_" . $i . ">\n";
		}
		$soap .= "</modifyAccount>\n";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->regURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"modifyAccount");
	}
	
	/*
	 * Function Name: getContest
	 * Arguments: contest ID and Presslaff ID
	 * Function Description: Grabs an individual contest Questions.  Contest is selected by contest ID.  The call will return a setup of
	 * the specified Contest.   How it is extracted is available above in 'parseXML'
	 * Pre-conditions: Individual ($cID) contest will not be available
	 * Post-conditions: Individual ($cID) contest will be available
	 */
	protected function getContest( $cID, $pID)
	{
		$soap = ""; 
		
		$this->resetHeaders( );
		
		$this->headers[] = "SOAPAction: http://tempuri.org/GetContestInfo";
		
		$soap = $this->getSOAPStartContest( );
		$soap .= "<GetContestInfo xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<StationID>" . $this->stationID . "</StationID>";
		$soap .= "<SubscriberID>" . $pID . "</SubscriberID>";
		$soap .= "<ContestID>" . $cID . "</ContestID>";
		$soap .= "</GetContestInfo>";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );

		$ch = $this->getCurlObj( $this->contestURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xml = simplexml_load_string($xmlString);
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		$xmlString = str_replace("&#x0;","",$xmlString);
		
		//$xmlString = str_replace("&eacute;","&#201;", $xmlString);
		$xml = simplexml_load_string($xmlString);
		

		
		return $this->parseXML($xml,"getContestInfo");
	
	}
	
	/*
	 * Function Name: getContestsforSubscriber
	 * Arguments: subscriber (preslaff) id
	 * Function Description: getCOntestforSubscriber is mainly used to list out the contest that are eligible for a specific user.
	 * This function mainly takes over the place for 'getContests()'.  What this function does differently is gives a message on 
	 * whether or not the subscriber (user) has entered the contest and, if so, when.
	 * Pre-Conditions: The contests will not be listed for a given subscriber (user)
	 * Post-Condition: The contests will be listed for a given subscriber (user)
	 */
	protected function getContestsforSubscriber($subscriberID){
		
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/getContestsforSubscriber";
		
		$soap = $this->getSOAPStartContest( );
		$soap .= "<getContestsforSubscriber xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<StationId>" . $this->stationID . "</StationId>\n";//station ID
		$soap .= "<SubscriberID>" . $subscriberID . "</SubscriberID>\n";//subscriber ID
		$soap .= "</getContestsforSubscriber>\n";
		$soap .= $this->getSOAPEnd( );
	
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->contestURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		$xmlString = str_replace("&#x0;","",$xmlString);
		
		//$xmlString = str_replace("&eacute;","&#201;", $xmlString);
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"getContestsforSubscriber");
			
	}
	
	/*
	 * Function Name: getAllContests
	 * Arguments: n/a
	 * Function Description: getAllContests returns all available contests for a given statoin.  For the contest to be available,
	 * The contest must have a logo and as well as timed correctly.
	 * Pre-conditions: Contests will not be shown.
	 * Post-conditions: Contests will be visible.
	 */
	protected function getAllContests( )
	{
		
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/GetContests";
		
		$soap = $this->getSOAPStartContest( );
		$soap .= "<GetContests xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<StationId>" . $this->stationID . "</StationId>\n"; 
		$soap .= "</GetContests>\n";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		
		$ch = $this->getCurlObj( $this->contestURL, $soap, $this->headers);
		
		//call server and return soap response
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"getContests");
		
	}

	
	/*
	 * Function Name: getAccountIDByAnyField
	 * Arguments: value of the node being retrieved
	 * Function Description: This function is primarily used with logging in via Facebook.  If the user is logging in with Facebook and
	 * the Facebook email used is different than the log in email, this function will be called and check the alternate Email being used.
	 * Currently, the alternate email field is being stored in Data Node 26.  For future notes, two arguments should be passed in: the value
	 * and the number should be passed in. The de facto number right now, though, is 26.
	 * Pre-Condition: Subscriber will not be logged in.
	 * Post-Condition: If email (or value) exists within the given node, then the code will be returned for the user to be logged in.
	 */
	protected function getAccountIDByAnyField($value,$fieldnum)
	{
		$soap = "";
		
		$this->headers[] = "SOAPAction: http://tempuri.org/getSubscriberIDByAnyField";
		
		$soap = $this->getSOAPStartContest( );
		$soap .= "<getSubscriberIDByAnyField xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<fieldNum>" . $fieldnum . "</fieldNum>\n";
		$soap .= "<fieldData>" . $value . "</fieldData>\n";
		$soap .= "<showID>" . $this->stationID . "</showID>\n";
		$soap .= "</getSubscriberIDByAnyField>\n";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->contestURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		$xmlString = str_replace("&#x0;","",$xmlString);
		//$xmlString = str_replace("&eacute;","&#201;", $xmlString);
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"GetAccountIDByAnyField");
		
	}
	
	/*Function Name: putContestData
	 * Arguments: presslaffID, $contestID, $xmlResponse
	 * Function Description: This function will upload the information that user ($pID) has entered for a
	 * specific contest ($cID).  The Responses are written out in XML format and passed in the string $xmlResponses
	 * Pre-Condition: The user will not be entered in a given contest
	 * Post-Condition: If successful, the user will be entered in the contest. An Error message will be returned if entry
	 * is unsuccessful.
	 * 
	 */
	protected function putContestData($pID, $cID, $xmlResponses)
	{
		$soap = "";
		
		$this->resetHeaders( );
		
		$this->headers[] = "SOAPAction: http://tempuri.org/putContestData";
		
		$soap = $this->getSOAPStartContest( );
		$soap .= "<putContestData xmlns=\"http://tempuri.org/\">\n";
		$soap .= "<StationID>" . $this->stationID . "</StationID>\n";
		$soap .= "<SubscriberID>" . $pID . "</SubscriberID>\n";
		$soap .= "<ContestID>" . $cID . "</ContestID>\n";
		
		$carrats = array("<",">");
		$carrat_replace = array("&lt;","&gt;");
		$xmlfields = str_replace($carrats, $carrat_replace, $xmlResponses);
		
		$soap .= "<xmlString>" . $xmlfields . "</xmlString>\n";
		$soap .= "</putContestData>\n";
		$soap .= $this->getSOAPEnd( );
		
		$this->headers[] = "Content-length: " . strlen( $soap );
		
		$ch = $this->getCurlObj( $this->contestURL, $soap, $this->headers);
		
		//call server and return soap response	
		$soapResp = curl_exec( $ch );
		
		curl_close( $ch );
		
		$xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResp);
		$xmlString = str_replace("&#x0;","",$xmlString);
		
		//$xmlString = str_replace("&eacute;","&#201;", $xmlString);
		$xml = simplexml_load_string($xmlString);
		
		return $this->parseXML($xml,"putContestData");
	}
	
	/*Function Name: getContestImage
	 * Arguments: 
	 * 
	 * 
	 */
	protected function getContestImage( $cID )
	{
		$contests = $this->getAllContests( );
		$contests = explode("^_^",$contests);
		array_shift($contests);
		$contestimage = "";
		foreach($contests as $contest)
		{
			list($id,$desc,$img,$name,$order,$type) = explode("|",$contest);
			if($id == $cID)
			{
				$contestimage = $img;
			}
		}
		return $contestimage;
	}
	
	/*
	 *Function Name: parseXML 
	 * Arguments: simple xml object (a given node),orginiating function,(optional pass agrument)
	 * Function Description:  parseXML is used to parse through the returning xml from the Presslaff Server.
	 * The function is a recursive function.  It will view the current node that is being passed into the function. 
	 * If it determines that this is parent node and has children it will call itself again with the child nodes.
	 * It will read through the child nodes, collecting information.  When the xml has been read through it will pass the 
	 * string back to the calling function and that function will send the information back to the calling program.
	 * Pre-conditions: The information in the xml from the SOAP call to the Presslaff server will be unprocessed and in raw
	 * XML format.
	 * Post-condition: The XML will be read and all relavant information will be extracted and sent back to the calling 
	 * function. 
	 */
	private function parseXML($x, $function, &$fields = array( ) )
	{
		$num = "";
		$txt = "";
		$typ = "";
		$contentID = "";
		$currentID = "";
		$rv;
		$rc	 = 0;
		if( is_object( $x ) )
		{
			$kids = count($x->children( ));//count how many children the incoming xml node has	
		}
		else
		{
			echo $x;
			echo "not an object";
			exit;
		}
		$choicecount = 0;
		$childcount = 0;
		/*Loop through the children of the current node.  The first node to be passed in is the 
		 * root node which will always have children.*/
		foreach($x->children( ) as $child)
		{
			//echo $child->getName( ). ": " . $child . "<br />";
			//Check if the current child has children
			if( count( $child->children( ) ) > 0 )
			{
				/*getContestInfo is a function that needs to have information extracted from the 
				 * attributes of the XML nodes.  This section is a place to extract attributes*/
				if( $function == "getContestInfo" )
				{
					
				
					//Check if the child name is field
					if($child->getName( ) == "field")
					{
						$fieldIndex = sizeof($fields["fields"]);
						
						//Loop through and extract needed information
						foreach($child->attributes( ) as $f => $v)
						{
							$fields["fields"][$fieldIndex][$f] = (string)$v;
						}
					}
				}
				//Once any necessary attributes are extracted, call parseXML again pass  the next node.

				$this->parseXML($child, $function, $fields );
				
			//If the current node has no children then extract information.
			}
			else
			{
				/*Below are all the conditional statements for all the passing functions.
				 * The information will be added to the $rv variable and keep accumulating until
				 * the xml file is finished processing.  The information will be parsed and returned.*/
				if( $function == "getRegistrationQuestions" )
				{ 
					if( ! isset( $fields["questions"] ) )
					{
						$fields["questions"] = array( );
					}
					
					// condition for getRegistrationQuestions
					//Node_1 through Node_50 will be returned.
					//getRegistration question node values will be seperated by a ';' and each 
					//question will be seperated by '*_*' 
					if( $child->getName( ) == "QuestionNumber" )
					{
						$currentIndex 	= sizeof( $fields["questions"] );
						
						$fields["questions"][$currentIndex]["QuestionNumber"] = (string)str_replace(":","",$child);
						//$rv .= str_replace(":","",$child);
					}
					elseif( $child->getName( ) == "QuestionText" )
					{
						$currentIndex = sizeof( $fields["questions"] ) - 1;
						
						$fields["questions"][$currentIndex]["QuestionText"] = (string)str_replace(":","",$child);
						//$rv .= ";" . str_replace(":","",$child);
					}
					elseif( $child->getName( ) == "QuestionType" )
					{
						$currentIndex = sizeof( $fields["questions"] ) - 1;
						
						$fields["questions"][$currentIndex]["QuestionType"] = (string)str_replace(":","",$child);
						/*if( strtolower( $child ) == "multiple choice" || strtolower( $child ) == "radio buttons")
						{
							$rv .= ";" . str_replace(":","",$child);
						}
						else
						{
							$rv .= ";" . str_replace(":","",$child);
						}*/
					}
					elseif( $child->getName( ) == "getRegistrationQuestionsCode" )
					{
						$currentIndex = sizeof( $fields["questions"] ) - 1;
						
						$fields["status"] = (string)str_replace(":", "", $child );
					}
					elseif(strtolower($child->getName( )) == "choices")
					{
						$currentIndex = sizeof( $fields["questions"] ) - 1;
						
						if( ! is_array( $field["fields"][$fieldIndex]["values"] ) )
						{
							$field["fields"][$fieldIndex]["values"] = array( );
							$valueIndex = 0;
						}
						else
						{
							$valueIndex = sizeof( $field["fields"][$fieldIndex]["values"] ) + 1;
						}

						if( ! is_array( $fields["questions"][$currentIndex]["choices"] ) )
						{
							$fields["questions"][$currentIndex]["choices"] = array( );
							
						}
						
						$fields["questions"][$currentIndex]["choices"][] = (string)$child;
					}
					elseif( $child->getName( ) == "Required" )
					{
						$fields["questions"][$currentIndex]["Required"] = (string)str_replace(":","",$child);
						//$rv .= ";" . str_replace(":","",$child) . "|";
					}
				}
				elseif( $function == "getAccountIDbyEmail") // condition for getAccountIDbyEmail
				{ 
					//Return values are: Result code and account ID
					//Values will be seperated by '|'
					if( $child->getName( ) == "getAccountIDbyEmailResultCode" )
					{
						$fields["status"] = str_replace(":","",$child);					
						//$rv .= str_replace(":","",$child);
					}
					elseif($child->getName( ) == "accountID")
					{
						$fields["accountID"] = str_replace(":","",$child);
						//$rv .= "|" . str_replace(":","",$child);
					}
				}
				elseif($function == "createAccountComplete" || $function == "createAccount") //condition for createAccountComplete
				{
					if($child->getName( ) == "createAccountCompleteResultCode" || $child->getName( ) == "SubscribeResultCode")
					{
						$fields["status"] = (string)$child;
					}
					elseif($child->getName( ) == "email")
					{
						$fields["email"] = (string)str_replace(":","",$child);
					}
					elseif( $child->getName( ) == "createAccountCompleteResultDescr" )
					{
						$fields["msg"] = (string)$child;
					}
					elseif( $child->getName( ) == "accountID" )
					{
						$fields["accountid"] = (string)$child;
					}
				}
				elseif( $function == "modifyAccount") //condition for modifyAccount
				{
					if( $child->getName( ) == "modifyAccountResultCode" )
					{
						//Return code on whether or not the account is modified
						//$rv = str_replace(":","",$child);
						$fields["resultcode"] = str_replace(":","",$child);
					}
				}
				elseif($function == "getAccountbyID") //condition for getAccountbyID
				{
					if( strtolower( $child->getName( ) ) == "getaccountinforesultcode")
					{
						$fields["status"] = str_replace(":","",$child);
					}
				
					if( preg_match("/^Data_/",$child->getName( ) ) )
					{
						//Return all the information about the user logging in.
						//Fields will be seperated by '|' and Field/Value will be seperated by ':'
						
						$fields[$child->getName( )] =  str_replace(":","",$child);
					}
				}
				/*
				elseif( $function == "getContests")//condition for getContests
				{
					//$fields[$index][$child->getName( )] = $child;
					
					$newrecord = false;
					if( ( trim( $child->getName( ) ) == "ContestID") || sizeof( $fields ) == 0 )
					{
						$newrecord = true;
					}
					
					$fieldsIndex = $newrecord ? sizeof( $fields ) : (sizeof( $fields) - 1 );
					
					$fields[$fieldsIndex][$child->getName( )] = (string)$child;
					
					
					
				}*/
				elseif( $function == "GetAccountIDByAnyField" ) //condition for GetAccountIDByAnyField
				{
					//This is mainly used to retrieve the account id if the user is using an  email 
					//address to login via Facebook that is different than their login email.
					//Values delimited by '|'
					if( $child->getName( ) == "getSubscriberIDByAnyFieldResultCode" )
					{
						$fields["resultcode"] = $child;
					}
					elseif( $child->getName( ) == "accountID" )
					{
						$fields["accountID"] = str_replace(":","",$child);
					}
				}
				elseif( $function == "getContestInfo" ) //condition for getContestsInfo
				{
					//This will retrieve the information for a particular contest
					//Values and Fields are delimited similarly to 'getRegistrationQuestions
					//->START data being delimited with '|' 
					
					if( $child->getName( ) == "label" | $child->getName( ) == "uploadlabel")
					{
						 $fieldIndex = (sizeof( $fields["fields"] ) - 1);
						 
						 $fields["fields"][$fieldIndex]["label"] = (string)$child;	 
					}
					else if( $child->getName( ) == "value" )
					{
						$fieldIndex = ( sizeof( $fields["fields"] ) - 1 );
						
						if( ! is_array( $field["fields"][$fieldIndex]["values"] ) )
						{
							$field["fields"][$fieldIndex]["values"] = array( );
							$valueIndex = 0;
						}
						else
						{
							$valueIndex = sizeof( $field["fields"][$fieldIndex]["values"] ) + 1;
						}
					
						foreach( $child->attributes( ) as $k => $v )
						{
							$fields["fields"][$fieldIndex]["values"][$valueIndex][$k] = (string)$v;	
						}
						
						
					}
					else
					{
						$fields[$child->getName( )] = (string)$child;
					}
				}
				elseif($function == "getContestByID") //Condition for getContestByID
				{
					//This will retrieve a given contest by ID but ONLY return few values.
					if( strtolower( $child->getName( ) ) == "successfailure" )//Whether not there is a success or failure upon returning a given contest
					{
						$fields["success"] = $child;
					}
					if(strtolower($child->getName( )) == "description")//Description does not return proper description
					{
						$fields["description"] = $child;
					}
					if(strtolower($child->getName( )) == "name")//Get Name of contest
					{
						$fields["name"] = $child;
					}
				}
				elseif( $function == "putContestData")//condition for 'putContestData'
				{
					//This will return whether or not uploading contest information was successful or not 
					//returns success and any errors.
					if( strtolower( $child->getName( ) ) == "success")
					{
						$fields["success"] = (string)$child;
					}
					if( $child->getName( ) == "error")
					{
						//Check to see if the 'ERROR' node has children.
						//If so, loop through and retrieve via attributes of node
						if( count( $child->attributes( ) ) > 0 )
						{
							if( ! is_array( $fields["errors"] ) )
							{
								$errorIndex = 0;
							}
							else
							{
								$errorIndex = sizeof( $fields["errors"] );	
							}
							
							foreach( $child->attributes( ) as $k => $v )
							{
								$fields["errors"][$errorIndex]["field"] = (string)$v;
							}
							$fields["errors"][$errorIndex]["value"] = (string)$child;
						}
						else
						{
							$fields["errors"] = (string)$child;
						}
					}
				}
				elseif( $function == "getContestsforSubscriber" || $function == "getContests" )//condition ofr getContests for Subscriber
				{
					//grabs contests available for a given subsciber.
					//Mainly used on for showing all contests and seeing which subscriber is eligible for 
					//a given contest.
					//contests delimited by '^_^' and values delimited by '|'
					$newrecord = false;
					if( ( trim( $child->getName( ) ) == "ContestID") || sizeof( $fields ) == 0 )
					{
						$newrecord = true;
					}
					
					$fieldsIndex = $newrecord ? sizeof( $fields ) : (sizeof( $fields) - 1 );
					
					$fields[$fieldsIndex][$child->getName( )] = (string)$child;
				}
			}
			$childcount++;
		} 
		//return $rv;

		return $fields;
	}
	
	protected function getCurlObj( $url, $xmlString, $headers )
	{
		$ch = curl_init( );
		
		$options = array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPAUTH => CURLAUTH_ANY,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $xmlString,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADERS => TRUE
		);
		
		curl_setopt_array($ch, $options);
		
		return $ch;		
		
	}
	
	protected function resetHeaders( )
	{
		$this->headers = array( 
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"CacheControl: no-cache",
			"Pragma: no-cache"
		);
	}
	
	
}