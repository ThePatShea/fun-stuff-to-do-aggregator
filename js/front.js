var postLoad          =  20;
var totalBubbles      =  8;
var checkMobileCount  =  0;
var currentBubble     =  0;
var numSelected       =  0;
var lastTime          =  0;
var midQuery          =  0;
var loading           =  0;
var start             =  0;
var shiftOffset       =  0;
var shiftSection      =  0;
var longestHeight     =  [];
var heightCount       =  0;
var urlPrefix         =  "";
var urlPostfix        =  "";


function trackLinkClick(url)
{
	mixpanel.track("User clicked a link to the url: " + url);
}

function trackAction(action, id)
{
	mixpanel.track(action + " " + id);
}

function startModalLoader()
{
	$('#loadModal').fadeIn('medium');
}

function stopModalLoader()
{
	$('#loadModal').fadeOut('medium');
}

function selectorClick()
{
	if ( $(this).scrollTop() >= $("#phase1").height() )
	{
		$('body,html').clearQueue();
		$('body,html').animate({scrollTop : 0},'slow');
	}  
	else
	{
		$('body,html').clearQueue();
		$('body,html').animate({scrollTop : $("#phase1").height()},'slow');
	}
	
	mixpanel.track("User clicked the selector");
}

function shiftSmallBubbles(direction)
{	
	//if there's already an animation happening, it just ignores the new scroll
		var n       =  $("#smallBubble_1").queue("fx");
  		var length  =  n.length;
  		
  		if (length>0)  return;

	
	var overlap            =  22;
    var smallBubble_width  =  $("#smallBubble_1").width();
    var smallBubble_space  =  smallBubble_width - overlap;
    var selectorLeft       =  $("#selector").offset().left;
    var numBubbles         =  Math.floor( (selectorLeft + (overlap * 5)) / smallBubble_space);
    var maxBubbles         =  (numBubbles * 2);
    
    var numSections        =  Math.floor((totalBubbles-1)/numBubbles) - 1;
    
    $("#conveyorShift_prev").removeClass("noClick_prev");
    $("#conveyorShift_next").removeClass("noClick_next");
    
    if (direction == "left")
    {
    	if (shiftSection >= (numSections))
		{
			$("#conveyorShift_next").addClass("noClick_next");
			return;
		}
		
    	var directionMultiplier = 1;
    }
	else
	{
		if (shiftSection <= 0)
		{
			$("#conveyorShift_prev").addClass("noClick_prev");
			return;
		}
		
		var directionMultiplier = -1;
	}
    
    shiftSection += directionMultiplier;
    shiftOffset++;
    
    if (shiftSection <= 0)
	{
	    $("#conveyorShift_prev").addClass("noClick_prev"); // Prevent glitch where noClick class isn't added until you try to click the button once
	}
	
	if (shiftSection >= (numSections))
	{
	    $("#conveyorShift_next").addClass("noClick_next");
	}
    
	var shiftMultiplier    =  shiftOffset * numBubbles;
	var selectorRight      =  $("#selector").offset().left + $("#selector").width();
	var extraSpace         =  selectorLeft  - ((numBubbles) * smallBubble_space) + overlap;
	var move               =  selectorRight + extraSpace;	
	
	for (i=1; i <= totalBubbles; i++)
    {
    	var newLeft = $("#smallBubble_"+i).offset().left - (directionMultiplier * move);
    	
    	
    	// CHANGE 4 TO MORE DYNAMIC NUMBER
    	if ( (i - shiftMultiplier) > numBubbles)
    	{
    		newLeft = $(window).width() - (directionMultiplier * (( (smallBubble_width * ( totalBubbles-(i-shiftMultiplier)+1 ) ) - (overlap * (totalBubbles-(i-shiftMultiplier)+1)) ) + 5));
    		
    		if (totalBubbles > maxBubbles)
    		{    			
    			newLeft += ((totalBubbles - maxBubbles) * smallBubble_space);	
    		}
    	}
    	
    	$("#smallBubble_"+i).clearQueue();
    	$("#smallBubble_"+i).animate({left : newLeft+'px'},'slow');    	
    }

	
	
	mixpanel.track("User shifted the smallBubbles in the conveyor: " + direction);
}

