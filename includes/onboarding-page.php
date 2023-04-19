<!DOCTYPE html>
<html>
<head>

</head>
<body>

  <h1>Onboarding</h1>

<button id="popup-btn">Onboarding</button>



  <script>


  // Get a reference to the button element
  const popupBtn = document.getElementById('popup-btn');

  // Add a click event listener to the button
  popupBtn.addEventListener('click', function() {

    // Call the open360DialogPopup function with the window location origin
    open360DialogPopup(window.location.origin);
  });  	

//	open360DialogPopup(window.location.origin);

	// `processParams` function retrieves the current URL search parameters and posts them to the parent window.
	// If there is an `opener` window, the function posts the parameters to it and closes the current window.
	function processParams() {
	  const params = window.location.search; // retrieve the current URL search parameters

	  // Check if there is an opener window
	  if (window.opener) {
	    window.opener.postMessage(params); // post the parameters to the opener window
	    window.close(); // close the current window
	  }
	}

	// `window.onload` event is used to trigger the execution of the `processParams` function
	// when the page has finished loading.
	window.onload = function() {
	  processParams();
	};

	// // `open360DialogPopup` function opens a new window with the specified URL and options
	// // and adds a message event listener to the current window.
	function open360DialogPopup(baseUrl) {
	//   //window.removeEventListener("message", receiveMessage); // remove any existing message event listeners

	  // Window options to be used in opening the new window
	  const windowFeatures = "toolbar=no, menubar=no, width=600, height=900, top=100, left=100";
	  const partnerId = "<?php echo get_option('atoomo_whatsapp_partner_id')?>";
	  const redirectUrl = "<?php echo get_option('atoomo_whatsapp_partner_redirect_url')?>"; // additional redirect if needed - if you don't want to use your 
	  // previously set partner redirect

	  // Open the new window with the specified URL
	  open(
	    "https://hub.360dialog.com/dashboard/app/" + partnerId + "/permissions?redirect_url=" + redirectUrl,
	    "integratedOnboardingWindow",
	    windowFeatures
	  );

	  // Add a message event listener to the current window
	  window.addEventListener("message", (event) => receiveMessage(event, baseUrl), false);
	}

	// `receiveMessage` function is the callback function that is executed when the message event is triggered.
	// It retrieves the data from the event, sets it as the search parameters of the current URL,
	// and returns if the origin of the event is not the same as the `baseUrl` or the type of `event.data` is an object.
	const receiveMessage = (event, baseUrl) => {
	  // Check if the event origin is not the same as `baseUrl` or `event.data` is an object.
	  if (event.origin != baseUrl || typeof event.data === "object") {
	    return;
	  }
	  const { data } = event; // retrieve the data from the event
	  const redirectUrl = `${data}`; // create a redirect URL from the data

		// var currentUrl = window.location.href;
		// var newUrl = window.location.search;


		// var currentUrl = redirectUrl;
		// var newUrl = window.location.search;

		// var separator = "?"; 
		// if (currentUrl.indexOf("?") !== -1) {
		//   separator = "&"; 
		// }
		// var newUrl = currentUrl + "&" + newUrl.substring(1); 

	  window.location.search = redirectUrl; // set the redirect URL as the search parameters of the current URL
	};
  </script>
  
</body>
</html>