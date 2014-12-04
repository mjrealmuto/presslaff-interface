var exDays = 2;

var fieldIDs = new Array( );

(function ($) {
	"use strict";
	$(function () {
		
		var url = location.href;
		
		var urlSplit = url.split("?");
		
		var contestid = "";
		
		var accountid = "";
		
		if( supports_html5_storage( ) )
		{
			accountid = localStorage.getItem("presslaffID");
		}
		else
		{
			accountid = getCookie( "presslaffID" );
		}
		
		if( accountid === null )
		{
			accountid = "";
		}

		
		if( urlSplit[1] !== undefined )
		{
		
			var parameters = urlSplit[1].split("&");
			
			for( var p in parameters )
			{
				var argument = parameters[p].split("=");
				
				if( argument[0] == "contestid" )
				{
					contestid = argument[1];
				}
			}
		}
		
		if( contestid.length == 0 )
		{
			$.ajax({
				method: "GET",
				url: "/wp-admin/admin-ajax.php?action=getContestsPublic",
				dataType: "json",
				beforeSend: function( ){
					$("#presslaff_contests").html("Loading Contests....");
					
				},
				success: function( data )
				{
					var success = Boolean( data.shift( ) );
					
					var contests = "<br /><hr class='contest_divider'><br />";
					
					if( success )
					{
						for( var x in data )
						{
							if( data[x].Hidden.toLowerCase( ) == "false" )
							{
								
								var now = new Date( );
								
								now = now.getTime( );
								
								if( data[x].NextEligibilityDate != "" )
								{
									var ned = new Date( data[x].NextEligibilityDate );
									
									ned = ned.getTime( );
								}
								else
								{
									ned = now;
								}
								
								var contestLinkStart = "<a onclick=\"_gaq.push(['_trackEvent', 'outbound-article-int', '" + location.href + "?contestid=" + data[x].ContestID + "', '']);\" href='" + location.href + "?contestid=" + data[x].ContestID + "'>";						
								contests += "<div class='contest_container' style='border-bottom: 1px solid #DDD; padding-bottom: 10px; '>";
								contests += contestLinkStart + "<img class='alignleft' src='" + data[x].LogoURL + "' title='" + data[x].Name + "' /></a>";
								contests += "<p class='p-title'>" + contestLinkStart + data[x].Name + "</a></p>";
								contests += "<br />";
								contests += "<p>" + data[x].Description + "</p>";
								contests += contestLinkStart + "Enter Here</a>";
								contests += "<div class='clear'></div>";
								contests += "</div>";
								contests += "<hr ><br />";	
							
							}
						}
												
						$("#presslaff_contests").html( contests );
					}
				},
				error: function( x,y )
				{
					
					
				}
			});
		}
		else
		{
					
			$.ajax({
				method: "GET",
				url: "/wp-admin/admin-ajax.php?pID=" + accountid + "&cID=" + contestid + "&action=getContest",
				dataType: "json",
				beforeSend: function( )
				{
					$("#presslaff_contest_" + contestid).html("Loading Contest...");
				},
				success: function( data )
				{
					//var contest = "<h1 class='name post-title entry-title'>" + data.title + "</h1>";
					
					$(".content article > div > h1").html( data.title );
					
					$("#presslaff_contest_" + contestid).html( data.content );
					
					var fields = data.fields;
					
					var contest_out = "";
					
					contest_out += "<br />";
					contest_out +=  "\
						<div id='form_error'></div>\
						<form name='contest" + contestid + "' action='/wp-admin/admin-ajax.php' method='post'>\
						<input type='hidden' name='action' value='entercontest_public' />\
						<input type='hidden' name='contestid' value='" + contestid + "' />\
					";
					
					contest_out += "<label for='firstname'>First Name*</label><br />";
					contest_out += "<input type='text' size='35' name='firstname' value='" + data.firstname + "' />";
					contest_out += "<div id='error_fname' class='required'></div>";
					contest_out += "<br />";
					contest_out += "<label for='lastname'>Last Name*</label><br />";
					contest_out += "<input type='text' size='40' name='lastname' value='" + data.lastname + "' />";
					contest_out += "<div id='error_lname' class='required'></div>";					
					contest_out += "<br />";
					contest_out += "<label for='emailAddress'>E-Mail Address*</label><br />";
					contest_out += "<input type='text' size='45' name='email' value='" + data.email + "' />";
					contest_out += "<div id='error_email' class='required'></div>";
					contest_out += "<br />";
					
					contest_out += "<hr />";
					
					if( fields.length > 0 )
					{
						var fieldcount = 0;
					
						for( var f in fields )
						{
							fieldIDs.push( fields[f].id );
							if( fields[f].type != "" && fields[f].type != "Hidden Text" )
							{
								if( fields[f].required == "True" )
								{
									contest_out += "<label for='input_" + fields[f].id + "' >" + fields[f].label + "&nbsp;*</label><br />";
								}
								else
								{
									contest_out += "<label for='input_" + fields[f].id + "'>" + fields[f].label + "</label><br />";
								}
							}
							
							contest_out += "<input type='hidden' name='" + fields[f].id + "_required' value='" + fields[f].required.toLowerCase( ) + "' />\
								<input type='hidden' name='fieldID" + f + "' value='" + fields[f].id + "' />";
						
							
							switch( fields[f].type )
							{
								case "Radio Buttons":
									
									var values = fields[f].values;
									contest_out += "<table style='max-width: 150px;'>";
									for( var v in values )
									{
										if( values[v].selected == "true" )
										{
											contest_out += "<tr><td style='padding: 2px;'>" + values[v].actualvalue + "</td><td style='padding: 2px;'><input type='radio' name='input_" + fields[f].id + "' value='" + values[v].actualvalue + "' checked='checked' /><td></tr>";
										}
										else
										{
											contest_out += "<tr><td style='padding: 2px;'>" + values[v].actualvalue + "</td><td style='padding: 2px;'><input type='radio' name='input_" + fields[f].id + "' value='" + values[v].actualvalue + "' /></td></tr>";
										}
									}						
									contest_out += "</table>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='radio' />";
								break;
								case "Text":
									contest_out += "<input type='text' name='input_" + fields[f].id + "' size='30'/>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='text' />";
								break;
								case "Verbatim":
									contest_out += "<textarea cols='30' rows='7' name='input_" + fields[f].id + "'></textarea>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='verbatim' />";
								break;
								case "Hidden Text":
									contest_out += "<input type='hidden' name='input_" + fields[f].id + "' />";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='hidden' />";
								break;
								case "Check Boxes":
									var values = fields[f].values;
									contest_out += "<table style='max-width: 150px;'>";
									for( var v in values )
									{
										if( values[v].selected == "true" )
										{
											contest_out += "<tr><td style='padding: 2px;'>" + values[v].actualvalue + "</td><td style='padding: 2px;'><input type='checkbox' name='input_" + fields[f].id + "[]' value='" + values[v].actualvalue + "' checked='checked' /><td></tr>";
										}
										else
										{
											contest_out += "<tr><td style='padding: 2px;'>" + values[v].actualvalue + "</td><td style='padding: 2px;'><input type='checkbox' name='input_" + fields[f].id + "[]' value='" + values[v].actualvalue + "' /></td></tr>";
										}
									}						
									contest_out += "</table>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='checkbox' />";
								break;
								case "Multiple Choice":
									var values = fields[f].values;
									contest_out += "<select name='input_" + fields[f].id + "'>";
									contest_out += "<option value=''>Select an Option</option>";
									for( var v in values )
									{
										if( values[v].selected == "true" )
										{
											contest_out += "<option value='" + values[v].actualvalue + "' selected='selected' >" + values[v].actualvalue + "</option>";
										}
										else
										{
											contest_out += "<option value='" + values[v].actualvalue + "'  >" + values[v].actualvalue + "</option>";
										}
									}						
									contest_out += "</select>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='select' />";
								
								break;
								case "Multiple Selection":
									var values = fields[f].values;
									contest_out += "<select name='input_" + fields[f].id + "[]' multiple>";
									for( var v in values )
									{
										if( values[v].selected == "true" )
										{
											contest_out += "<option value='" + values[v].actualvalue + "' selected='selected' >" + values[v].actualvalue + "</option>";
										}
										else
										{
											contest_out += "<option value='" + values[v].actualvalue + "'  >" + values[v].actualvalue + "</option>";
										}
									}						
									contest_out += "</select>";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='select_multiple' />";
								break;	
								case "Upload":
									
									contest_out += "<input type='file' name='input_" + fields[f].id + "' />";
									contest_out += "<div id='error_" + fields[f].id + "' class='required'></div>";
									contest_out += "<input type='hidden' name='" + fields[f].id + "_type' value='upload' />";
								
								break;
							}
							fieldcount++;
						}
					}
					
					$("#presslaff_contest_" + contestid).append(contest_out + "\
						<br />\
						<input type='hidden' name='fieldcount' value='" + fieldcount + "' />\
						<input type='submit' name='Enter' value='Enter Contest' id='presslaffsubmit' />\
						<br />\
						<p><div class='progressbar'><div class='progress'></div></div></p>\
						</form>\
					");
					
					var options = {
						beforeSubmit: function( fD, jqF, o)
						{
							$(".required").html("").removeClass("msg_err");
							
							$("#presslaffsubmit").val("Entering Contest...");
							
							document.getElementById("presslaffsubmit").disabled = true;
							
							var errors = 0;
							
							if( $("input[name=firstname]").val( ) == "" )
							{
								errors = 1;
								$("#error_fname").html( "Please Enter your First Name.");
							}
							
							if( $("input[name=lastname]").val( ) == "" )
							{
								errors = 1;
								$("#error_lname").html( "Please Enter your Last Name." );
							}
							
							if( $("input[name=email]").val( ) == "" )
							{
								errors = 1;
								$("#error_email").html( "Please Enter your E-Mail Address" );
							}
							
							if( errors == 1 )
							{
								$("#presslaffsubmit").val("Enter Contest");

								
								document.getElementById("presslaffsubmit").disabled = false;
								
								return false;
								
							}
							else
							{
								$(".progressbar").show( );
							}
			
						},
						uploadProgress: function(events, position, total, percentComplete)
						{ 
							$(".progress").css( "width", percentComplete + "%" );	
						},
						success: function( data )
						{
							
							//status is returned when the user logs in.  The backend script will try and grab the Subscriber ID based off the email address sent to the script.  If the email is malformed a status of 300 will be returned and inform the user their email is not formatted correctly
							if( data.status )
							{
								
								$(".progress").css("width","0px");
								$(".progressbar").hide( );
								
								if( parseInt( data.status ) == 300 )
								{
									$("#error_email").html( "Your E-Mail is not formatted correctly. ");
									$("#presslaffsubmit").val("Enter Contest");
								}
							}
							else
							{
								
								if( data.success == "True" )
								{
									location.href = "/" + contestslug_public;	
								}
								else
								{
									$(".progress").css("width","0px");
									$(".progressbar").hide( );
									
									$("#presslaffsubmit").val("Enter Contest");
									
									if( typeof data.errors == "string" )
									{
										$("#form_error").addClass("msg_err").html( data.errors );
									}
									else
									{
										for( var e in data.errors )
										{
											$("#error_" + data.errors[e].field).addClass("msg_err").html( data.errors[e].value );
										}	
									}
								}	
							}
							
						},
						type: 'post',
						dataType: 'json',
						clearForm: false	
						
					};	
						
					$("form[name=contest" + contestid + "]").ajaxForm( options );			
				
				}
			});
		}
	});
}(jQuery));

function openModal( )
{
	$ = jQuery;
	
	$("#editModal").animate({top : "150px"}, 1000, function( ){
		$('#edit-overlay, #edit-overlay-back').fadeIn( 1000 );
	});
}