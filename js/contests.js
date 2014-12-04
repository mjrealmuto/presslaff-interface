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
		
		$("#edit").on({
			
			click: function( )
			{
				//openModal( );	
				//window.open(  );
				
				$.colorbox({href: "https://www.1.dat-e-baseonline.com/front/updatelink.asp?a=UpdateRequest&zx=" + stationid, iframe : true, width : "80%", height: "80%"});
			},
			mouseover: function( )
			{
				$(this).css({
					cursor: "pointer",
					fontWeight: "bold"
				});
			},
			mouseout: function( )
			{
				$(this).css({
					cursor: "auto",
					fontWeight: "normal"
				});
			}
		});
		
		$("#logout").on({
			
			click: function( )
			{
				if( supports_html5_storage( ) )
				{
					localStorage.removeItem("presslaffID");
					localStorage.removeItem("presslaffID_expire");
				}
				else
				{
					document.cookie = "presslaffID=; expires=Thu, 01 Jan 1970 00:00:00 UTC"; 
				}	
				
				location.href = "/" + loginslug;
			},
			mouseover: function( )
			{
				$(this).css({
					cursor: "pointer",
					fontWeight: "bold"
				});
			},
			mouseout: function( )
			{
				$(this).css({
					cursor: "auto",
					fontWeight: "normal"
				});
			}
		});
		
		
		if( contestid.length == 0 )
		{
			$.ajax({
				method: "GET",
				url: "/wp-admin/admin-ajax.php?pID=" + accountid + "&action=getContests",
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
							// - Removed to allow for 'Hidden' contests to be shown for Private contests only
							//if( data[x].Hidden.toLowerCase( ) == "false" )
							//{
								
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
							
							if( data[x].SubscriberParticipated.toLowerCase( ) == "true" )
							{
							
								if( ned == now )
								{
									contests += "<div class='contest_container' style='border-bottom: 1px solid #DDD; padding-bottom: 10px; '>";
									contests += "<img class='alignleft' src='" + data[x].LogoURL + "' title='" + data[x].Name + "' />";
									contests += "<p class='p-title'>" + data[x].Name + "</p>";
									contests += "<br />";
									contests += "<p>" + data[x].Description + "</p>";
									contests += "You Participated in this contest at: " + data[x].ParticipatedDate + "<br />";
								}
								else
								{
								
									if( ned < now )
									{
										var contestLinkStart = "<a onclick=\"_gaq.push(['_trackEvent', 'outbound-article-int', '" + location.href + "?contestid=" + data[x].ContestID + "', '']);\" href='" + location.href + "?contestid=" + data[x].ContestID + "'>";
										
										contests += "<div class='contest_container' style='border-bottom: 1px solid #DDD; padding-bottom: 10px; '>";
										contests += contestLinkStart + "<img class='alignleft' src='" + data[x].LogoURL + "' title='" + data[x].Name + "' /></a>";
										contests += "<p class='p-title'>" + contestLinkStart + data[x].Name + "</a></p>";
										contests += "<br />";
										contests += contestLinkStart + "Enter Here</a>";
										contests += "<br />You Participated in this contest at: " + data[x].ParticipatedDate + "<br />";
									}
									else
									{
										
										contests += "<div class='contest_container' style='border-bottom: 1px solid #DDD; padding-bottom: 10px; '>";
										contests += "<img class='alignleft' src='" + data[x].LogoURL + "' title='" + data[x].Name + "' />";
										contests += "<p class='p-title'>" + data[x].Name + "</p>";
										contests += "<br />";
										contests += "<p>" + data[x].Description + "</p>";
										contests += "You Participated in this contest at: " + data[x].ParticipatedDate + "<br />";
										
										var days = 1000*60*60*24;
										
										var hours = 1000*60*60;
										
										var mins  = 1000*60;
										
										var difference_ms = ned - now;
										
										//days_left Math.round( difference_ms/one_day ) );											
										
										contests += "You can enter this contest again on: " + data[x].NextEligibilityDate + "<br />";
									}
								}
								
								contests += "<div class='clear'></div>";
								contests += "</div>";
								contests += "<hr ><br />";	
							}
							else
							{
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
							//}
						}
												
						$("#presslaff_contests").html( contests );
					}
				},
				error: function( x,y )
				{
					
					console.log( x );
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
					
					contest_out += "<hr /><br />";
					contest_out +=  "\
						<div id='form_error'></div>\
						<form name='contest" + contestid + "' action='/wp-admin/admin-ajax.php' method='post'>\
						<input type='hidden' name='action' value='entercontest' />\
						<input type='hidden' name='contestid' value='" + contestid + "' />\
						<input type='hidden' name='presslaffid' value='" + accountid + "' />\
					";
					
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
						<input type='hidden' name='fieldcount' value='" + fieldcount + "' />\
						<br />\
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
							
							$(".progressbar").show( );
						},
						uploadProgress: function(events, position, total, percentComplete)
						{ 
							console.log( percentComplete );
							$(".progress").css( "width", percentComplete + "%" );	
						},
						success: function( data )
						{
							if( data.success == "True" )
							{
								$(".progress").css("width","100%");
								location.href = "/" + contestslug;	
							}
							else
							{
								$(".progress").css("width","0px");
								$(".progressbar").hide( );
								
								$("#presslaffsubmit").val("Enter Contest");
							
								document.getElementById("presslaffsubmit").disabled = false;
								
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