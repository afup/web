$(document).ready(function() {
	$("a.geek").click(function() {
		$("div.boss div.details").slideUp();
		$("div.boss h3").css("color", "#A6A6A6");
		$("div.geek div.details").slideDown();
		$("div.geek h3").css("color", "#5A669D");
		return false;
	});
	$("a.boss").click(function() {
		$("div.geek div.details").slideUp();
		$("div.geek h3").css("color", "#A6A6A6");
		$("div.boss div.details").slideDown();
		$("div.boss h3").css("color", "#5A669D");
		return false;
	});
});