$(function() {
	/*
	// Graph
	//===== Sparklines =====//
	$('#total-visits').sparkline(
		'html', {type: 'bar', barColor: '#ef705b', height: '35px', barWidth: "5px", barSpacing: "2px", zeroAxis: "false"}
	);
	$('#balance').sparkline(
		'html', {type: 'bar', barColor: '#91c950', height: '35px', barWidth: "5px", barSpacing: "2px", zeroAxis: "false"}
	);
	
	$(window).resize(function () {
		$.sparkline_display_visible();
	}).resize();
	*/
	
	/*
	$(".dynamic-bootbox").click(function(e) {
		e.preventDefault();

		var str = $("<img src='assets/img/elements/loaders/7.gif' />");
		bootbox.alert(str);
		
		var href = $(this).attr("href");
		$.get(
				href,
				function(result) {
					$(".modal-body").html(result);
				},
				'html'
			);
	});*/

	//===== Form elements styling =====//
	$(".styled, .dataTables_length select").uniform({ radioClass: 'choice' });
	
	$(".select2").select2();
	
	//===== Elastic textarea =====//
	if ($(".auto").length > 0) {
		$('.auto').autosize();
	}
	
	
	$(".dynamic-bootbox").click(function(e) {
		e.preventDefault();
		
		bootbox.dialog("<img src='assets/img/elements/loaders/7.gif' />", 
						[{
							"label" : "Close"
						}]
		);
		
		var href = $(this).attr("href");
		$.get(
				href,
				function(result) {
					$(".modal-body").html(result);
				},
				'html'
			);
	});

	$(".popup-700x700").popupBox({
		width:700,
		height:700
	});
	
	$(".confirm-link").click(function(e) {
		e.preventDefault();
		
		var href = $(this).attr("href");
		
		bootbox.confirm("Are you sure?", function(result) {
			if (result) {
				document.location = href;
			} else {
				return;
			}
		});
	});
	
	if ($(".datepicker").length > 0) {
		$(".datepicker").datepicker({
			defaultDate: +7,
			showOtherMonths:true,
			autoSize: true,
			appendText: '(yyyy-mm-dd)',
			dateFormat: 'yy-mm-dd'
		});
	}

});