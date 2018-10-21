jQuery(document).ready(function() {

	jQuery("div#makeMeScrollable").smoothDivScroll({ 
		autoScrollingMode: "onStart",
		autoScrollDirection: "endlessloopright", 
		autoScrollingDirection: 1, 
		autoScrollingInterval: 20,
	});
});