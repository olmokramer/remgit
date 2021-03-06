/* Main Controller JS */
(function() {
	"use strict";
	window.Main = {

		//sections widths
		windowWidth: null,
		windowHeight: null,
		headerHeight: null,
		footerHeight: null,

		LoadingIndicator: {
			show: function() {
				$(".loading-indicator").css("opacity", 1);
			},
			hide: function() {
				$(".loading-indicator").css("opacity", 0);
			}
		},

		//js session vars
		currentPageId: null,
		currentGallery: null,
		selectedMedia: null,
		currentMenuItemId: null,
		currentMediaItemId: null,
		currentTypeOfMain3Data: 'document',
		maxImageSize: 0,

		run: function() {
			Main.resizeWindow();
			Main.initList();
			Main.initMenuItems();
			Main.initNav();
			Main.getMaxImageSize();
			MainMenu.show();
		},

		resizeWindow: function() {
			this.setWindowSizes();
			this.adjustWindow();
		},

		setWindowSizes: function() {
			this.windowWidth = $(window).width();
			this.windowHeight = $(window).height();
		},

		adjustWindow: function () {
			$('.section').each(function(){
				$(this).find('.section-container').height($(window).height() - 42 - 41);
				switch($(this).attr('class')) {
					case 'section list':
						$(this).width($(window).width() / 5);
						break;
					case 'section view':
						$(this).width($(window).width() / 5*3);
						break;
				}
				$(this).height($(window).height() - 102);
			});
		},

		getMaxImageSize: function() {
			$.post("AjaxListener.php", {
				action: "getMaxImageSize"
			}, function(maxSize) {
				Main.maxImageSize = maxSize;
			});
		},

		initList: function(container) {
			$(container).find('li').on("click", function() {
				$(this).addClass('current')
					.siblings(".current").removeClass("current");
				Main.ListItemEvent($(this).data());
			});
		},

		initTextareas: function(container) {
			$(container).find('textarea')
				.each(function() {
					switch($(this).data('inputtype')) {
						case 'tags':
							$(this).tagsInput({'width': '96%'});
							break;
						case 'wysiwyg':
							$(this).cleditor({
							width:'97%',
							height:'300px',
							controls: "bold italic underline strikethrough | color highlight removeformat | bullets numbering | alignleft center alignright justify | undo redo | " + "image link unlink | cut copy paste pastetext | source", // controls to add to the toolbar
							});
							break;
					}
				});
		},

		ListItemEvent: function(data) {
			$('.menu-item-deleteallimages').css("visibility", "hidden");
			switch(data.type) {
				case 'folder':
				case 'cat':
					Document.showList(data.type, data.id);
					break;
				case 'doc':
					Document.show(data.id);
					break;
				case 'batch':
					$('.menu-item-deleteallimages').css("visibility", "visible");
				case 'library':
					$('#main-2').find('.section-container').html();
					Media.currentMediaKind=data.mediakind;
					Media.showList("limit=0,50&id="+data.id,Media.currentMediaKind);
					break;
				case 'mediaItem':
					Media.show(data.id);
					break;
			}
		},

		notify: function(functionName) {
			var gritter_title = functionName;
			var gritter_text;
			switch(functionName) {
				case 'Save Document':
					gritter_text = 'The document was succesfully saved';
					break;
				case 'Create Document':
					gritter_text = 'A document was successfully created';
					break;
				case 'Delete Document':
					gritter_text = 'The document was successfully deleted';
					break;
				case 'Order Documents':
					gritter_text = 'Documents successfully reordered';
					break;
				case 'Order Media':
					gritter_text = 'Gallery media were successfully reordered';
					break;
				case 'Add Media to Gallery':
					gritter_text = 'One or more images were succesfully added to the gallery';
					break;
				case 'Remove Media from Gallery':
					gritter_text = 'One or more images were succesfully removed from the gallery.';
					break;
				case 'Save Media':
					gritter_text = 'Media was successfully saved';
					break;
				case 'Delete Media':
					gritter_text = 'Media was successfully deleted';
					break;
				case 'Add vimeo stream':
					gritter_text = 'Vimeo Stream succesfully added';
					break;
				case 'Youtube video saved':
					gritter_text = 'Youtube video successfully added to the library';
					break;
			}

			$.gritter.add({
				title: gritter_title,
				text: gritter_text,
				sticky: false,
				time: '2000'
			});

		},

		initMenuItems: function() {
			$('.button-create').click(function() {
				switch(Main.currentTypeOfMain3Data){
				case 'document':
					Document.create();
				break;
				case 'mediaItem':
					//Media.showUploadScreen();
					break;
				}
			});
			$('.button-saveitem').click(function() {
				switch(Main.currentTypeOfMain3Data){
				case 'document':
					Document.save();
					break;
				case 'mediaItem':
					Media.save();
					break;
				}
			});
			$('.menu-item-removemedia').click(function() {
				Gallery.removeMedia();
			});
			$('.menu-item-deletedoc').click(function() {

				switch(Main.currentTypeOfMain3Data){
				case 'document':
					Main.confirmDelete("document");
					break;
				case 'mediaItem':
					Main.confirmDelete("media");
					break;
				}

			});
			$('.menu-item-deleteallimages').click(function() {
				var curItem = $("#main-1").find("li.current");
				if($(curItem).attr("data-mediakind") === "batch") {
					var batchId = $(curItem).attr("data-id");
					Media.deleteBatch(batchId);
				}
			});
		},

		initNav: function() {
			$('nav').find('li').click(function() {
				if(!$(this).hasClass('current')) {
					$(this).parent().find('.current').removeClass('current');
					$(this).addClass('current');
					switch($(this).data('label')) {
					case 'documents':
						MainMenu.show();
						Main.currentTypeOfMain3Data = 'document';
						Main.currentPageId = null;
						Main.currentMediaItemId = null;
						break;
					case 'media':
						Media.showMenu();
						Main.currentTypeOfMain3Data = 'mediaItem';
						Main.currentPageId = null;
						Main.currentMediaItemId = null;
						Media.currentMediaKind = "all";
						break;
					}
				}
			});
			$('.logout').click(function() {
				Logger.logout();
			});
		},

		confirmDelete: function (action) {
			apprise('Are You Sure you want to delete this item?', {verify: true}, function(r) {
				if(r) {
					switch(action) {
					case 'document':
						Document.drop();
						break;
					case 'media':
						Media.drop();
						break;
					}
				}
			});
		}
	};

	$(document).ready(function() {
		Main.run();
	});
	$(window).resize(function(){
		Main.resizeWindow();
	});
})();
