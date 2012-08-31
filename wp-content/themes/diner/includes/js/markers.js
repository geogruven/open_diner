function createMarker(point,root,the_link,the_title,color) {
    								
	var baseIcon = new GIcon(G_DEFAULT_ICON);
	baseIcon.shadow = root + "/images/icons/shadow.png";
	
	var blueIcon = new GIcon(baseIcon);
	blueIcon.image = root + "/images/icons/blue-dot.png";
	  
	var redIcon = new GIcon(baseIcon);
	redIcon.image = root + "/images/icons/red-dot.png"; 
	
	var greenIcon = new GIcon(baseIcon);
	greenIcon.image = root + "/images/icons/green-dot.png";   
	
	var yellowIcon = new GIcon(baseIcon);
	yellowIcon.image = root + "/images/icons/yellow-dot.png";      		
	
	var tealIcon = new GIcon(baseIcon);
	tealIcon.image = root + "/images/icons/teal-dot.png"; 
	
	var blackIcon = new GIcon(baseIcon);
	blackIcon.image = root + "/images/icons/black-dot.png"; 
	
	var whiteIcon = new GIcon(baseIcon);
	whiteIcon.image = root + "/images/icons/white-dot.png"; 
	
	var purpleIcon = new GIcon(baseIcon);
	purpleIcon.image = root + "/images/icons/purple-dot.png"; 
	
	var pinkIcon = new GIcon(baseIcon);
	pinkIcon.image = root + "/images/icons/pink-dot.png"; 
	
	var customIcon = new GIcon(baseIcon);
	customIcon.image = color;
		
	//Do the work	
	if(color == 'blue')			{ markerOptions = { icon:blueIcon, title:the_title } } 
	else if(color == 'red')		{ markerOptions = { icon:redIcon, title:the_title } } 
	else if(color == 'green')	{ markerOptions = { icon:greenIcon, title:the_title } } 
	else if(color == 'yellow')	{ markerOptions = { icon:yellowIcon, title:the_title } } 
	else if(color == 'teal')	{ markerOptions = { icon:tealIcon, title:the_title } } 
	else if(color == 'black')	{ markerOptions = { icon:blackIcon, title:the_title } } 
	else if(color == 'white')	{ markerOptions = { icon:whiteIcon, title:the_title } } 
	else if(color == 'purple')	{ markerOptions = { icon:purpleIcon, title:the_title } } 
	else if(color == 'pink')	{ markerOptions = { icon:pinkIcon, title:the_title } }
	else { markerOptions = { icon:customIcon, title:the_title }  }
	
	var marker = new GMarker(point, markerOptions);
	 	
	GEvent.addListener(marker, "click", function() {
	  window.location = the_link;
	});
	  
	return marker;
}