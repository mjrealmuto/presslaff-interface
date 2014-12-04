var exDays = 2;

(function ($) {
	"use strict";
	$(function () {
		
		var path = location.pathname;
		
		path = path.replace(/\//g, "");
		
		if( path == loginslug )
		{
			if( isValidSession( ) )
			{
				location.href = "/" + contestslug;
			}
		}
		else if( path == contestslug )
		{
			if( ! isValidSession( ) )
			{
				location.href = "/" + loginslug;
			}
		}
		
		
		$("#p-prev-contest").on("click", function( )
		{
			var clone = $(".presslaff-item:first-child").clone( true );
			
			$(".presslaff-item > li").animate({ left : "-=220px"}, 1000);
			
			$(".presslaff-item:first-child").remove( );
			
			$(".presslaff-list").append( clone );
			
		});
		
		$("#p-next-contest").on("click", function( )
		{
			$("#presslaff-item:last-child").css("display","none");
			
			var clone = $(".presslaff-item:last-child").clone( true );
			
			$(".presslaff-item:last-child").remove( );
			
			$(".presslaff-list").prepend( clone );
			
			$(".presslaff-item:first-child").css("display","block");
			
			$(".presslaff-item > li").animate({left : "+=440px"}, 1000);
			
		});
		
	});
}(jQuery));

function supports_html5_storage() 
{
  try 
  {
    return 'localStorage' in window && window['localStorage'] !== null;
  } 
  catch ( e ) 
  {
    return false;
  }
}

function isValidSession( )
{
	if( supports_html5_storage( ) )
	{

		if( localStorage.getItem("presslaffID") != "undefined" && localStorage.getItem("presslaffID") != null )
		{
			var expire_date = localStorage.getItem("presslaffExpire");
			
			var now = new Date( );
			
			var ts = now.getTime( )	
			
			if( expire_date < ts )
			{
				localStorage.removeItem("presslaffID");
				localStorage.removeItem("presslaffExpire");

				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		if( getCookie( 'presslaffID' ) == "" )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}

function getCookie( cname ) 
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0 ; i < ca.length ; i++) 
    {
        var c = ca[i];
        
        while (c.charAt(0)==' ') c = c.substring(1);
        
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    
    }
    return "";
} 

function setCookie(cname, cvalue, exdays)
{
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function checkCookie( cookiekey ) 
{
    var cookie = getCookie( cookiekey );
    if (cookie != "") {
        return true;
    } 
    else 
    {
        return false;
    }
}

