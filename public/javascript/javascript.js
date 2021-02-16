(function ($)
{
	homepage = function (input_val)
	{
		var message = "Please enter a domain.";

		// Reset the form
		$("#submit").attr("disabled", false);
		$("#input").attr("disabled", false).css("color", "#AAA");

		// Clears the default input value on click
		// Changes the colour of text input
		$("#input").on("focus", function ()
		{
			if ($(this).val() == input_val)
			{
				$(this).val("");
			}

			$(this).css("color", "#36393D");
		});

		// Hijack form submission
		$("#form").on("submit", function ()
		{
			// Retrieve the value from the input field
			var url = $("#input").val();

			// Only if the url contains something we can submit
			if (url && url != message)
			{
				// Change the browser's url, adding only the domain
				window.location = "/" + url.replace(/^[ \s]+|[ \s]+$|http(s)?:\/\/|\/(.*)/g, "")
										   .toLowerCase();
			}
			else
			{
				$("#input").val(message).css("color", "#AAA");

				input_val = message;
			}

			// Return false if input is empty
			return false;
		});
	};
})(jQuery);
