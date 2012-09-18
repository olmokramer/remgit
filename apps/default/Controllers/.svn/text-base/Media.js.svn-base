/* Media Controller JS */
var Media = {

	/*
	function show
	Displays a media item from the Library
	@param int id - The id of the media item to be shown
	*/

	show: function(id) {		
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'showMediaItem',
				id: id
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Main.currentMediaItemId = id;
				Media.init(data);
			}
		})		
	},
	
	/*
	function showMenu
	Displays the media menu
	*/
	
	showMenu: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'showMediaMenu',
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Media.initMenu(data);

			}
		})
	},
	
	/*
	function showList
	Displays the media items list
	@param string options
	*/
	
	showList: function(options) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "showMediaList",
				"options": options
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Media.initList(data);
			}
		})
	},
	
	/*
	function appendToList
	Appends items to the existing media items list
	@param string options
	*/
	
	appendToList: function(options) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "appendToMediaList",
				"options": options
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Media.handleListMedia(data);
			}
		})
	},

	/*
	function showBrowser
	Diplays the Media Browser
	*/
	
	showBrowser: function(activeIds, options) {
		Main.activeGalleryIds = activeIds;
		
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "showMediaBrowser",
				"activeMedia": activeIds,
				"options": options
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Media.initBrowser(data, options);
			}
		})
	},
	
	/*
	function appendToBrowser
	Append newly loaded items to the Media Browser
	*/
	
	appendToBrowser: function() {

		Main.activeGalleryIds = activeIds;
		
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "appendtoMediaBrowser",
				"activeMedia": activeIds,
				"options": options
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Media.handleBrowserMedia(data, options);
			}
		})

	},
	
	/*
	function showPicker
	Append newly loaded items to the Image Picker
	@param string options  - (i.e. an item limit)
	*/
	
	appendToPicker: function(options) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "appendtoImagePicker",
				"options": options
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				$("#imagepicker-media").append(data);
				$("#imagepicker-media").find(".showmore-browser").click(function() {
					options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
					$(this).remove();
					Media.appendToPicker(options);
				})
				$("#imagePicker").find('.mediaItem').unbind('click').click(function() {
					imgCleanUrl = $(this).find('img').data('cleanurl');
					imgUrl = $(this).find('img').attr('src');
					$("#coverImage").find('.image').html('<img src="'+imgUrl+'" data-cleanurl="'+imgCleanUrl+'">');
					$("#imagePickerHolder").hide();
				});
			}
		})
	},
	
	/*
	function save
	Updates media item info
	*/
	
	save: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'saveMediaItem',
				id: Main.currentMediaItemId,
				title: $('#mediaitem-container').find('#media-title').val(),
				caption: $('#mediaitem-container').find('#media-caption').val()
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				Main.notify('Save Media');
			}
		})

	},

	/*
	function drop
	Deletes a media item
	*/
	
	drop: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'deleteMediaItem',
				id: Main.currentMediaItemId
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				$('#main-3').find('.section-container').html('');
				$('#main-2').find('li[data-id="'+Main.currentMediaItemId+'"]').remove();
				Main.notify('Delete Media');
			}
		})
	},

	/*
	function showUploadScreen
	Displays the upload screen
	*/
	
	showUploadScreen: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'showUploadScreen',
			},
			cache: false,
			beforeSend: function() {
				$("#loading-indicator").show(); //show the loading indicator
			},
			success: function(data){
				$("#loading-indicator").hide(); //hide the loading indicator
				$("#ajaxLoader").html(data);
					$("#uploadScreen").draggable({
						handle: ".header"
					});
			}
		})
	},
	
	/*
	function init
	Initializes a media item
	@param string data - the ajax loaded data
	*/
	
	init: function(data) {
		$('#main-3').find('.section-container').html(data);
		Main.currentTypeOfMain3Data = 'mediaItem';
	},
	
	/*
	function initMenu
	@param string data - the ajax loaded data
	*/
	
	initMenu: function(data) {
	
		$('#main-1').find('.section-container').html(data); //parse the ajax loaded data into the div container
		Main.initList('#main-1'); //initialize the list elements

		//empty the other section-containers
		$('#main-2').find('.section-container').html(''); 
		$('#main-3').find('.section-container').html('');
		
		Main.resizeWindow(); //resize window
	},
	
	/*
	function initList
	Initializes a list of media items
	@param string data = the ajax loaded data
	*/
	
	initList: function(data) {
		$('#main-2').find('.section-container').html(data);		
		Main.initList('#main-2');
		$('.footer').find('.button-docsettings').hide();
		$('.showmore').click(function(){
			$(this).remove();
			options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
			Media.appendToList(options, 1);
		})
	},
	
	/*
	function handleListMedia
	*/
	
	handleListMedia: function(data) {
		$('#main-2').find('.section-container').find('ul').append(data);		
		Main.initList('#main-2');
		$('.footer').find('.button-docsettings').hide();
		$('.showmore').click(function(){
			$(this).remove();
			options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
			Media.appendToList(options, 1);
		})
	},
	
	/*
	function initBrowser
	*/
	
	initBrowser: function(data, options) {
		$("#ajaxLoader").html(data);
		$("#mediaBrowser").draggable({
		 handle: ".header"
		});
		$("#mediaBrowser").find('.mediaItem').unbind('click').click(function() {
			imageElem = $(this).find('img');
			imageElem.toggleClass('activated');
		});
		$(".showmore-browser").click(function() {
			options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
			Media.appendToBrowser(Main.activeGalleryIds, options);
		})
		
		$("#button-addtogallery").click(function() {
			Gallery.addMedia();
		})
		
		$("#button-browsercancel").click(function() {
			$('#mediaBrowser').remove();
		})
	},
	
	/*
	function handleBrowserMedia
	*/
	
	handleBrowserMedia: function() {	
		$(".showmore-browser").remove();
		$("#media-tabs").append(data);
		$(".showmore-browser").click(function() {
			options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
			Media.appendToBrowser(Main.activeGalleryIds, options);
		})
		$("#mediaBrowser").find('.mediaItem').unbind('click').click(function() {
			imageElem = $(this).find('img');
			imageElem.toggleClass('activated');
		});
	}
	
}