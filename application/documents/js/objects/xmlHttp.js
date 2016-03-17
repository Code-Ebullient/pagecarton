//	Include the neccessary css, js files
//ayoola.files.loadJsObjectCss( 'xmlHttp' );
ayoola.files.loadJsObject( 'events' );
ayoola.files.loadJsObject( 'style' );

//	Object Begins
ayoola.xmlHttp =
{
	objects: new Object, // Stores the xmlHTTP Objects 
	objectCounter: 0, // Counts the number of xmlHTTP objects created. 
	afterStateChangeCallbacks: Array(), // Callbacks after state change 
	beforeStateChangeCallbacks: Array(), // Callbacks after state change 
	classPlayerUrl: '/tools/classplayer/', // Url for application class player 
	
	//	Initialize the objects for automatic enablement
	init: function()
	{
		for( var a = 0; a < document.forms.length; a++ )
		{
	//		continue;
			var formElement = document.forms[a];
			var callback = ayoola.xmlHttp.sendForm;
			if( formElement.enctype == 'multipart/form-data' ){ callback = ayoola.xmlHttp.simulateSendFormWithIframe; }
			if( formElement.getAttribute( 'data-not-playable' ) ){ continue; }
			ayoola.events.add( formElement, 'submit', callback ); 
		}
		var links = document.links;
		for( var a = 0; a < links.length; a++ )
		{
			if( ayoola.spotLight.isPlayable( links[a] ) )
			{ 
				ayoola.events.add( links[a], 'click', ayoola.spotLight.play ); 
			}
			else if( ayoola.xmlHttp.isClassPlayerLink( links[a] ) )
			{ 
				ayoola.events.add( links[a], 'click', ayoola.xmlHttp.play ); 
			}
		}
	//	ayoola.xmlHttp.setAfterStateChangeCallback( ayoola.xmlHttp.init );
		
	},
	
	//	Return a new XmlHttp Object when called
	getObject: function( uniqueNameForObject )
	{
	//	alert( uniqueNameForObject );
		var object = false;
		object = window.XMLHttpRequest ? new XMLHttpRequest() : object;
 		try{ object = new XMLHttpRequest(); }
		catch( exception1 )
		{
			try{ object = new ActiveXObject( 'MSXML2.XMLHTTP' ); }
			catch( exception2 )
			{ 
				try{ object = new ActiveXObject( 'Microsoft.XMLHTTP' ); }
				catch( exception3 ){ null; }
			}
		}
 		if( object )
		{ 
			uniqueNameForObject = uniqueNameForObject ? uniqueNameForObject : ayoola.xmlHttp.objectCounter;
			ayoola.xmlHttp.objectCounter++;
			ayoola.xmlHttp.objects[uniqueNameForObject] = object; 
		}
		return object;
	},
	
	//	Sends a form to server
	sendForm: function( e )
	{
		var result = true; //	Use this to determine if the function is successful
		var ajax = ayoola.xmlHttp.getObject();
		var target = ayoola.events.getTarget( e );
		var method = target.getAttribute( 'method' ) || 'GET';
		var name = target.getAttribute( 'name' );
	//	var parent = ayoola.div.getParent( target, 2 ); // Two steps backward
		var parents = document.getElementsByName( name + "_container" ); // Gets containers
	//	alert( parents.length );
	//	alert( target.getAttribute( 'action' ) );
		
		//	Allows me to be able to delete rich text editors before getting form values
		ayoola.xmlHttp.callBeforeStateChangeCallbacks();
		
		//	Play the class silently
	//	alert( target.action );
	//	alert( target.action.search( /\/\// ) );
		if( target.action && ! target.action.search( /#/ ) )  
		{
			return true;
		}
		var formValues = ayoola.div.getFormValues( target );
		var url = ayoola.xmlHttp.getClassPlayerUrl() + 'get/object_name/' + name + '/' + ayoola.artificialQueryString;
	//	alert( formValues );
		var queryString = location.search ? location.search : '?'; // Put ? at the end of an empty query string
		url += method.toLowerCase() == 'get' ? queryString + '&' + formValues : '';
/* 		alert( method );
		alert( url );
		alert( location.search );
 */		
		ajax.open( method, url, true );
		var contentType = target.getAttribute( 'enctype' ) || 'application/x-www-form-urlencoded';
		ajax.setRequestHeader( 'Content-Type', contentType );
		var changeContent = function()
		{ 
			if( ayoola.xmlHttp.isReady( ajax ) )
			{ 
				if( ! ajax.responseText )
				{
					alert( 'Server returned an empty response.' );
				//	alert( ajax.responseText );
					return;
				}
				if( parents )
				{
					//	change for each container on the page
				//	alert( parents.length );
					//	workaround for a bug that is causing infinite loop when parents.length autogrow
					var c = parents.length;
					for( var a = 0; a < c; a++ )
					{
					//	alert( parents.length );
						var parent = parents[a];
				//			alert( a );
					//		alert( parents[a] );
				//			alert( parent.name );
							
							//	workaround for a bug that is causing infinite loop
							if( a > 3 ){ break; }
							
					//		alert( ayoola.scrollToViewMargin );
					//		parent.style.marginTop = ayoola.scrollToViewMargin; 
					//		alert( parent.style.marginTop );
							parent.scrollIntoView(); 
							parent.focus(); 
							if( parent.parentNode )
							{
								var b = document.createElement( 'div' );
								var d = document.getElementById( 'scrollToViewMargin_container' );
								if( ! d )
								{
									var d = document.createElement( 'div' );
									d.id = 'scrollToViewMargin_container';
									d.innerHTML = '<div id="scrollToViewMargin" style="padding-top:' + ayoola.scrollToViewMargin + ';"></div>';
							//		alert( d.outerHTML );
								}
								else
								{
									var e = d.cloneNode( true );
									d.parentNode.removeChild( d );
									d = e;
								}
								b.innerHTML = d.outerHTML + ajax.responseText; 
							//	b.style.paddingTop = ayoola.scrollToViewMargin; 
							//	b.style.marginTop = '100px'; 
								b.style.display = 'none';
							//	alert( b.style.marginTop );
								b = parent.parentNode.appendChild( b ); 
								parent.parentNode.replaceChild( b, parent ); 
								b.style.display = '';
								b.scrollIntoView(); 
								b.focus(); 
							}
							else
							{
								parent.innerHTML = ajax.responseText; 
							}
						//	parent.scrollIntoView(); 
						//	parent.focus(); 
						//	alert( parent.id );
						//	window.location.hash = "#ssss";
						//	if( parent.id ){ location.hash = "#ssss" + parent.id; }
					}
				}
				else
				{
					//	var parent = ayoola.div.getParent( target, 2 ); // Two steps backward
				}
				ayoola.xmlHttp.callAfterStateChangeCallbacks();
			} 
		}
		if( ! ayoola.events.add( ajax, 'readystatechange', changeContent ) ){ ajax.onreadystatechange = changeContent; }
		ajax = ayoola.xmlHttp.setDefault( ajax );
		ajax.send( formValues );
		if( e.preventDefault ){ e.preventDefault(); }
		if( result == true ){ return true; }
	//	ayoola.xmlHttp.simulateSendFormWithIframe( e );
		return false;
	},
	
	//	Use Iframe to simulate the AJAX feel
	simulateSendFormWithIframe: function( e )
	{
		var target = ayoola.events.getTarget( e );
		var name = target.getAttribute( 'name' ) || 'GET';
	//	var parent = document.getElementById( name ) || ayoola.div.getParent( target, 3 ); // Two steps backward
		var parent = ayoola.div.getParent( target, 2 ); // Two steps backward
		var iframe = document.createElement( 'iframe' );
		iframe.name = 'ayoola.xmlHttp.sendForm.' + name;
		iframe.style.display = 'none';
		document.body.appendChild( iframe );
		ayoola.xmlHttp.callBeforeStateChangeCallbacks();
		var changeContent = function( ev )
		{
			var iframeTarget = ayoola.events.getTarget( ev );
			
			var iframeParent = window.frames[iframe.name].document.getElementById( name ) || window.frames[iframe.name].document.firstChild;
			parent.innerHTML = iframeParent.innerHTML;
			ayoola.xmlHttp.callAfterStateChangeCallbacks();
			ayoola.xmlHttp.init();
		//	iframe.parentNode.removeChild( iframe ); //	self destruct
		}
		ayoola.events.add( iframe, 'load', changeContent )
		target.target = iframe.name;
	//	alert( iframe );
	//	if( e.preventDefault ){ e.preventDefault(); }
		return false;
	},
	
	//	Use Iframe to simulate fetching content with ajaxs
/* 	Fetches data from a link and puts it in an element
 */	simulateFetchContentWithIframe: function( link, element )
	{
		var iframe = document.createElement( 'iframe' );
		iframe.name = 'ayoola.xmlHttp.similator.fetch.' + name;
		iframe.style.display = 'none';
		iframe.src = link;
		document.body.appendChild( iframe );
		var changeContent = function( ev )
		{
	//		alert( iframe.contentDocument );
		//	alert( iframe.contentDocument.documentElement );
			var iframeParent = iframe.contentDocument || window.frames[iframe.name].document.firstChild;
			element.innerHTML = iframeParent.innerHTML;
		//	ayoola.xmlHttp.callAfterStateChangeCallbacks();
		//	ayoola.xmlHttp.init();
		//	iframe.parentNode.removeChild( iframe ); //	self destruct
		}
		ayoola.events.add( iframe, 'load', changeContent )
	//	alert( iframe );
	//	if( e.preventDefault ){ e.preventDefault(); }
		return false;
	},
	
	//	Retrieves a link via ajax
	fetchLink: function( linkObject, uniqueNameForObject, dataToSend )
	{
	//	alert( linkObject );
	//	alert( typeof linkObject );
		switch( typeof linkObject )
		{
			//	Compatibility
			case 'string':
				linkObject = { url: linkObject, id: uniqueNameForObject, data: dataToSend }
		//		alert( linkObject );
/* 				linkObject.url = linkObject;
				linkObject.id = uniqueNameForObject;
				linkObject.data = dataToSend;
 */			break;
			case 'object':
			
			break;
		}
		var method = linkObject.data ? 'POST' : 'GET';
/* 		alert( linkObject.url );
		alert( linkObject.id ); 
		alert( linkObject.data );
 */		var ajax = ayoola.xmlHttp.getObject( linkObject.id );
	//	alert( ajax );
		ajax.open( method , linkObject.url, true );
		ajax = ayoola.xmlHttp.setDefault( ajax );
		if( method == 'POST' && ajax.setRequestHeader )
		{
			ajax.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			ajax.setRequestHeader( 'Request-Type', 'xmlHttpFetchLink' );
		}
/* 		if( ! changeContent )
		{ 
			throw new Exception( 'Function to perform on state change was not defined' );
//			return false; 
		}
		var callback = function(){ changeContent( e, ajax ); }
		ayoola.events.add( ajax, 'readystatechange', callback );
 */		ajax = ayoola.xmlHttp.setDefault( ajax );
		! linkObject.skipSend ? ajax.send( linkObject.data ) : null;
		return ajax;
	},
	
	//	Refreshes a content
	refreshElement: function()
	{
		for( var a = 0; a < arguments.length; a++ )
		{
			var ajax = ayoola.xmlHttp.getObject();
			var element = arguments[a]; //
			if( typeof element == 'string' )
			{
			//	var f = b[0].getAttribute( 'data-object-name' );
				var b = document.getElementsByName( element + '_container' );
		//		alert( rel.changeElementId );
		//		alert( element );
				if( ! b.length ){ return false; }
				if( ! b[0].getAttribute( 'data-object-name' ) ){ return false; }
				var url = ayoola.xmlHttp.getClassPlayerUrl() + 'get/object_name/' + b[0].getAttribute( 'data-object-name' ) + '/' + location.search;
			//	element = document.getElementById( element );
			}
			else if( element )
			{
		//	if( ! element ){ continue; }
				if( ! element.getAttribute( 'data-object-name' ) ){ return false; }
				var url = ayoola.xmlHttp.getClassPlayerUrl() + 'get/object_name/' + element.getAttribute( 'data-object-name' ) + '/' + location.search;
			}
			if( ! url ){ continue; }
			ajax.open( 'GET', url, true );
			var changeContent = function()
			{
				if( b )
				{
					if( ayoola.xmlHttp.isReady( ajax ) )
					{
						for( var a = 0; a < b.length; a++ )
						{
					//		alert( b.length );
							var c = b[a];
						//	c.innerHTML = ajax.responseText; 
							var d = document.createElement( 'span' );
							d.innerHTML = ajax.responseText;
							var e = c.parentNode;
							e.removeChild( c );
							e.appendChild( d );
						}
					} 
				}
				else if( element )
				{
					element.innerHTML = ajax.responseText; 
				}
			}
			ayoola.events.add( ajax, 'readystatechange', changeContent );
			ajax = ayoola.xmlHttp.setDefault( ajax );
			ajax.send( null );
		}
	},
	
	//	Play a link by refreshing an identified changeElement
	play: function( e )
	{
		var target = this; //ayoola.events.getTarget( e );
		var ajax = ayoola.xmlHttp.getObject();
		var rel = ayoola.div.getAnchorRel( target );
		var url = rel.classPlayerUrl || target.href;
	//	var element = document.getElementById( rel.changeElementId );
		var b = document.getElementsByName( rel.changeElementId + '_container' );
//		alert( rel.changeElementId );
//		alert( element );
		if( ! b.length ){ return false; }
		
		ajax.open( 'GET', url, true );
		var changeContent = function()
		{
			if( ayoola.xmlHttp.isReady( ajax ) )
			{
			//	alert( b.length );
				for( var a = 0; a < b.length; a++ )
				{
			//		alert( b.length );
					var c = b[a];
				//	c.innerHTML = ajax.responseText; 
					var d = document.createElement( 'span' );
					d.innerHTML = ajax.responseText;
					var e = c.parentNode;
					e.removeChild( c );
					e.appendChild( d );
				}
			} 
		}
		ayoola.events.add( ajax, 'readystatechange', changeContent );
		ajax = ayoola.xmlHttp.setDefault( ajax );
		ajax.send( null );
		if( e.preventDefault ){ e.preventDefault(); }
	},
	
	//	Check if the an anchor as a classplayer link
	isClassPlayerLink: function( anchor )
	{
		if( undefined == anchor.href ){ return false; }
		
		var rel = ayoola.div.getAnchorRel( anchor );
		if( anchor.href.search( ayoola.xmlHttp.classPlayerUrl ) == -1 )
		{ 
			if( ! rel.classPlayerUrl || rel.classPlayerUrl.search( ayoola.xmlHttp.classPlayerUrl ) == -1 )
			{
				return false; 
			}
		}
	//	alert( rel.changeElementId );
		if( ! rel.changeElementId ){ return false; }
		var b = document.getElementsByName( rel.changeElementId + '_container' );
		if( ! b.length ){ return false; }
	//	alert( b.length );
	//	alert( rel.classPlayerUrl );
		return true
	},
	
	//	Check if the xmlHttp Object is ready
	isReady: function( object )
	{
		if( object.readyState == 4 && object.status == 200 ){ return true; }
		return false;
	},
	
	//	Set the ajax to some preset settings
	setDefault: function( ajax )
	{
		if( ajax.setRequestHeader ){ ajax.setRequestHeader( 'Request-Type', 'xmlHttp' ); }
//		ayoola.xmlHttp.callBeforeStateChangeCallbacks();
		
		//	Instantiate all the registered callbacks for statechange
	//	for( var a = 0; a < ayoola.xmlHttp.afterStateChangeCallbacks.length; a++ )
		{
		//	alert( ayoola.xmlHttp.afterStateChangeCallbacks[a] );
	//		ayoola.events.add( ajax, 'readystatechange', ayoola.xmlHttp.afterStateChangeCallbacks[a] );
		}
		ayoola.events.add( ajax, 'readystatechange', ayoola.xmlHttp.init );
		return ajax;
	},
	
	//	Include what to do before status changed
	setBeforeStateChangeCallback: function( callback )
	{
		if( ! callback ){ return; }
		for( var a = 0; a < ayoola.xmlHttp.beforeStateChangeCallbacks.length; a++ )
		{
			if( ! callback == ayoola.xmlHttp.beforeStateChangeCallbacks[a] ){ return; }
		}
		ayoola.xmlHttp.beforeStateChangeCallbacks.push( callback );
	},
	
	//	Do 
	callBeforeStateChangeCallbacks: function()
	{
	//		alert( ayoola.xmlHttp.afterStateChangeCallbacks.length );
		for( var a = 0; a < ayoola.xmlHttp.beforeStateChangeCallbacks.length; a++ )
		{
			var callback = ayoola.xmlHttp.beforeStateChangeCallbacks[a];
			if( ! callback || typeof callback != 'function' ){ return; }
			callback();
		//	alert( callback );
		}
	},
	
	//	Include what to do when status changed
	setAfterStateChangeCallback: function( callback )
	{
		if( ! callback ){ return; }
		for( var a = 0; a < ayoola.xmlHttp.afterStateChangeCallbacks.length; a++ )
		{
			if( ! callback == ayoola.xmlHttp.afterStateChangeCallbacks[a] ){ return; }
		}
		ayoola.xmlHttp.afterStateChangeCallbacks.push( callback );
	},
	
	//	Include what to do when status changed
	callAfterStateChangeCallbacks: function()
	{
	//		alert( ayoola.xmlHttp.afterStateChangeCallbacks.length );
		for( var a = 0; a < ayoola.xmlHttp.afterStateChangeCallbacks.length; a++ )
		{
			var callback = ayoola.xmlHttp.afterStateChangeCallbacks[a];
			if( ! callback || typeof callback != 'function' ){ return; }
			callback();
		}
	},
	
	//	Include what to do when status changed
	getClassPlayerUrl: function()
	{
		return ayoola.pcPathPrefix + ayoola.xmlHttp.classPlayerUrl;
	}
}

//	End Object
ayoola.events.add( window, 'load', ayoola.xmlHttp.init );