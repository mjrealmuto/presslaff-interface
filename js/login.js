var exDays = 2;

(function ($) {
	"use strict";
	$(function () {
		
		//Actions for Presslaff Login
		
		$("body").append( $('<div>', {id : "signup-overlay", class : "overlay"}) );
		$("body").append( $('<div>', {id : "signup-overlay-back", class : "overlay-back"}) );
			
		$("#signup-overlay").before( $('<div>', {id : "signupModal", class : "modal"}) );
			
		$("#signup-overlay, #signup-overlay-back").css("height", $("body").css("height") );
			
		$("#signupModal").html("Loading Registration Questions...");
		
		$(window).load(function( ){ 
			
			$("#signupModal").html( $("#signupModalTmp").html( ) );
			
			$("#signupModalTmp").remove( );
			
			$("#modal-close").on({
				
				mouseover: function( )
				{
					$(this).css("cursor","pointer");
				},
				mouseleave: function( )
				{
					$(this).css("cursor","auto");	
				},
				click: function( )
				{
					$("#signupModal").animate({top : "-999999px"}, 500);
					$('#signup-overlay, #signup-overlay-back').fadeOut( 500 );
				}
			});
			
			$("form[name=register]").on("submit", function( e ){

				e.preventDefault( );
				e.stopPropagation( );
				
				
				$.ajax({
					method: "POST",
					url: $(this).attr("action"),
					data: $(this).serialize( ),
					dataType: "json",
					beforeSend: function( )
					{
						if( $("#register_msg").hasClass( "msg_err") )
						{
							$("#register_msg").html( "Give us a second while we sign you up!!!").removeClass("msg_err").addClass("msg_okay");	
						}
						else
						{
							$("#register_msg").html( "Give us a second while we sign you up!!!").addClass("msg_okay");
						}
					},
					success: function( data )
					{
						var $msg = $("#register_msg");
						
						switch( parseInt( data.status ) )
						{
							case 100:
								$msg.html( "Your Account has been Created! Logging you in...");
								
								console.log( data.email );
								
								
								$.ajax({
									method: "POST",
									url: "/wp-admin/admin-ajax.php",
									data: {username : data.email, action : "presslafflogin"},
									dataType: "json",
									success: function( data )
									{
										
										var d = new Date( );
							
										var ts = d.getTime() + (exDays*24*60*60*1000);
										
										if( supports_html5_storage( ) )
										{
											localStorage.setItem("presslaffID", data.accountID );
											localStorage.setItem("presslaffExpire", ts);
											
										}
										else
										{
											setCookie("presslaffID", data.accountID, exDays );
										}
										
										location.href =  "/" + contestslug;
									}
								});
								
							break;
							default:
								$msg.html( data.msg ).removeClass("msg_okay").addClass("msg_err");
							break;
						}
					}
				});
			});
			
			$("form[name=presslaffLogin]").on("submit", function( e ){

				e.preventDefault( );
				e.stopPropagation( );
				
				var email = $(this).children("input[name=emailaddress]").val( );
				
				if( email.trim( ).length == 0 )
				{
					$("#transit_message").addClass("error").html("Please enter an Email Address");
				}
				else
				{
					$.ajax({
						method : "POST",
						url : $(this).attr("action"),
						data : {username : email, action : "presslafflogin"},
						dataType: "json",
						beforeSend: function( ){
							
							$("#transmit_message").removeClass("msg_err").addClass("msg_okay").html("Logging you in...");
						},
						success: function( data )
						{
							
							if( parseInt( data.status ) == 100 )
							{
								var d = new Date( );
							
								var ts = d.getTime() + (exDays*24*60*60*1000);
								
								if( supports_html5_storage( ) )
								{
									localStorage.setItem("presslaffID", data.accountID );
									localStorage.setItem("presslaffExpire", ts);
									
								}
								else
								{
									setCookie("presslaffID", data.accountID, exDays );
								}	
								
								location.href =  "/" + contestslug;
							}
							else if( data.status == 200 )
							{
								$("#transmit_message").removeClass("msg_okay").addClass("msg_err").html("The E-Mail Address Supplied is not in our Database.");
							}
							else if( data.status == 300 )
							{
								$("#transmit_message").removeClass("msg_okay").addClass("msg_err").html("Not a valid E-Mail Address.");
							}
						}
					});			
				}
			});     
		});
	});
}(jQuery));


function signup( )
{
	$ = jQuery;
	
	$("#signupModal").animate({top : "150px"}, 1000, function( ){
		$('#signup-overlay, #signup-overlay-back').fadeIn( 1000 );
	});
}
