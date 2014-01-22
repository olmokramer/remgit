/* Media Controller JS */
var Media = {

	uploadedFiles: 0,
	processedFiles: 0,
	totalFiles: 0,

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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				Media.initMenu(data);

			}
		})
	},

	/*
	function showList
	Displays the media items list
	@param string options
	*/

	showList: function(options, kind) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "showMediaList",
				"options": options,
				"kind": kind
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				Media.initList(data);
			}
		})
	},

	/*
	function appendToList
	Appends items to the existing media items list
	@param string options
	*/

	appendToList: function(options, append) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "appendToMediaList",
				"options": options,
				"append": append
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
			    $(".showmore").remove();
				Main.LoadingIndicator.hide(); //hide the loading indicator
				Media.handleListMedia(data);
			}
		})
	},

	/*
	function showBrowser
	Diplays the Media Browser
	*/

	showBrowser: function(activeIds, mediaKind, options) {
		Main.activeGalleryIds = activeIds;

		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				"action": "showMediaBrowser",
				"activeMedia": activeIds,
				"mediaKind": mediaKind,
				"options": options
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				Media.initBrowser(data, options);
			}
		})
	},

	/*
	function appendToBrowser
	Append newly loaded items to the Media Browser
	*/

	appendToBrowser: function(activeIds, options) {
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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				$("#imagepicker-media").append(data);
				$("#imagepicker-media").find(".showmore-browser").click(function() {
					options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
					$(this).remove();
					Media.appendToPicker(options);
				})
				$("#imagePicker").find('.mediaItem').unbind('click').click(function() {
					imgCleanUrl = $(this).data('cleanurl');
					imgUrl = $(this).css('background-image');
					$("#coverImage")
						.css({"background-image": imgUrl})
						.attr("data-cleanurl", imgCleanUrl)
					$("#imagePickerHolder").hide();
				});
			}
		})
	},

	/*
	function addVideoStream
	Adds a vimeo user id to db and refreshes vimeo media
	*/

	addVideoStream: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'addVideoStream',
				vimeoUserId: $('#ajaxLoader').find('#streamId').val()
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				Main.notify('Add vimeo stream');
				Media.refreshVideoStreams();
				$('#addVideoStream').remove();
				Main.ListItemEvent($('.section-container li:last').data());
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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
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
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				$('#main-3').find('.section-container').html('');
				$('#main-2').find('li[data-id="'+Main.currentMediaItemId+'"]').remove();
				Main.notify('Delete Media');
			}
		})
	},

	deleteBatch: function(batchId) {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'deleteMediaBatch',
				batchId: batchId
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				$('#main-1').find('li.current').remove();
				$('#main-2 .section-container').html("");
				Main.notify('Delete Media');
			}
		})
	},

	/*
	function showAddVideoStream
	Displays a popup in which you can fill in video stream information
	*/

	showAddVideoStream: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'showAddVideoStream',
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				$("#ajaxLoader").html(data);
				$("#addVideoStream").draggable({
					handle: ".header"
				});
			}
		})
	},

	/*
	function refreshVideoStreams
	Refreshes all known media streams in the db
	*/

	refreshVideoStreams: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'refreshVideoStreams',
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
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

        var batchdate = false;
		//init the upload-media div element
		$("input#addImages").fileprocessor({
            maxImageSize: Main.maxImageSize,
            onItem: function(item, numProcessed, total) {
            	var data = item;
            	data.action = "uploadImage";
            	if(numProcessed === 1) {
            		$("#uploads-progress").show();
            		Media.uploadedFiles = 0;
            		Media.processedFiles = numProcessed;
            		Media.totalFiles = total;
            		batchdate = Math.round((new Date()).getTime() / 1000);
            		$("#uploads-progress h3").html("uploading files");
            	}
            	data.creationdate = batchdate;
				$.post("AjaxListener.php", data, function(result) {
					Media.uploadedFiles++;
            		$("#uploads-progress h3").html("uploaded " + Media.uploadedFiles + " of " + Media.totalFiles);
            		if(Media.uploadedFiles === Media.totalFiles) {
						$("#uploads-progress").fadeOut();
            		}
					Media.showList('limit=0,50', Media.currentMediaKind);
				});
            },
            onEnd: function(data) {
            }
        });

		$('#main-1').find('.section-container').html(data); //parse the ajax loaded data into the div container
		Main.initList('#main-1'); //initialize the list elements

		//empty the other section-containers
		$('#main-2').find('.section-container').html('');
		$('#main-3').find('.section-container').html('');
		$('<div/>')
			.addClass('dropdown')
			.append($('<ul/>')
				.append($('<li/>')
					.addClass('menu-item-addimages')
					.html('Add Image(s)')
					.click(function() {
						$("#addImages").trigger("click");
					})
				)
				.append($('<li/>')
					.addClass('menu-item-addvideostream')
					.html('Add Vimeo Stream')
					.click(function() {
						Media.showAddVideoStream();
					})
				)
				.append($('<li/>')
					.addClass('menu-item-refreshvideostreams')
					.html('Refresh Vimeo Streams')
					.click(function() {
						Media.refreshVideoStreams();
					})
				)
				.append($('<li/>')
					.addClass('menu-item-refreshvideostreams')
					.html('Add Single video')
					.click(function() {
						Media.addSingleVideo();
					})
				)
			)
			.appendTo('.button-create');
		$('.button-create').attr('title', '');
		$('.footer').find('.button-docsettings').hide();

		Main.resizeWindow(); //resize window
	},

	/*
	function initList
	Initializes a list of media items
	@param string data = the ajax loaded data
	*/

	initList: function(data) {
	    $('#main-2').find('.section-container').html("<ul></ul>");
		$('#main-2').find('.section-container ul').html(data);
		Main.initList('#main-2');
		$('.showmore').unbind("click").click(function(){
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
		$('.showmore').unbind("click").click(function(){
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
			$(this).toggleClass('activated');
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

	handleBrowserMedia: function(data) {
		$(".showmore-browser").remove();
		$("#media-tabs").append(data);
		$(".showmore-browser").click(function() {
			options = "limit="+$(this).data('limitstart')+","+$(this).data('limitend');
			Media.appendToBrowser(Main.activeGalleryIds, options);
		})
		$("#mediaBrowser").find('.mediaItem').unbind('click').click(function() {
			$(this).toggleClass('activated');
		});
	},

	/*
	add Single video
	*/

	addSingleVideo: function() {
		$(document).load('Views/ModalWindow.php', function(data) {
			$('body').append(data);

			$(".modalWindow .button-confirm").click(function() {
				Media.saveSingleVideo();
			})

			$(".modalWindow .button-cancel").click(function() {
				$(".modalOverlay").hide().remove();
			})


		});
	},

	/*
	save Single video
	*/

	saveSingleVideo: function() {
		url = $(".modalWindow input#url").val();

		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: {
				action: 'saveSingleVideo',
				url: url,
			},
			cache: false,
			beforeSend: function() {
				Main.LoadingIndicator.show(); //show the loading indicator
			},
			success: function(data){
				Main.LoadingIndicator.hide(); //hide the loading indicator
				$(".modalOverlay").hide().remove();
				Main.notify('Video added to library');
				Media.showList('limit=0,50', Media.currentMediaKind);
			}
		})
	}

}
