/* Gallery Controller JS */
(function() {
	"use strict";
	window.Gallery = {

		/*
		function sort
		Sorts gallery images
		*/

		sort: function(galleryId) {

			Main.currentGallery = galleryId;
			var item_ids = [];
			$('.gallery[data-id="'+galleryId+'"]').find('.item').each(function(key, element) {
				item_ids[key] = $(element).data('id');
			});

			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					"action": "sortGallery",
					"gall_id":Main.currentGallery,
					"items":item_ids
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					Main.notify('Order Media');
				}
			});
		},

		/*
		function addMedia
		*/

		addMedia: function() {
			var selectedMedia = [];
			$("#mediaBrowser").find('.activated').each(function() {
				selectedMedia.push($(this).data('id'));
			});

			if(selectedMedia.length > 0) { //check if at least one item is selected
				Gallery.putMedia(selectedMedia);
			}
		},

		putMedia: function(selectedMedia) {
			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					"action": "addMediaToGallery",
					"galleryId": Main.currentGallery,
					"selectedMedia": selectedMedia
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(data){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					$(".gallery[data-id="+Main.currentGallery+"]").append(data);
					Gallery.initItems();
				}
			});
		},

		/*
		function removeMedia
		@param int id (optional) - id of a specifix mediaItem
		*/

		removeMedia: function(id) {

			var selectedMedia = [];

			if(!id) {
				Main.currentGallery = $('.gallery').data('id');
				$('.gallery[data-id="'+Main.currentGallery+'"]').find('.selecteditem').each(function(){
					selectedMedia.push($(this).parent().data('id'));
				});
			}
			else {
				Main.currentGallery = $('.item[data-id="'+id+'"]').parent().data('id');
				selectedMedia.push(id);
			}

			Main.selectedMedia = selectedMedia;

			$.ajax({
				type: "POST",
				url: "AjaxListener.php",
				data: {
					"action": "removeMediaFromGallery",
					"galleryId":Main.currentGallery,
					"mediaIds":selectedMedia
				},
				cache: false,
				beforeSend: function() {
					Main.LoadingIndicator.show(); //show the loading indicator
				},
				success: function(){
					Main.LoadingIndicator.hide(); //hide the loading indicator
					for(var i=0; i<Main.selectedMedia.length; i++) {
						$('.item[data-id="'+Main.selectedMedia[i]+'"]').remove();
					}
					Main.notify('Remove Media from Gallery');
				}
			});
		},

		/*
		function initItems
		@param string data - the ajax loaded data
		*/

		initItems: function() {
			$("#mediaBrowser").remove();

			//init the gallery item elements
			$('.item').unbind('click').click(function() {
				$(this).find('img').toggleClass('selecteditem');
			});

			//init the gallery item button elements
			$('.gallery').find('.remove').unbind('click').click(function(){
				Gallery.removeMedia($(this).parent().data('id'));
			});

			Gallery.sort(Main.currentGallery);

			$(".gallery[data-id="+Main.currentGallery+"]").sortable({
				stop: function() {
					Gallery.sort(Main.currentGallery);
				}
			});

			Main.notify('Add Media to Gallery');
		}
	};
})();
