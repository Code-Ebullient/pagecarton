//	Include the neccessary css, js files
//ayoola.files.loadJsObjectCss( 'div' );
//ayoola.files.loadJsObject( 'events' );

//	Class Begins
ayoola.image =
{
	fileCount: 0, // Number of files in the file record
	namespace: null, // Namespace to use for looking for IDs
	formElement: null, // Element to preload the returned information
	fieldName: null, // Fieldname to enter the returned information
	fieldNameValue: '', // Fieldname to enter the returned information
	directory: null, // The Directory to upload to
	root: null, // PHP Constant of the ROOT DIRECTORY e.g. APPLICATION_DIR, DOCUMENT_DIR
	maxWidth: 1500, // Max Width
	maxHeight: 900, // Max Height
	defaultWidth: 1500, // Max Width
	defaultHeight: 900, // Max Height 
	upLoadOnSelect: true,
	previewOnSelect: true,
	uniqueNameForBrowseButton: 'ayoola_image_ajax_upload_button',
	mimeType: 'image/jpeg',
	suggestedUrl: '', //	Used to "edit" pictures
	url: '/tools/classplayer/get/object_name/Ayoola_Doc_Upload_Ajax/', //	URL to upload the file too
	files: {}, //	Storage for file records
	splashScreen: {},
	thumbnail: 
	{
		//	Cropping size
		size:
		{
			width: 32,
			height: 32
		},
		//	First resize the image to this size before croping.
		resize:
		{
			width: 48,
			height: 48
		}
	}, 
	cropping: 
	{
		//	Switch center cropping on or off
		crop: false,

		//	First add this to crop size and resize the image before croping.
		resizeOffset:
		{
			width: 100,
			height: 100
		}
	}, 
	//	Storage for file records
	emptyFileObject: 
	{
		preview: 
		{
			node: null,
			count: 0
		} //	every new file object will be preloaded with this
	}, 
	afterStateChangeCallbacks: new Array( ), //	
	
	//	Changes an image from the browser
	change: function( imageInfo ) 
	{
		imageInfo.crop = imageInfo.crop || '';
		ayoola.spotLight.showLinkInIFrame( '/tools/classplayer/?object_name=Ayoola_Doc_Upload_Link&image_url=' + imageInfo.url + '&crop=' + imageInfo.crop );
	},
	
	//	Uploads a file to the server
	upload: function( fileObject ) 
	{
		fileObject.url = fileObject.url || ayoola.image.url;
		fileObject.directory = fileObject.directory || ayoola.image.directory;
		fileObject.maxWidth = fileObject.maxWidth || ayoola.image.maxWidth || ayoola.image.defaultWidth;
		fileObject.maxHeight = fileObject.maxHeight || ayoola.image.maxHeight || ayoola.image.defaultHeight;
		fileObject.suggestedUrl = fileObject.suggestedUrl || ayoola.image.suggestedUrl;
		
		//	Make ajax upload a var
		var ajaxUpload = function( dataObject )
		{
			//	UPLOAD FILE TO THE SERVER INSTANTLY IF ALLOWED
			if( ! ayoola.image.upLoadOnSelect )
			{
			//	var aa = dataObject.url.split( ',' );
			//	aa.
				ayoola.image.files[fileObject.id]['response'] = 
				{ 
					file_info:
					{
						url: dataObject.url,
						dedicated_url: dataObject.url,
						base: dataObject.url
						//	this is not working because i also need to save mime type
					//	base: dataObject.url.split( ',' ).pop()
					}
				
				};
				ayoola.image.files[fileObject.id]['status'] = 'UPLOAD ON SELECT IS TURNED OFF'; 
				ayoola.image.callAfterStateChangeCallbacks( fileObject.id );
			//	alert( ayoola.image.files[fileObject.id]['response'].file_info.url );
			//	alert( ayoola.image.files[fileObject.id]['response'].file_info.base );
				return false;
			}
				
			//	UPLOAD TO SERVER
			var uniqueNameForAjax = 'upload_' + fileObject.id;
			
			//	Sets Ajax but dont send yet
			ayoola.xmlHttp.fetchLink( { url: ayoola.pcPathPrefix + fileObject.url, id: uniqueNameForAjax, data: dataObject.data, skipSend: true } );
		//	alert( arguments.length );
			var ajax = ayoola.image.files[fileObject.id].ajax = ayoola.xmlHttp.objects[uniqueNameForAjax];
			ajax.setRequestHeader( 'AYOOLA-PLAY-MODE', 'JSON' );
	//		alert( ajax );
	//		alert( ajax.setRequestHeader( 'AYOOLA_PLAY_MODE', 'JSON' ) );
			var ajaxCallback = function()
			{
				//	alert( ajax );
				if( ayoola.xmlHttp.isReady( ajax ) )
				{
				//	alert( ajax.responseText );
					if( ! ajax.responseText )
					{ 
						return false;
					}
					try
					{
						var response = JSON.parse( ajax.responseText );
					}
					catch( e )
					{
					 	alert( e ); //error in the above string(in this case,yes)! 						
						alert( ajax.responseText );
						
						// An error has occured, handle it, by e.g. logging it
						console.log( e );
					}
				//	var response = JSON.parse( ajax.responseText );
					ayoola.image.files[fileObject.id]['response'] = response;
					ayoola.image.files[fileObject.id]['status'] = 'Uploaded'; 
					ayoola.image.callAfterStateChangeCallbacks( fileObject.id );
				} 
			}
			ayoola.events.add( ajax, "readystatechange", ajaxCallback );
			
			//	Send ajax request
		//	ajax.setRequestHeader( 'AYOOLA-PLAY-MODE', 'JSON' );
			ajax.send( dataObject.data );
		}
		var dataToSend =  '&name=' + fileObject.file.name + '&id=' + fileObject.id+ '&directory=' + fileObject.directory + '&max_width=' + fileObject.maxWidth + '&max_height=' + fileObject.maxHeight + '&suggested_url=' + fileObject.suggestedUrl + '&mime_type=' + fileObject.file.type;
/* 		alert( dataToSend );
 		alert( fileObject );
		alert( fileObject.type );
 */		
	//	alert( fileObject.file.type );
		var reader = new FileReader();
		reader.onloadend = function() 
		{
		
	//		alert( fileObject.file.type );
	//		alert( reader.result );
			if( fileObject.file.type.match( 'image.*' ) && ! fileObject.file.type.match( '.*gif' ) ) 
			{
				var tempImg = new Image();
				tempImg.src = reader.result;
				tempImg.onload = function() 
				{
					var MAX_WIDTH = fileObject.maxWidth;
					var MAX_HEIGHT = fileObject.maxHeight;
					
			//		alert( MAX_WIDTH );
			//		alert( MAX_HEIGHT );
					
					if( ayoola.image.cropping.crop )
					{
						//	we need to add the cropping offset to be used later when centercropping
						// MAX_WIDTH = MAX_WIDTH + ayoola.image.cropping.resizeOffset.width;
						// MAX_HEIGHT = MAX_HEIGHT + ayoola.image.cropping.resizeOffset.height;
						
						//	Now, lets pad by 10%
					//	MAX_WIDTH = MAX_WIDTH + ( tempImg.width / 50 ); 
					//	MAX_HEIGHT = MAX_HEIGHT + ( tempImg.height / 50 );
					}
					
					var tempW = tempImg.width;
					var tempH = tempImg.height;
					if( tempW < MAX_WIDTH )
					{
						MAX_WIDTH = tempW;
					}
					if( tempH < MAX_HEIGHT )
					{
						MAX_HEIGHT = tempH;
					}
					
					if( tempW > tempH ) 
					{
						if( tempW > MAX_WIDTH ) 
						{
						   tempH *= MAX_WIDTH / tempW;
						   tempW = MAX_WIDTH;
						}
					} 
					else 
					{
						if( tempH > MAX_HEIGHT ) 
						{
						   tempW *= MAX_HEIGHT / tempH;
						   tempH = MAX_HEIGHT;
						}
					}
					var canvas = document.createElement( 'canvas' );
					canvas.width = tempW;
					canvas.height = tempH;
					var ctx = canvas.getContext( "2d" );
					ctx.drawImage( this, 0, 0, tempW, tempH );
					var dataURL = canvas.toDataURL( fileObject.file.type );
					var data = 'image=' + dataURL + dataToSend;
				//	alert( dataURL );
					if( ayoola.image.cropping.crop && ( tempW > MAX_WIDTH && tempH > MAX_HEIGHT ) )
					{
						//	Perfom center crop
						var img = new Image();

						img.onload = function() 
						{
							var canvas = document.createElement( 'canvas' );
							canvas.width = fileObject.maxWidth;
							canvas.height = fileObject.maxHeight;
							var context = canvas.getContext( '2d' );
							// draw cropped image
							var sourceWidth = fileObject.maxWidth;
							var sourceHeight = fileObject.maxHeight;
							var destWidth = sourceWidth;
							var destHeight = sourceHeight;
							var destX = canvas.width / 2 - destWidth / 2;
							var destY = canvas.height / 2 - destHeight / 2;
							var sourceX = img.width / 2 - destWidth / 2;
						//	sourceX = sourceX < 0 ? 0 : sourceX;
							var sourceY = img.height / 2 - destHeight / 2;
							//	alert( data );
							if( sourceX < 0 || sourceY < 0 )
							{
								sourceX = 0;
								sourceY = 0;
							//	alert( data );
							//	ajaxUpload( { data: data } );
								context.drawImage( img, 0, 0, sourceWidth, sourceHeight );  
							}
							else
							{
						//		sourceY = sourceY < 0 ? ( img.height / 2 ) : sourceY;
		/* 						alert( img );
		 */						context.drawImage( img, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight );
							}
							var newDataURL = canvas.toDataURL( fileObject.file.type );
							var data = 'image=' + newDataURL + dataToSend;
							
							//	UPLOAD FILE TO THE SERVER INSTANTLY IF ALLOWED
						//	if( ayoola.image.upLoadOnSelect )
							{
								ajaxUpload( { data: data, url: newDataURL } );
							}
						//	ajaxUpload( { data: data } );
						};
						img.src = dataURL;
					}
					else
					{
						//ajaxUpload( { data: data } );
						//	UPLOAD FILE TO THE SERVER INSTANTLY IF ALLOWED
					//	if( ayoola.image.upLoadOnSelect )
						{
							ajaxUpload( { data: data, url: dataURL } );
						}
					}
				}
			}
			else
			{
				var data = 'document=' + reader.result + dataToSend;
			//	alert( fileObject.file.type );
			//	alert( fileObject.file.file );
			//	alert( dataToSend );
			//	ajaxUpload( { data: data } );
				//	UPLOAD FILE TO THE SERVER INSTANTLY IF ALLOWED
			//	if( ayoola.image.upLoadOnSelect )
				{
					ajaxUpload( { data: data, url: reader.result } );
				}
			}
	 
		}
	//	alert( fileObject.file );
		reader.readAsDataURL( fileObject.file );  
 	//  reader.readAsDataURL( fileObject.file );  
	},
	
	//	Select a file for upload
	select: function( e ) 
	{
		e.stopPropagation();
		e.preventDefault();
		var target = ayoola.events.getTarget( e );
		
	//	var spinner = new Spinner().spin( document.body );
	//	spinner.spin( );
	//	ayoola.spotLight.popUp( "<div style=\"height:100%; background: rgba( 255, 255, 255, 0 ) url('http://i.stack.imgur.com/FhHRx.gif') 50% 50% no-repeat;\"></div>" ); 
	
		//	Set a splash screen to indicate that we are loading.
		var splash = ayoola.spotLight.splashScreen();
		ayoola.image.splashScreen = splash;
		
		if( window.File && window.FileReader && window.FileList && window.Blob ) 
		{
			var files = target.files || e.dataTransfer.files;
			var result = '';
			var file;
			for( var i = 0; i < files.length; i++ ) 
			{
				file = files[i];
				// if the file is not an image, continue
			//	alert( file.type );
			//	ayoola.image.
				if ( ! file.type.match( 'image.*' ) ) 
				{
				//	continue;
				}
				//	NEXT
				ayoola.image.fileCount++;
				
				ayoola.image.files[ayoola.image.fileCount] = { preview: { node: document.createElement( 'img' ), count: 0 } };
				ayoola.image.files[ayoola.image.fileCount].file = file;
				ayoola.image.files[ayoola.image.fileCount].fieldName = ayoola.image.fieldName;
				ayoola.image.files[ayoola.image.fileCount].fieldNameValue = ayoola.image.fieldNameValue;
				ayoola.image.files[ayoola.image.fileCount].formElement = ayoola.image.formElement;
				
				//	SET STATUS
				ayoola.image.files[ayoola.image.fileCount]['status'] = 'Ready to upload';
				ayoola.image.callAfterStateChangeCallbacks( ayoola.image.fileCount );
				
				//	UPLOAD FILE TO THE SERVER INSTANTLY IF ALLOWED
				
				//	Now doing this at ajax level
			//	if( ayoola.image.upLoadOnSelect )
				{
					ayoola.image.upload
					( 
						{ 
							file: file, 
							id: ayoola.image.fileCount 
						} 
					);
				}
			}
		}
		else 
		{
			alert( 'The File APIs are not fully supported in this browser.' );
		}
	},
	
	//	Set drop zone
	setDropZone: function( target )
	{
//		alert( e );
	//	var target = ayoola.events.getTarget( e );

		//	Drop effect
		dragOver = function( e )
		{
			e.stopPropagation();
			e.preventDefault();
			e.dataTransfer.dropEffect = 'copy';
		}
//		alert( e );
		
		//	EVERY FILE IN DROP ZONE IS SELECTED AS WELL
		ayoola.events.add( target, 'dragover', dragOver );
		ayoola.events.add( target, 'drop', ayoola.image.select );
		
		//	YOU MAY ALSO CLICK DROP ZONES TO UPLOAD
		ayoola.events.add( target, 'click', ayoola.image.clickBrowseButton );
		
	},
	
	//	
	clickBrowseButton: function( inputObject )
	{
		inputObject = inputObject || { }; 
		e1 = document.createEvent( "MouseEvents" );
		e1.initEvent( "click", true, false );
		var a = document.getElementById( ayoola.image.uniqueNameForBrowseButton );
		if( ! a )
		{
			var span = document.createElement( 'span' );
			
			//	in text for compatibility
			span.innerHTML = '<input type="file" accept="' + ( inputObject.accept || '' ) + '" id="' + ayoola.image.uniqueNameForBrowseButton + '" multiple="multiple" />';
			span.style.display = 'none';
			document.body.appendChild( span );
			a = document.getElementById( ayoola.image.uniqueNameForBrowseButton );
			
			//	select me for upload
	//		ayoola.events.add( a, 'change', function(){  } );
			ayoola.events.add( a, 'change', ayoola.image.select );
		}
		else
		{
			a.accept = inputObject.accept || '';  
		}
		a.dispatchEvent( e1 );
	},
	
	//	
/* 	makeMeClickBrowseButton: function( element )
	{
		ayoola.events.add( element, 'click', ayoola.image.setDropZone );
	},
 */	
	//	Include what to do when status changed
	setAfterStateChangeCallback: function( callback )
	{
		if( ! callback ){ return; }
		
		//	check if we are already there
		for( var a = 0; a < ayoola.image.afterStateChangeCallbacks.length; a++ )
		{
			if( ! callback == ayoola.image.afterStateChangeCallbacks[a] ){ return; }
		}
		ayoola.image.afterStateChangeCallbacks.push( callback );
	},
	
	//	Include what to do when status changed
	callAfterStateChangeCallbacks: function( fileId )
	{
	//		alert( ayoola.image.afterStateChangeCallbacks.length );
		for( var a = 0; a < ayoola.image.afterStateChangeCallbacks.length; a++ )
		{
			var callback = ayoola.image.afterStateChangeCallbacks[a];
			if( ! callback || typeof callback != 'function' || callback == ayoola.image.setStatus ){ continue; }
			callback( fileId );
		}
		ayoola.image.setStatus( fileId );
	},
	
	//	Sets status of a file
	setStatus: function( fileId ) 
	{
	//	alert( "" + fileId + 1 );
		if( ! ayoola.image.fieldName )
		{
		//	return false;
		}
/* 		ayoola.image.files[fileId].fieldName = ayoola.image.fieldName;
		ayoola.image.files[fileId].fieldNameValue = ayoola.image.fieldNameValue;
 */		var a = ayoola.image.files[fileId];
		var previewLivePicture = function()
		{
			var d = ayoola.form.elementValueChangeCallbacks[a.fieldName]; 
		//	alert( a.fieldName );
		//	alert( d );
			if( a.fieldNameValue == 'base' )
			{
			//	alert( a.fieldName );
			//	alert( d );
				return false;
			}
			if( d )
			{
				for( var f = 0; f < d.length; f++ )
				{
					d[f]();
				}
			}
		}
//		alert(  "" + fileId + 2 );
		//	PREVIEW
		do
		{
			//	We dont need it if response is ready
		//	alert( a.preview.node );
		//	alert(  "" + fileId + 2 );
			if( ! a || a.response || ! a.preview.node || a.preview.count ){ break; }
		//	if( ! a || a.response ){ break; }
			
	//		alert(  "" + fileId + 3 );
			var b = document.getElementsByName( ayoola.image.fieldName + "_preview_zone" );
			if( ! b ){ break; }
			
			//	ADD PREVIEW NODE TO STORAGE FOR LATER USAGE
			var reader = new FileReader();
			
			//	SOLVES MANY ISSUES WITH ayoola.image.fileCount BEING THE SAME BUILD PREVIEW
		//	var d = ayoola.image.fileCount;
			
		//	alert( d );
			var buildPreview = function( e1 )
			{
			//	alert( ayoola.image.fileCount );
			//	alert( i );
				var tempImg = new Image();
				tempImg.src = e1.target.result;
				tempImg.onload = function() 
				{
					var MAX_WIDTH = ayoola.image.thumbnail.resize.width;
					var MAX_HEIGHT = ayoola.image.thumbnail.resize.height;
					var tempW = tempImg.width;
					var tempH = tempImg.height;
					if( tempW > tempH ) 
					{
						if( tempW > MAX_WIDTH ) 
						{
						   tempH *= MAX_WIDTH / tempW;
						   tempW = MAX_WIDTH;
						}
					} 
					else 
					{
						if( tempH > MAX_HEIGHT ) 
						{
						   tempW *= MAX_HEIGHT / tempH;
						   tempH = MAX_HEIGHT;
						}
					}
					var canvas = document.createElement( 'canvas' );
					canvas.width = tempW;
					canvas.height = tempH;
					var ctx = canvas.getContext( "2d" );
					ctx.drawImage( this, 0, 0, tempW, tempH );
					var dataUrl = canvas.toDataURL( a.type );
				//	alert( resizedFile );		

					//	preview image
					var img = new Image();

					img.onload = function() 
					{
						var canvas = document.createElement( 'canvas' );
						canvas.width = ayoola.image.thumbnail.size.width;
						canvas.height = ayoola.image.thumbnail.size.height;
						var context = canvas.getContext( '2d' );
						// draw cropped image
						var sourceWidth = ayoola.image.thumbnail.size.width;
						var sourceHeight = ayoola.image.thumbnail.size.height;
						var destWidth = sourceWidth;
						var destHeight = sourceHeight;
						var destX = canvas.width / 2 - destWidth / 2;
						var destY = canvas.height / 2 - destHeight / 2;
						var sourceX = img.width / 2 - destWidth / 2;
					//	sourceX = sourceX < 0 ? 0 : sourceX;
						var sourceY = img.height / 2 - destHeight / 2;
					//	sourceY = sourceY < 0 ? ( img.height / 2 ) : sourceY;
						if( sourceX < 0 || sourceY < 0 )
						{
							sourceX = 0;
							sourceY = 0;
						//	alert( data );
						//	ajaxUpload( { data: data } );
							context.drawImage( img, 0, 0, sourceWidth, sourceHeight );
						}
						else
						{
	 						context.drawImage( img, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight );
						}
					//	context.drawImage( img, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight );
						var dataURL = canvas.toDataURL( a.type );
						a.preview.node.src = dataURL;
						a.preview.node.name = ayoola.image.fieldName + '_preview_photos';
						a.preview.node.className = 'normalnews boxednews';
						var selectMe = function()
						{
							//	toggle selection
							ayoola.div.selectElement( { element: a.preview.node, disableUnSelect: true, name: a.preview.node.name } );
							
							var b = document.getElementsByName( a.fieldName );
			//				if( ! b ){ break; }
							for( var c = 0; c < b.length; c++ )
							{
								//	alert( ayoola.image.fieldNameValue );
								//	alert( a.response.file_info[ayoola.image.fieldNameValue] );
								b[c].value = a.response.file_info[a.fieldNameValue];
								previewLivePicture();
							}
						}
						ayoola.events.add( a.preview.node, "click", selectMe );
					};
			 //     img.src = 'http://www.html5canvastutorials.com/demos/assets/darth-vader.jpg';
					img.src = dataUrl;
				}
				
				
			//	a.preview.node.src = e1.target.result;
				
				//	FIXING A BUG THAT IS NOT ALLOWING PREVIEW TO SHOW UNTIL THE NEXT UPLOAD
			//	ayoola.image.callAfterStateChangeCallbacks( ayoola.image.fileCount );
			}
			ayoola.events.add( reader, "load", buildPreview );
			reader.readAsDataURL( a.file );	

				
	//		alert(  "" + fileId + 4 );
		//	a.preview.node.style.width="100%";
			for( var c = 0; c < b.length; c++ )
			{
			//	alert(  "" + fileId + 5 );
				b[c].style.display = "block";
				b[c].appendChild( a.preview.node );
				
				//	avoid duplicate
				ayoola.image.files[fileId].preview.count++;
			}
		}
		while( false );
		
		//	INJECT FIELD-NAME
		do
		{
			if( ! a.fieldName || ! a.fieldNameValue )
			{
		//		return false;
			}
			if( ! a || ! a.response || ! a.response.file_info ){ break; }
					
			//	Close the splash screen
			ayoola.image.splashScreen.close ? ayoola.image.splashScreen.close() : null; 
//			ayoola.spotLight.close(); 
			if( a.formElement )
			{
				a.formElement.value = a.response.file_info[a.fieldNameValue];
				a.fieldName = a.formElement.id;
			}
			var b = document.getElementsByName( a.fieldName );
		//	if( ! b ){ break; }
			for( var c = 0; c < b.length; c++ )
			{
				//	alert( ayoola.image.fieldNameValue );
				//	alert( a.response.file_info[ayoola.image.fieldNameValue] );
				b[c].value = a.response.file_info[a.fieldNameValue];
			}
			previewLivePicture();
			
			//	Do this manually because of profile pictures
			var d = document.getElementsByName( a.fieldName + '_preview_zone_image' );
			
			for( var e = 0; e < d.length; e++ )
			{ 
			//	alert( target );
			//	alert( c[b] );
				d[e].onerror = function()
				{
					this.onerror  = function()
					{
						//	avoid infinite lookup if document fail
						this.onerror = null;
						this.src = '/open-iconic/png/document-8x.png';
					}
					this.src = '/open-iconic/' + ayoola.image.files[fileId].file.type.split( '/' ).shift() + '.png';
				}
				if( ayoola.image.files[fileId].file.type.match( 'image.*' ) ) 
				{
					d[e].src = a.response.file_info['dedicated_url'];
				}
				else
				{
					d[e].src = '/open-iconic/' + ayoola.image.files[fileId].file.type + '.png';
				}
			}
		}
		while( false );
	}
	
}
