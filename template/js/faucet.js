$("#CountDownTimer").TimeCircles({ time: { Days: { show: false }, Hours: { show: false } }});
$("#CountDownTimer").TimeCircles({count_past_zero: false}); 
$("#CountDownTimer").TimeCircles({fg_width: 0.05}); 
$("#CountDownTimer").TimeCircles({bg_width: 0.5}); 
$("#CountDownTimer").TimeCircles(); 
var time_left = $("#CountDownTimer").TimeCircles().getTime();  
setTimeout(function(){
		window.location.href = fauceturl;
}, time_left*1000);