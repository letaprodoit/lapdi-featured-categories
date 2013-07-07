jQuery(document).ready(function() {

	jQuery("div#makeMeScrollable").smoothDivScroll({ 
		autoScroll: "onstart",
		autoScrollDirection: "endlessloopright", 
		autoScrollStep: 1, 
		autoScrollInterval: 20,
		visibleHotSpots: "always"
	});
});