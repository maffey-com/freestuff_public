$(function() {
	//===== Hide/show sidebar =====//
	$('.fullview').click(function(){
	    $("body").toggleClass("clean");
	    $('#sidebar').toggleClass("hide-sidebar mobile-sidebar");
	    $('#content').toggleClass("full-content");
	});

	//===== Hide/show action tabs =====//
	$('.showmenu').click(function () {
		$('.actions-wrapper').slideToggle(100);
	});



	//===== Collapsible plugin for main nav =====//
	$('.expand').collapsible({
		defaultOpen: 'current,third',
		cookieName: 'navAct',
		cssOpen: 'subOpened',
		cssClose: 'subClosed',
		speed: 200
	});

});