//	Class Begins
ayoola.js =
{
	addCodeToHead: false, // whether to add code to the top or to the bottom.
	delayLoading: false, // whether to add code to the top or to the bottom.
		
	//	Add a javascript code to the body
	addCode: function( code, returnScript )
	{
		var script = document.createElement( 'script' );
		script.setAttribute( 'type', 'text/javascript' );
		script.innerHTML = code;
		if( returnScript )
		{
			return script;
		}
		ayoola.js.addToBody( script, true  );
	},
		
	//	Add a javascript code to the body
	addCodeOnLoad: function( code )
	{
		ayoola.js.addToBody( ayoola.js.addCode( code, true ), true, true );
	},
		
	//	Add a javascript file to the body
	addFile: function( file, returnScript )
	{
		var script = document.createElement( 'script' );
		script.setAttribute( 'src', file );
		if( returnScript )
		{
			return script;
		}
		ayoola.js.addToBody( script, true  );
	},
		
	//	Add a javascript file to the body onload
	addFileOnLoad: function( file )
	{
		ayoola.js.addToBody( ayoola.js.addFile( file, true ), true, true );
	},
		
	//	Add a javascript file to the body
	addToBody: function( script, addCodeToHead, delayLoading )
	{
		//	Ensure this is not a double addition
		var scriptsInDocument = document.getElementsByTagName( 'script' );
		if( scriptsInDocument && scriptsInDocument.length )
		{
			for( var a = 0; a < scriptsInDocument.length; a++ )
			{
				var currentScript = scriptsInDocument[a];
				if( currentScript.innerHTML == script.innerHTML && script.src == currentScript.src )
				{
				//	alert( currentScript.src );
					return false;
				}
			
			}
		}
		var parentElement = document.body;
		if( addCodeToHead )
		{
			var head = document.getElementsByTagName( 'head' );
			if( head && head.item( 1 ) ){ parentElement = head.item( 1 ); }
		}
	//	alert( script );
	//	alert( script.src );
		if( ! delayLoading )
		{
			parentElement.appendChild( script );
		}
		else
		{
			ayoola.events.add( window, 'load', function(){ parentElement.appendChild( script ); } );
		}
	},
}
