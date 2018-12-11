var imperialCountdown = {
	
	init: function (postID, countdownSeconds ) {
		
		
		var timerDivID = "imperialCountdownTimer"+postID;
		var feedbackDivID = "imperialCountdownTimerFeedback"+postID;		

		var countDownDate = new Date();
		console.log("countDownDate = "+countDownDate);
		// modify the countdown date				
		countDownDate.setSeconds(countDownDate.getSeconds() + countdownSeconds);		
		console.log("countDownDate = "+countDownDate);
		

		// Update the count down every 1 second
		var x = setInterval(function() {

		  // Get todays date and time
		  var now = new Date().getTime();

		  // Find the distance between now and the count down date
		  var distance = countDownDate - now;

		  // Time calculations for days, hours, minutes and seconds
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  
		  if(seconds<10){seconds = "0"+seconds;}
		  if(minutes<10){minutes = "0"+minutes;}
		  // Display the result in the element with id="demo"
		  document.getElementById(timerDivID).innerHTML = "<div>"+minutes+"</div><div> : </div><div>"+seconds+"</div><div>minutes</div><div></div><div>Seconds</div>";

		  // If the count down is finished, write some text 
		  if (distance < 0) {
			clearInterval(x);

			  jQuery('#'+timerDivID).hide();
			  jQuery('#'+feedbackDivID).show();
			
			
			
		  }
		}, 1000);
	}	
}

