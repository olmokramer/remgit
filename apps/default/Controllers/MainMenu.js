/* Main Menu Controller JS */
var MainMenu = {
	
	/*
	function show
	Displays the main menu
	*/
	
	show: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {action: 'showMainMenu'},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				MainMenu.init(data);
			}
		})
	},
	
	/*
	function init
	Initializes the main menu
	@param string data - the ajax loaded data
	*/
	
	init: function(data) {
		$('#main-1').find('.section-container').html(data); //parse the ajax loaded data into the div container
		Main.initList('#main-1'); //initialize the list elements

		//empty the other section-containers
		$('#main-2').find('.section-container').html(''); 
		$('#main-3').find('.section-container').html('');
		
		//show the settings button in the footer
		$('.footer').find('.button-docsettings').show();
		$('.footer').find('.button-create').find('.dropdown').remove();
		$('.footer').find('.button-create').attr('title', 'Add Item(s)');
		
		Main.resizeWindow(); //resize window
	}
}