function positionSmallBubbles()
{ 	
 	$("#conveyorShift_prev").addClass("noClick_prev");
 	
 	
 	var selectorLeft   =  $("#selector").offset().left;
 	var overlap            =  22;
    var smallBubble_width  =  $("#smallBubble_1").width();
    var smallBubble_space  =  smallBubble_width - overlap;
    var numBubbles         =  Math.floor( (selectorLeft + (overlap * 5)) / smallBubble_space);
    
    
    if (numBubbles < 1)
    {
    	numBubbles = 1;
    }
    
    var maxBubbles         =  (numBubbles * 2);
    
    
    if   (maxBubbles > totalBubbles)  var lastBubble  =  totalBubbles - 1;
    else                              var lastBubble  =  maxBubbles   - 1;
    
    
    for (i=1; i <= totalBubbles; i++)
    {
    	var lastBubbleID   =  $("#smallBubble_"+lastBubble);
    	var newLeft        =  (smallBubble_width * (i-1) ) - (overlap * i);
    	var maxRight       =  (lastBubbleID.offset().left) + lastBubbleID.width();
    
    
    	if (i > numBubbles && $(window).width() > 750)
    	{
    		newLeft = $(window).width() - ( (smallBubble_width * ( totalBubbles-i+1 ) ) - (overlap * (totalBubbles-i+1)) ) + 5;
    		
    		if (totalBubbles > maxBubbles)
    		{    			
    			newLeft += ((totalBubbles - maxBubbles) * smallBubble_space);	
    		}
    	}
    
    	$("#smallBubble_"+i).css({ 'left': newLeft+'px', 'z-index': overlap - i });
    }
    
}

function generateSlider()
{
	url = urlPrefix + 'php/ajax.php?do=generateSlider';
	
	$.ajax(url)
	.done(  function(data) { $("#slider").html(data);                })
    .always(function()     { $('#featured').orbit(); checkMobile();	 });
	
}


function generateArrows(bubbleTag)
{
	url = urlPrefix + 'php/ajax.php?do=generateArrows&bubbleTag='+bubbleTag;
    
    $.ajax(url).done(function(data) { $("#buttonOverlay").html(data); });
}

function loadIntoModal(bubble, bubbleID)
{
	startModalLoader();
	url = urlPrefix + 'php/ajax.php?do=getPostInfo&bubble='+bubble+'&bubbleID='+bubbleID;
    
    $.ajax(url)
	.done(  function(data) { $("#modalContainer").html(data);    stopModalLoader();})
    .always(function()     { createScrollBars(); });
    
    
    mixpanel.track("User loaded a new post into the modal window: " + bubble);
}

function createScrollBars()
{
	 	$('#postDescription').slimScroll({
	    		  height: '450px',
	    		  width: '445px',
	    		  alwaysVisible: false,
				  start: 'top',
	    		  wheelStep: 10,
	    		  size: '7px',
	    		  distance: 1,
	    		  opacity: .7
	   	  });
	    	  
	    $('#commentContentWrapper').slimScroll({
	    		  height: '400px',
	    		  width: '270px',
	    		  alwaysVisible: false,
				  start: 'top',
	    		  wheelStep: 10,
	    		  size: '7px',
	    		  distance: 3,
	    		  opacity: .7
	    	  });	  
		
	    	  		  
}


function getBubbles()
{
    newBubble = 1;
    
    
	
	
	url = urlPrefix + "php/ajax.php?do=getSelector";
	
	$.ajax(url)
	.done(  function(data) { $("#selContainer").html(data);    })
	.always(function()
	{
		url = urlPrefix + "php/ajax.php?do=getBubbles";
		
		$.ajax(url)
		.done(  function(data) { $("#bs").html(data);    })
    	.always(function()
    	{
    		checkMobile();
    		activateBubbleHover();
    		
    		stopModalLoader();
    		$('#phase1').animate({'opacity': 1}, 'slow');
			$('#phase2').animate({'opacity': 1}, 'slow');
			
			positionSmallBubbles();
    	});
	});
	
	
	
        
}

