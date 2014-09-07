/* Document Controller JS */
(function() {
	"use strict";
	window.Document = {

		/*
		function show
		@param int id - document id
		Show a single document, found by id
		*/

		show: function(id) {
			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {action: 'showDoc', id: id},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(data){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					Main.currentPageId = id; //set the Application current page id to current page id
					Document.init(data); //initialize the document using the ajax returned data
				}
			});
		},

		/*Main.LoadingIndicator
		function show list
		@param string type - the documentlist type
		@param int id - the id of the current menu items id
		@param boolean reOrder - if true, re-order the open documentlist
		Show a document list
		*/

		showList: function(type, id, reOrder) {
			if(reOrder === true) { Document.sortList(); } //reorder open list if true
			Main.currentMenuItemId = id; //set the currentId to this id

			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					action: 'showDocList',
					type: type,
					id: id,
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(data){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					Document.initList(data);
				}
			});
		},
		/*
		function save
		Save a single document
		*/

		save: function() {

			var fields = [];
			var categories = [];
			var pubdate = [];
			var pubstate = null;

			//parse field values
			$("#document-form").find("input[type=text], textarea").each(function(key, element) {
				fields.push({
					kind: $(element).data('kind'),
					label: $(element).data('name'),
					id: $(element).data('id'),
					value: $(element).val(),
					fieldType: $(element).data('fieldtype'),
				});
			});
			//parse checked category ids
			$("#document-form").find("input[type=checkbox]").each(function(key, element){
				var value = $(element).attr('checked');
				if(value === "checked") {
					categories.push($(element).data('id'));
				}
			});

			//coverImage
			var coverImage = $("#coverImage").data('cleanurl');

			//parse publication status
			pubdate = $("#pubstate-form").find("input[data-name=pubdate-year]").val();
			pubdate += $("#pubstate-form").find("input[data-name=pubdate-month]").val();
			pubdate += $("#pubstate-form").find("input[data-name=pubdate-day]").val();
			pubdate += $("#pubstate-form").find("input[data-name=pubdate-hour]").val();
			pubdate += $("#pubstate-form").find("input[data-name=pubdate-minute]").val();
			pubstate = $("#pubstate-form").find("select[data-name=pubstate]").val();

			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					"action": "saveDoc",
					"id": Main.currentPageId,
					"fields": fields,
					"coverImage": coverImage,
					"categories": categories,
					"pubstate": pubstate,
					"pubdate": pubdate
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(data){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					//get the title from the saved document and apply it to the corresponding list item
					var doctitle = $("#document-form").find('input[data-name=title]').val();
					$('#main-2').find('li[data-id="'+Main.currentPageId+'"]').find('h6').html(doctitle);

					//check the publication status of the saved document and apply it to the corresponding list item
					pubstate = $("#pubstate-form").find('#publishState').val();
					$('#main-2').find('li[data-id="'+Main.currentPageId+'"]').find('.pubstate').removeClass().addClass('pubstate state-'+pubstate);

					//notify that the document is saved
					Main.notify('Save Document');

					//log the data (for development use only)
					console.log(data);
				}
			});
		},
		/*
		function create
		Creates a new document
		*/

		create: function() {
			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {action: 'createDoc', menuItemsId: Main.currentMenuItemId},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(docId){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					Document.showList('folder', Main.currentMenuItemId, true); //reload the document list
					Document.show(docId); //show the just created document
					Main.notify('Create Document'); //notify that the document was created
				}
			});
		},

		/*
		function sortList
		Sorts the currently open list of documents
		*/
		sortList: function() {
			var docIds = []; //create a new array

			//push all list elements to the docIds array
			$("#main-2").find('li').each(function() {
				docIds.push($(this).data('id'));
			});

			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					"action": "sortDocs",
					"menuItemId": Main.currentMenuItemId,
					"docIds": docIds
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					Main.notify('Order Documents'); //notify that the list was re-ordered
				}
			});
		},
		/*
		function drop
		Deletes a single document
		*/

		drop: function() {
			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					action: 'deleteDoc',
					id: Main.currentPageId,
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					$('#main-2').find('li[data-id="'+Main.currentPageId+'"]').remove(); //remove the corresponding lis element
					$('#main-3').find('.section-container').html(''); //empty the right container since the document was deleted
					Main.notify('Delete Document'); //notify that the document was deleted
				}
			});
		},

		/*
		function init
		Initializes single document
		@param string data - The ajax loaded return data
		*/
		init: function(data) {
			//parse the ajax loaded data into the div container
			$('#main-3').find('.section-container').html(data);

			//Initialize the textarea elements
			Document.initTextareas();

			//Initialize the tabs
			$("#document-tabs").tabs();

			//Initialize the galleries
			$(".gallery").sortable({
				stop: function() { Gallery.sort($(this).data('id')); }
			});

			$('.upload-media').click(function() {
				$("#uploadImages").trigger("click");
			});

			//init the add-media div element
			$('.add-media').click(function() {
				var media = [];
				Main.currentGallery = $(this).parent().data('id');
				var galleryKind = $('#main-3 #document-tabs li.ui-tabs-selected a').data('kind');
				$(this).parent().find('.item').each(function() {
					media.push($(this).data('id'));
				});
				Media.showBrowser(media, galleryKind, 'limit=0,50');
			});


			var batchdate = false;		//init the upload-media div element
			$("input#uploadImages").fileprocessor({
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
						Main.currentGallery = $('.gallery').data('id');
						Gallery.putMedia([result]);
					});
				},
				onEnd: function() {
				}
			});


			//init the gallery item elements
			$('.item').click(function() {
				$(this).find('img').toggleClass('selecteditem');
			});

			//init the gallery item button elements
			$('.gallery').find('.remove').click(function(){
				Gallery.removeMedia($(this).parent().data('id'));
			});

			//init the publication status select box
			$('#publishState').change(function() {
				$("#publishdate").toggle();
			});
			//Load library media to the Image Picker Element
			Media.appendToPicker('limit=0,50');

			$('#coverImage').click(function() {
				$('#imagePickerHolder').toggle();
			});

			//set the current maindata type to 'document'
			Main.currentTypeOfMain3Data = 'document';
		},
		/*
		function initList
		@param string data - the ajax loaded data
		Initialize a list of documents
		*/

		initList: function(data) {
			//parse the ajax loaded data into the div container
			$('#main-2').find('.section-container').html(data);

			//initialize the lists (make sortable)
			$('#main-2').find('ul').sortable({
				handle: 'h6',
				stop: function() { Document.sortList(); }

			});

			//apply the 'current' class to the currently open document
			$('#main-2').find('li[data-id="'+Main.currentPageId+'"]').addClass('current');

			Main.initList('#main-2'); //init the list elements

		},
		/*
		function initTextareas
		Initializes the textarea elements of the open document; applies the cleditor (wysiwyg) plugin or tagsInput plugin if needed
		*/

		initTextareas: function() {
			var container = "#main-3";
			$(container)
				.find('textarea')
					.each(function() {
						switch($(this).data('inputtype')) {
							case 'tags':
								$(this).tagsInput({'width': '96%'});
								break;
							case 'wysiwyg':
								$(this).cleditor({
									width:'97%',
									height:'300px',
									controls:     // controls to add to the toolbar
									"bold italic underline strikethrough | color highlight removeformat | bullets numbering | alignleft center alignright justify | undo redo | " + "image link unlink | cut copy paste pastetext | source"
								});
								break;
						}
					});
		}
	};
})();
