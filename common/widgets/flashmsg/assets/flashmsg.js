/* "Very simple messages" by Marco Del Tongo <info@marcodeltongo.com> - Public Domain */
(function($){

	$fc = $("#flash-messages-container");
	$msgs = $(".flash-message", $fc);
	$numMsgs = $msgs.length;
	$currMsg = 0;

	function showFlashMsg(item) {
		$($msgs[item]).fadeIn(function() {
			if (item < $numMsgs) {
				showFlashMsg(item + 1);
			} else {
				window.setTimeout("hideFlashMsg("+$numMsgs+")", 3000);
			}
		});
	}

	if ($numMsgs > 0) {
		$fc.show();

		$numMsgs--;
		showFlashMsg(0);
	}
})(jQuery);

function hideFlashMsg() {
	$msgs = jQuery("#flash-messages-container .flash-message:visible");

	if ($msgs.length > 0) {
		$msgs.first().fadeOut(function() {
			hideFlashMsg();
		});
	} else {
		jQuery("#flash-messages-container").hide();
	}
}