function switchBubble(bubbleTag)
{	
	if (currentBubble != bubbleTag)
	{
		//populateFollowBar(bubbleTag);
		generateArrows(bubbleTag);
		populateBubble(bubbleTag);
		populateCoverPhotos(bubbleTag);
		populateSelector(bubbleTag);
		
		mixpanel.track("User switched to the bubble: " + bubbleTag);
	}
	else
	{
		$('body,html').clearQueue();
		$('body,html').animate({scrollTop : $("#phase1").height()},'slow');		
	}
}


function populateSelector(bubbleTag)
{
	if      (bubbleTag == "ee14bc9e-d5ed-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 7px;'>";
			selectorHTML      +=  "<div style='font-size: 22px;'>night</div>";
			selectorHTML      +=  "<div style='font-size: 34px; position: relative; top: 10px;'>life</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "ee14c716-d5ed-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 7px;'>";
			selectorHTML      +=  "<div style='font-size: 21px;'>greek</div>";
			selectorHTML      +=  "<div style='font-size: 34px; position: relative; top: 10px;'>life</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "ee14d044-d5ed-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 10px;'>";
			selectorHTML      +=  "<div style='font-size: 25px;'>emory</div>";
			selectorHTML      +=  "<div style='font-size: 14px; position: relative; top: 4px; left: -1px;'>academics</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "ee14d968-d5ed-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 12px;'>";
			selectorHTML      +=  "<div style='font-size: 16px;'>atlanta</div>";
			selectorHTML      +=  "<div style='font-size: 21px; position: relative; top: 4px;'>sports</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "ee14ab0a-d5ed-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 16px;'>";
			selectorHTML      +=  "<div style='font-size: 30px;'>get</div>";
			selectorHTML      +=  "<div style='font-size: 21px; position: relative; top: 9px;'>food</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "138814bc-d5ee-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 2px;'>";
			selectorHTML      +=  "<div style='font-size: 17px;'>atlanta</div>";
			selectorHTML      +=  "<div style='font-size: 31px; position: relative; top: 8px;'>bars</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "1385e7e6-d5ee-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 10px;'>";
			selectorHTML      +=  "<div style='font-size: 26px;'>night</div>";
			selectorHTML      +=  "<div style='font-size: 23px; position: relative; top: 6px; left: -1px;'>clubs</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "2809077e-d5ef-11e1-b249-002590605566")
	{
		var selectorHTML   =  "<div style='position: relative; top: 14px;'>";
			selectorHTML      +=  "<div style='font-size: 14px;'>concerts</div>";
			selectorHTML      +=  "<div style='font-size: 18px; position: relative; top: 0px;'>&shows</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "713d4d27-ed49-11e1-bf61-aafbeaa37357")
	{
		var selectorHTML   =  "<div style='position: relative; top: 9px;'>";
			selectorHTML      +=  "<div style='font-size: 18px;'>weekend</div>";
			selectorHTML      +=  "<div style='font-size: 18px; position: relative; top: 6px;'>escapes</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "e099ace7-ed4b-11e1-bf61-aafbeaa37357")
	{
		var selectorHTML   =  "<div style='position: relative; top: 15px;'>";
			selectorHTML      +=  "<div style='font-size: 14px;'>freshmen</div>";
			selectorHTML      +=  "<div style='font-size: 19px; position: relative; top: 2px;'>events</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "49438594-ed4c-11e1-bf61-aafbeaa37357")
	{
		var selectorHTML   =  "<div style='position: relative; top: 13px;'>";
			selectorHTML      +=  "<div style='font-size: 18px;'>campus</div>";
			selectorHTML      +=  "<div style='font-size: 19px; position: relative; top: 4px; left: -1px;'>events</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "8475045d-ed4f-11e1-bf61-aafbeaa37357")
	{
		var selectorHTML   =  "<div style='position: relative; top: 12px;'>";
			selectorHTML      +=  "<div style='font-size: 16px;'>student</div>";
			selectorHTML      +=  "<div style='font-size: 23px; position: relative; top: 4px;'>deals</div>";
		selectorHTML      +=  "</div>";
	}
	else if (bubbleTag == "647659d4-ed50-11e1-bf61-aafbeaa37357")
	{
		var selectorHTML   =  "<div style='position: relative; top: 9px;'>";
			selectorHTML      +=  "<div style='font-size: 18px;'>outdoor</div>";
			selectorHTML      +=  "<div style='font-size: 13px; position: relative; top: 5px;'>adventures</div>";
		selectorHTML      +=  "</div>";
	}
	
	document.getElementById('selectorText_bottom').innerHTML = selectorHTML;
	document.getElementById('selectorText_top').innerHTML = selectorHTML;
}

function populateCoverPhotos(bubbleTag)
{
	if      (bubbleTag == "647659d4-ed50-11e1-bf61-aafbeaa37357") backgroundURL = "img/cover_photo/outdoor_adventures.jpg";
	else if (bubbleTag == "713d4d27-ed49-11e1-bf61-aafbeaa37357") backgroundURL = "img/cover_photo/weekend_escapes.jpg";
	else if (bubbleTag == "49438594-ed4c-11e1-bf61-aafbeaa37357") backgroundURL = "img/cover_photo/campus_events.jpg";
	else if (bubbleTag == "8475045d-ed4f-11e1-bf61-aafbeaa37357") backgroundURL = "img/cover_photo/student_deals.jpg";
	else if (bubbleTag == "1385e7e6-d5ee-11e1-b249-002590605566") backgroundURL = "img/cover_photo/night_clubs.jpg";
	else if (bubbleTag == "ee14bc9e-d5ed-11e1-b249-002590605566") backgroundURL = "img/cover_photo/night_life.jpg";
	else if (bubbleTag == "ee14c716-d5ed-11e1-b249-002590605566") backgroundURL = "img/cover_photo/greek_life.jpg";
	else if (bubbleTag == "e099ace7-ed4b-11e1-bf61-aafbeaa37357") backgroundURL = "img/cover_photo/freshmen.jpg";
	else if (bubbleTag == "2809077e-d5ef-11e1-b249-002590605566") backgroundURL = "img/cover_photo/concerts.jpg";
	else if (bubbleTag == "ee14d968-d5ed-11e1-b249-002590605566") backgroundURL = "img/cover_photo/sports.jpg";
	else if (bubbleTag == "ee14ab0a-d5ed-11e1-b249-002590605566") backgroundURL = "img/cover_photo/food.jpg";
	else if (bubbleTag == "138814bc-d5ee-11e1-b249-002590605566") backgroundURL = "img/cover_photo/bars.jpg";
		
	$('.smallestBubbleBG').css({'background-image': 'url("'+backgroundURL+'")'});
	$('.cover_photo').css({'background-image': 'url("'+backgroundURL+'")'});
}


function populateFollowBar(bubbleTag)
{
	url = urlPrefix + "php/ajax.php?do=followBar&bubble="+bubbleTag;
    	
	$.ajax(url).done(function(data) { $("#followBar").html(data); });
}



function populateBubble(bubbleTag)
{		
	if (currentBubble != bubbleTag)
	{
	    document.getElementById('tiles').innerHTML="";
	    start = 0;
	}
	
	currentBubble = bubbleTag;
	
	if ($(this).scrollTop() < $(window).height())
    {
        $('#pageContainer').removeClass('no_scroll');
        
        $('body,html').clearQueue();
	    $('body,html').animate({scrollTop : $("#phase1").height()},'slow');
	}
	
	// Prevents loading too much when scrolling
		var currentTime = Math.round((new Date()).getTime() / 1000);
		if (currentTime < (lastTime + 1))
		{
			// Create a new layout handler.
    		   activateWookmark();
			
			return;
		}
		
		lastTime  =  currentTime;

	if (start == -1) return;
	
	loading = 1;
	setTimeout(function()
	{
		if (loading == 1) document.getElementById('load').innerHTML="<img class='ajaxLoader' src='img/other/ajax_loader.gif'/>";
		
	},1000);
    url = urlPrefix + "php/ajax.php?do=populate&start="+start+"&bubble="+bubbleTag+"&postLoad="+postLoad;
    
    start  +=  postLoad;
  
    $.ajax(url)
	.done(function(data)
	{
		loading = 0;
    	document.getElementById('load').innerHTML="";
    	    	
    	if(data == "")
    	{
    	    start = -1;    		
    	    return;
    	}
    	
    	var item = $(data).hide().fadeIn('slow');
		$('#tiles').append(item);
    	
        
        // Create a new layout handler.
            activateWookmark();
        
        
        // Apply mobile or web properties
        	checkMobile();

	});
  
}

    
    
    // Prepare wookmark layout options.
    	var handler         =  null;
    	var wookMarkOffset  =  30;
    	
    	var options = {
    	  autoResize:  true,            // This will auto-update the layout when the browser window is resized.
    	  offset:      wookMarkOffset,  // Optional, the distance between grid items
    	  itemWidth:   280,             // Optional, the width of a grid item
    	  resizeDelay: 1000				// Optional, delays the re-sizing
    	};

   
  /*
    // Call the layout function.
   		$(document).ready(new function()
   		{
   			activateWookmark();
   		});
  */

   	function activateWookmark()
   	{   		
    	$('#tiles li').wookmark(options);
    
    /*
    	heightCount  =  0;
    	
    	if(handler) handler.wookmarkClear(); // Clears previous layout handler.
    	
    	for (i=0; i <= 8; i++)
    	{
    		$('#tileSection'+i+' li').wookmark(options);
    	}
    	
    	repositionWookmark();
   	*/
   	}

	function repositionWookmark()
	{		
	/*
		for (i=0; i <= 8; i++)
    	{
    		if (i != 0)   newTop     +=  longestHeight[i-1] + wookMarkOffset + 150;
    		else          var newTop  =  110;
    		
    		$('#tileSection'+i).css({'top': newTop+'px'});
    	}
	*/
	}

	$(function()
	{
	   $(window).resize(function()
	   {
	   		positionSmallBubbles();
	   		checkMobile();
	   		
	   		shiftSection  =  0;
	   		shiftOffset   =  0;
	   		
	   		
	   		mixpanel.track("User re-sized the browser window");
	   });
	});


	var y_prev = 0;
	
	$(function (top)
	{
	  var msie6 = $.browser == 'msie' && $.browser.version < 7;
	  
	  if (!msie6) {
	    
	    $(window).scroll(function (event) {
	   	  	   	
	   	  	top = $("#phase1").height() + 160;
	   	  	  
	   		// Adds more postBoxes when user scroll near bottom
	   			var closeToBottom = ( ($(window).scrollTop() + $(window).height()) > ($(document).height() - $(window).height() - 500) );
	   			
    			if(closeToBottom && currentBubble != 0 && $(window).scrollTop() > top )
    			{
    			    populateBubble(currentBubble);
    			    
    			    mixpanel.track("User scrolled down far enough to load new content into the bubble: " + currentBubble);
    			}
	   			
	   		
	   		// Activates the followBar when the user scrolls past the cover photo in phase 2
	    		var y = $(this).scrollTop();
	    		
	    		// whether that's below the form
	    		if (y >= top) {
	    		  // if so, ad the fixed class
	    		  $('#followBar').addClass('fixed');
	    		} else {
	    		  // otherwise remove it
	    		  
	    		  $('#followBar').removeClass('fixed');
	    		}
	      
	    });
	  }  
	});


// Causes the page to scroll back to the top on refresh
	$(window).unload(function()
	{
    	$('body').scrollTop(0);
    	
    	mixpanel.track("User left the site or refreshed the page");
	});


	function startModalKeyListener() {
		//if(navigator.userAgent.toLowerCase().indexOf("firefox") != -1)
			
		
    	if (document.addEventListener) {
    	    document.addEventListener("DOMMouseScroll", scrolledInModal, false);
			document.addEventListener("touchmove", touchMove, false);
    	    document.addEventListener("keydown", keyPressedInModal, false);
    	}
    	document.onmousewheel = scrolledInModal;
    	document.onscroll = scrolledInModal;
    	document.ontouchmove = touchMove;
    	document.onkeydown = keyPressedInModal;
	}

	function stopModalKeyListener() {
	    if (document.removeEventListener) {
	        document.removeEventListener("DOMMouseScroll", scrolledInModal, false);
			document.removeEventListener("touchmove", touchMove, false);
	        document.removeEventListener("keydown", keyPressedInModal, false);
	    }
	    document.onmousewheel = null;
	    document.onscroll = null;
	    document.ontouchmove = null;
	    document.onkeydown = null;
	}
	
	function scrolledInModal(event) {
	    var scrollTgt = 0;
	    event = window.event || event;
	    if (event.detail) {
	        scrollTgt = -40 * event.detail;
	    } else {
	        scrollTgt = event.wheelDeltaY;
	    }
		
		//this used to manually move the content based on scroll events
		//replaced by slimScroll
	    /*if (scrollTgt) {
	    	if(event.target.nodeName == 'PRE' || event.target.parentNode.nodeName == 'PRE')
	    		$('#descriptionContainer').scrollTop($('#descriptionContainer').scrollTop() - (scrollTgt/3));
	    	else if(event.srcElement.nodeName == 'PRE' || event.srcElement.parentNode.nodeName == 'PRE')	//internet explorer
	    		$('#descriptionContainer').scrollTop($('#descriptionContainer').scrollTop() - (scrollTgt/3));
	    	
	    	else if(event.target.className == 'postComment' || event.target.parentNode.parentNode.className == 'postComment' ||event.target.parentNode.parentNode.parentNode.className == 'postComment' || event.target.parentNode.className == 'commentDetailBox')
	    		$('#modalCommentDetailBox').scrollTop($('#modalCommentDetailBox').scrollTop() - (scrollTgt/3));
	    		
	    }*/
		preventDefault(event);
		
		mixpanel.track("User scrolled while in the modal window");
	}
	
	
		
	function touchMove(event){

		if(event.target.parentNode.className == 'descriptionContainer')
		{
			
		}
		else if(event.target.className == 'postComment' || event.target.parentNode.parentNode.className == 'postComment' ||event.target.parentNode.parentNode.parentNode.className == 'postComment' || event.target.parentNode.className == 'commentDetailBox')
		{
			
		}
		else
		{
			preventDefault(event);
		}
		
	}
			
	
	
	function preventDefault(event) {
	    event = event || window.event;
	    if (event.preventDefault) {
	        event.preventDefault();
	    }
	    event.returnValue = false;
	}
	
	
	function keyPressedInModal(event) {
  		var keyCode = event.keyCode;
  		
  		//disable up and down buttons
  		if(keyCode == 38 || keyCode == 40)
  			preventDefault(event);
  			
  		//left click
  		else if(keyCode == 37){
  			$('.prev').click();
  			
  			mixpanel.track("User pressed the left arrow key in the modal window, which loaded the previous post");
   		}
   		
   		//right click
   		else if(keyCode == 39){
  			$('.next').click();
  			
  			mixpanel.track("User pressed the right arrow key in the modal window, which loaded the next post");
   		}
  		
  		
  		//up = 38, down = 40, left = 37, right = 39
	}
	
	
		
	function modalWindowOpenJS()
	{
		stopKeyListener();				//stop listening for changeBubble
		startModalKeyListener();		//start listening for changePost
		
		createScrollBars();
		
		stopModalLoader();
	}
	function modalWindowCloseJS()
	{
		stopModalKeyListener();
		startKeyListener();
		
		stopModalLoader();	//just in case the user quits out before it's done loading
	}
	
	
	
	function startKeyListener()
	{
		if (document.addEventListener) {
    	    document.addEventListener("keydown", keyPressed, false);
    	    document.addEventListener("DOMMouseScroll", scrolled, false);
		}
    	document.onmousewheel = scrolled;
    	document.onkeydown = keyPressed;
	}
	
	function stopKeyListener()
	{
		if (document.removeEventListener) {
	        document.removeEventListener("keydown", keyPressed, false);
	        document.removeEventListener("DOMMouseScroll", scrolled, false);
	    }
	   	document.onmousewheel = null;
	    document.onkeydown = null;
	}
	
	
	//scrolled when not in the modal window
	function scrolled(event) {
	    var scrollTgt = 0;
	    event = window.event || event;
	    if (event.detail) {
	        scrollTgt = -40 * event.detail;
	    } else {
	        scrollTgt = event.wheelDeltaY;
	    }

		
				
		//if there's already an animation happening, it just ignores the new scroll
		var n = $('body').queue("fx");
  		var length = n.length;
  		if(length>0){
  			preventDefault(event);
  			return;
  		}
  		
		if($(window).scrollTop() <= $("#phase1").height() && 
			//$(window).scrollTop() > ($("#phase1").height()*.75) && 
			scrollTgt > 5)
		{
			
			$('body,html').clearQueue();
			$('body,html').animate({scrollTop : 0},'slow');
			preventDefault(event);
			
		}
		else if($(window).scrollTop() > -5 &&
				$(window).scrollTop() < ($("#phase1").height()*.75) && 
				scrollTgt < -5)
		{
			$('body,html').clearQueue();
			$('body,html').animate({scrollTop : $("#phase1").height()},'slow');
			preventDefault(event);
			
		}
		
		
		if(scrollTgt > 0  &&  $(window).scrollTop() < $("#phase1").height())
			preventDefault(event);
		if(scrollTgt < 0  &&  $(window).scrollTop() < $("#phase1").height()-50)
			preventDefault(event);
		
		mixpanel.track("User scrolled while in the main window");
	}
	
	
	//key pressed when NOT in modal window
	function keyPressed(event)
	{	
		var keyCode = event.keyCode;
  		
   		//left click
  		if(keyCode == 37){
  			if($(window).scrollTop() >= $("#phase1").height()-20)
  				$('#prevMain').click();
  			else
  				$('#conveyorShift_prev').click();
  			
  			
  			mixpanel.track("User pressed left arrow on keyboard");
   		}
   		
   		//right click
   		else if(keyCode == 39){
   			if($(window).scrollTop() >= $("#phase1").height()-20)
  				$('#nextMain').click();
  			else
  				$('#conveyorShift_next').click();
  			
  			
  			mixpanel.track("User pressed right arrow on keyboard");
   		}
   		
   		//up
   		else if(keyCode == 38){
   			if($(window).scrollTop() <= $("#phase1").height()){
   				$('body,html').clearQueue();
				$('body,html').animate({scrollTop : 0},'slow');
				preventDefault(event);
   			}
   			
   			mixpanel.track("User pressed up arrow, which scrolled the user up");
   		}
   		
   		//down
   		else if(keyCode == 40){
   			if($(window).scrollTop() > -5 &&
				$(window).scrollTop() < ($("#phase1").height()*.75)){
   				$('body,html').clearQueue();
				$('body,html').animate({scrollTop : $("#phase1").height()},'slow');
				preventDefault(event);
   			}
   			
   			mixpanel.track("User pressed down arrow, which scrolled the user down");
   		}
	}
	
	
	function activatePath()
	{
		var pathArray  =  window.location.pathname.split( '/' );
 		var path       =  pathArray[1];
 		
 		mixpanel.track("User loaded the home page");
 		
 		
		$('#pageContainer').addClass('no_scroll');
		
		startKeyListener();
 		
 		//urlPrefix   =  "http://emorybubble.com/"; // SHOULD ONLY APPLY TO MOBILE
		urlPostfix  =  "&mobile=true"; // SHOULD ONLY APPLY TO MOBILE
		
		generateSlider();
		checkMobile();
		getBubbles();
		
 		if (path == "uber")
 		{
		    startModalLoader(); TINY.box.show({url: urlPrefix +'php/ajax.php?do=getPostInfo&bubble=54c85d09-f0c0-11e1-8433-db05838510c0&bubbleID=8475045d-ed4f-11e1-bf61-aafbeaa37357',boxid:'bubblebox',width:950,height:500,fixed:true,maskid:'texturemask',animate:false,openjs:function(){modalWindowOpenJS()},closejs:function(){modalWindowCloseJS()}});
		    
		    mixpanel.track("User loaded emorybubble.com/uber");
 		}
		
	}
	

	function checkMobile()
	{		
		//Checks if the browser is an iPhone, to add height to phase1 to offset the height of the url bar
			var agent = navigator.userAgent.toLowerCase();

			
			if (agent.indexOf('iphone') != -1)
			{				
				var morePhase1Height = 60;				
			}
			else
			{
				var morePhase1Height = 0;
			}
		
			var windowHeight = $(window).height() + morePhase1Height;
			
		
		
		$('#pageContainer').css({'height': windowHeight + 'px'});
		$('#phase2').css({'min-height': windowHeight + 'px'});
		$('#phase1').css({'height': windowHeight + 'px'});
		
		
		
		if ($(window).width() < 750)
		{
			$("#scroller").css({width : ((121 * totalBubbles) - 30)+'px'});
			
			
			$('.orbit-wrapper').css({"width": "0px"});
			$('#featured').css({"width": "0px"});
			
			
			$('#conveyorShift_prev').css({"opacity": 0, "z-index": -50});
			$('#conveyorShift_next').css({"opacity": 0, "z-index": -50});
			$('#featuredContainer').css({"opacity": 0});
			$('#featured_mobile').css({"opacity": 1});
		
		
			
			
			$('#buttonOverlay').css({"display": "none"});
			$('.coverOverlay').css({"top": "-224px"});
			$('#cover').css({"height": "114px"});
			$('.coverBG').css({"background-size": "250%"});
			
			$('#shiftUpContainer').css({"display": "block"});
			$('#selector').css({"display": "none"});
			
			
			
		
			if (windowHeight > 410)
			{
				$('#featured_mobile_text').css({"opacity": 1});
			}
			else
			{
				$('#featured_mobile_text').css({"opacity": 0});
			}
			
			
			
			postLoad  =  6;
		}
		else
		{
			$("#scroller").css({width : '100%'});
			
			
			$('#buttonOverlay').css({"display": "block"});
			$('.coverOverlay').css({"top": "-160px"});
			$('#cover').css({"height": "178px"});
			$('.coverBG').css({"background-size": "100%"});
			
			
			$('#shiftUpContainer').css({"display": "none"});
			$('#selector').css({"display": "block"});
			
			
			
			
						
			
			$('.orbit-wrapper').css({"width": "750px"});
			$('#featured').css({"width": "750px"});
			
			
			$('#conveyorShift_prev').css({"opacity": 1, "z-index":  50});
			$('#conveyorShift_next').css({"opacity": 1, "z-index":  50});
			
			$('#featuredContainer').css({"opacity": 1});
			$('#featured_mobile').css({"opacity": 0});
			
						
			
		
			if ($(window).height() > 325)
			{
			    $('#featuredContainer').css({"opacity": 1});
			}
			else
			{
				$('#featuredContainer').css({"opacity": 0});
			}
			
			
			
			postLoad  =  20;
		}
		
		if ($(window).height() > 325)
		{
		    $('#tb').css({"opacity": 1});
		}
		else
		{
		    $('#tb').css({"opacity": 0});
		}
	}
	
	function activateBubbleHover()
	{
		$(".bubble").mouseenter(
			function()
			{
				$(this).addClass("bubbleHover");
			}
		);
		
		$(".bubble").mouseleave(
			function()
			{
				$(this).removeClass("bubbleHover");
			}
		);
		
		$(".bubble").click(
			function()
			{
				$(".bubble").removeClass("bubbleActive");
				$(this).addClass("bubbleActive");
			}
		);
	}
	
	
	function calibrateScrollTop()
	{
 		var fullPhase1  =  $("#phase1").height();
 		var halfPhase1  =  fullPhase1 / 3;
 		
 		var windowTop   =  $(window).scrollTop();
 		
 		if (halfPhase1 > windowTop && windowTop > 0)
 		{
 		    $('body,html').clearQueue();
		    $('body,html').animate({scrollTop : 0},5000);
 		}
 		else if (fullPhase1 > windowTop && windowTop >= halfPhase1)
 		{
 		    $('body,html').clearQueue();
		    $('body,html').animate({scrollTop : 700},'slow');
 		}
	}
	
	
	function activate_iScroll()
	{
		function start_iScroll(){ var myScroll = new iScroll('conveyorWrapper'); }
		document.getElementById('conveyorWrapper').addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
		document.addEventListener('DOMContentLoaded', start_iScroll, false);
		
    	$("#scroller").css({width : ((121 * totalBubbles) - 30)+'px'});
	}
	
	
	$(document).ready(function() 
	{
		activatePath();
	});

	startModalLoader();
	
	activate_iScroll();
	
	
	
	