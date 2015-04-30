var imageType = /image.*/;
var videoType = /video.*/;
var audioType = /audio.*/;
var imageDialogSelector = "#image-dialog"

function addTitleText(text, container) {
	var div = document.createElement("div");
	div.innerHTML = text;
	div.classList.add("file-name");
	container.appendChild(div);
}

function isYouTube(url) {
	retVal = false;
	if (url.indexOf("youtube.com") > -1 || url.indexOf("youtu.be") > -1) {
		retVal = true;
	}
	return retVal;
}

function isSoundCloud(url) {
	retVal = false;
	if (url.indexOf("soundcloud.com") > -1) {
		retVal = true;
	}
	return retVal;
}

function isSpotify(url) {
	retVal = false;
	if (url.indexOf("spotify.com") > -1 || url.indexOf("spotify:track:") > -1) {
		retVal = true;
	}
	return retVal;
}

function youTubeID(url) {
	var result = url.match(/(youtu(?:\.be|be\.com)\/(?:.*v(?:\/|=)|(?:.*\/)?)([\w'-]+))/i);
	if (result) {
		return result[result.length - 1];
	} else {
		return null;
	}
}

function spotifyTrackId(url) {
	var delimiter = "/";
	if (url.indexOf("spotify:track:") > -1) {
		delimiter = ":";
	}
	var parts = url.split(delimiter);
	var trackId = parts[parts.length - 1];

	return trackId;
}

function sendToken() {
	if (!document.getElementById('email').value) {
		document.getElementById('send-message').innerHTML = "Please enter a valid email address.";
	} else {
		var posting = $.post("send_preview.php", $("#send-form").serialize());
		posting.done(function(data) {
			$("#send-dialog" ).dialog("close");
		});
	}
}

function uploadFileData(fileData, fileName) {
    var xhr = new XMLHttpRequest();
    if (xhr.upload) {
		setStatus("Uploading " + fileName);
		xhr.upload.onprogress = function(e) {
			if (e.lengthComputable) {
				setStatus("Uploading " + fileName + " " + (Math.round((e.loaded / e.total) * 100))+"%");
			}
		};
        xhr.open("POST", "upload.php", true);
        xhr.setRequestHeader("X-FILENAME", fileName);
        xhr.send(fileData);
    }
}

function uploadFile(file) {
	var reader  = new FileReader();
	reader.onloadend = function () {
		uploadFileData(reader.result, reader.fileName);
	};
	reader.fileName = file.name;
	reader.readAsDataURL(file);
}

function addAudio (bento, audioSrc, audioFile, savedBento) {
	// Remove any existing audio
	if (bento.audio) {
		bento.removeChild(bento.audio);
		bento.audio = null;
	}
	bento.download_file_name = audioFile.name;
	bento.download_mime_type = audioFile.type;

	// Create the audio element
	var audio = document.createElement('audio');
	audio.setAttribute('controls', true);
	audio.src = audioSrc;
	audio.id = bento.id + '-audio';
	audio.classList.add("audio-player");
	audio.style.zIndex = 10;
	bento.appendChild(audio);
	bento.file = audioFile;
	bento.audio = audio;

	// add close button
	var closeButton = document.createElement('div');
	closeButton.id = audio.id + "-close";
	closeButton.classList.add("audio-close-button");
	closeButton.style.zIndex = 11;
	closeButton.target = audio;
	closeButton.onclick = function(){closeClicked(event, closeButton);};
	bento.appendChild(closeButton);
	showControl(closeButton.id, audio);
	unsaved();
}

function addVideo (bento, videoSrc, videoFile, savedBento) {
	if (bento.imageContainer) {
		bento.removeChild(this.imageContainer);
		bento.imageContainer = null;
		hideControl(bento.id + "-slider");
	} else if (bento.video) {
		bento.removeChild(bento.video);
		bento.video = null;
	}
	bento.download_file_name = videoFile.name;
	bento.download_mime_type = videoFile.type;

	var video = document.createElement('video');
	video.onclick = function(){videoClicked(event, this)};
	video.setAttribute('controls', true);
	video.setAttribute('preload', "auto");
	if (videoFile) {
		video.src = window.URL.createObjectURL(videoFile);
		bento.file = videoFile;
	} else {
		video.src = videoSrc;
	}
	video.id = bento.id + '-video';
	video.width =  parseInt(getComputedStyle(bento).width, 10);
	video.height = parseInt(getComputedStyle(bento).height, 10);
	video.style.position = "absolute";
	video.style.top = 0;
	video.style.left = 0;
	bento.video = video;
	video.classList.add("video-js");
	video.classList.add("vjs-default-skin");
	video.classList.add("video-player");
	video.classList.add("no-pointer");
	bento.appendChild(video);
	showControl(bento.id + "-close", video);
	unsaved();
}

function addImage(bento, imageSrc, imageFile, savedBento) {
	// Remove any previously dropped image or video
	if (bento.imageContainer) {
		bento.removeChild(bento.imageContainer);
		bento.imageContainer = null;
	} else if (bento.video) {
		bento.removeChild(bento.video);
		bento.video = null;
	}
	if (imageFile) {
		bento.image_file_name = imageFile.name;
	}

	// Create the image scroll container
	var imageContainer = document.createElement('div');
	bento.imageContainer = imageContainer;
	imageContainer.id =  bento.id + '-image-container';
	imageContainer.style.position = 'absolute';

	// Create the IMG
	var img = new Image();
	img.id = bento.id + '-image';
	img.file = imageFile;
	img.parentBento = bento;
	img.imageContainer = imageContainer;
	img.crossOrigin = "Anonymous";
	img.src = imageSrc;
	img.hyperlink = null;
	img.className = "bento-image";
	img.savedBento = savedBento;
	img.onload = function() {
		resizeImage(this, this.parentBento);
		if (this.savedBento) {
			this.style.width = this.savedBento.image_width;
			this.style.height = this.savedBento.image_height;
			this.saved = true;
			$("#"+bento.id+"-slider").slider("value", this.savedBento.slider_value);
		} else {
		}
		// resize the image container so that the image has scroll containment
		resizeContainer(this.parentBento, this, this.imageContainer);
		if (this.savedBento) {
			this.style.top = this.savedBento.image_top_in_container;
			this.style.left = this.savedBento.image_left_in_container;
		}
	}

	// add the img to the container, add the container to the bento
	imageContainer.appendChild(img);
	bento.appendChild(imageContainer);

	// make the IMG draggable inside the DIV
	$('#'+ img.id)
		.draggable({ containment: "#" + imageContainer.id})
		.click(function(){
            if ( $(this).is('.ui-draggable-dragging') ) {
                  return;
            }
            imageClicked($('#'+ img.id));
		});

	// change the hover for the bento to show the slider and close button
	showControl(bento.id + "-close", imageContainer);
	showControl(bento.id + "-slider", img);
}

function addSpotify(bento, trackId) {
	var iframe = document.createElement('iframe');
	var contentURI = "https://embed.spotify.com/?url=spotify:track:"+trackId;
	iframe.src = contentURI;
	var width = bento.offsetWidth;
	var height = bento.offsetHeight;
	iframe.width = width;
	iframe.height = height;
	iframe.style.border = 0;
	iframe.style.position = "absolute";
	iframe.style.top = "0px";
	iframe.style.left = "0px";
	bento.appendChild(iframe);
	bento.iframe = iframe;
	bento.contentURI = contentURI;
	showControl(bento.id + "-close", iframe);
	unsaved();
}

function addSoundCloud(bento, url) {
	var iframe = document.createElement('iframe');
	iframe.src = "https://w.soundcloud.com/player/?url="+encodeURIComponent(url);
	var width = bento.offsetWidth;
	var height = bento.offsetHeight;
	iframe.width = width;
	iframe.height = height;
	iframe.style.border = 0;
	iframe.style.position = "absolute";
	iframe.style.top = "0px";
	iframe.style.left = "0px";
	bento.appendChild(iframe);
	bento.iframe = iframe;
	bento.contentURI = url;
	showControl(bento.id + "-close", iframe);
	unsaved();
}

function addYouTube(bento, url) {
	var iframe = document.createElement('iframe');
	var videoId =  youTubeID(url);
	iframe.src = "//www.youtube.com/embed/"+videoId;
	var width = bento.offsetWidth;
	var height = bento.offsetHeight;
	iframe.width = width;
	iframe.height = height;
	iframe.style.border = 0;
	iframe.style.position = "absolute";
	iframe.style.top = "0px";
	iframe.style.left = "0px";
	bento.appendChild(iframe);
	bento.iframe = iframe;
	bento.contentURI = url;
	showControl(bento.id + "-close", iframe);
	unsaved();
}

function createThumbnailContainer(object, titleText, parentId) {
	var container = document.createElement("div");
	var inner = document.createElement("div");
	inner.classList.add("inner-thumbnail-container");
	container.onclick = function(){selectThumbnail(this)};
	object.classList.add("photo-thumbnail");
	container.classList.add("thumbnail-container");
	container.classList.add("thumbnail-container-hover");
	container.id = "thumbnail-container-"+($(".thumbnail-container").size()+1);
	inner.appendChild(object);
	container.appendChild(inner);
	if (titleText) {
		addTitleText(titleText, inner);
	}
	document.getElementById(parentId).appendChild(container);

	return container;
}

function openYouTube(url) {
	var videoId = youTubeID(url);
	var error = null;
	if (videoId) {
		var dataURL = "https://gdata.youtube.com/feeds/api/videos/"+videoId+"?v=2&alt=json";
		$.getJSON(dataURL,
			function(data){
				var title = data.entry.title.$t;
				var img = document.createElement("img");
				img.src = "https://img.youtube.com/vi/"+videoId+"/0.jpg";
				img.id = videoId;
				img.youTubeURL = url;
				createThumbnailContainer(img, title, "add-av-desktop");
			}).fail(function() {
				error = "Youtube API call failed.\n\n" + dataURL;
				console.log(error);
				alert(error);
			});
	} else {
		error = "Unable to extract a Youtube video ID from the URL.\n\n"+url;
		console.log(error);
		alert(error);
	}
}

function openSoundCloud(url) {
	$.getJSON("https://api.soundcloud.com/resolve.json?url="+url+"&client_id=YOUR_CLIENT_ID", function(data){
		var img = document.createElement("img");
		if (data.artwork_url) {
			img.src = data.artwork_url.replace("large", "t500x500");
		} else {
			img.src = "images/soundcloud_icon.jpg";
		}
		img.id = data.id;
		img.soundCloudURL = data.uri;
		createThumbnailContainer(img, data.title, "add-av-desktop");
	}).fail(function(){
		var error = "The URL specified is not a valid SoundCloud track or playlist URL.\n\n"+url;
		console.log(error);
		alert(error);
	});
}

function openSpotify(url) {
	var trackId = spotifyTrackId(url);
	$.getJSON("https://api.spotify.com/v1/tracks/"+trackId, function(data){
		var img = document.createElement("img");
		img.src = data.album.images[1].url;
		img.id = trackId;
		img.spotifyTrackId = trackId;
		createThumbnailContainer(img, data.name, "add-av-desktop");
	}).fail(function() {
		var error = "The URL specified is not a valid Spotify track URL.\n\n"+url;
		console.log(error);
		alert(error);
	});
}

function openImageFiles(files) {
	for (var i = 0; i < files.length; i++) {
		var file = files[i];

		// if not an image go on to next file
		if (!file.type.match(imageType)) {
			openMessage("Select Image Files", file.name+" is not an image file (.jpg, .png, etc.).");
			continue;
		}

		var img = document.createElement("img");
		img.src = window.URL.createObjectURL(file);
		img.file = file;
		img.id = file.name;
		createThumbnailContainer(img, file.name, "add-images-desktop");
	}
}

function openMediaFiles(files) {
    for (var i = 0; i < files.length; i++) {
		var file = files[i];

		// if not video or audio go on to next file
		if (!file.type.match(videoType) && !file.type.match(audioType)) {
			openMessage("Select Video/Audio Files", file.name+" is not a music or video file (.mp3, .mp4, etc.).");
			continue;
		}

		var element;
		if (file.type.match(videoType)) {
			element = document.createElement("video");
			element.src = window.URL.createObjectURL(file);
		}

		if (file.type.match(audioType)) {
			element = document.createElement("img");

			// Get the album artwork from an MP3
			if (file.type.indexOf("mp3") >= 0 || file.type.indexOf("mpeg") >= 0) {
				var url = file.urn || file.name;
				ID3.loadTags(
					url,
					function() {showAlbumArt(url, file);},
					{tags: ["title","artist","album","picture"], dataReader: FileAPIReader(file)}
				);
			} else {
				element.src = "images/audio.jpg";
			}
		}

		element.file = file;
		element.id = file.name;
		createThumbnailContainer(element, file.name, "add-av-desktop");
    }
}

function openAttachmentFiles(files) {
	for (var i = 0; i < files.length; i++) {
		var file = files[i];
		var a = appendAttachmentDisplay({file_name: file.name, download_file_name: file.name, download_mime_type: file.type});
		a.file = file;
	}
}

function appendAttachmentDisplay(attachment) {
	var a = document.createElement("a");
	a.appendChild( document.createTextNode(attachment.file_name) );
	a.download_file_name = attachment.download_file_name;
	a.download_mime_type = attachment.download_mime_type;
	var icon = document.createElement("i");
	icon.classList.add("fa", "fa-file", "fa-2x");
	a.appendChild(icon)
	document.getElementById("add-attachment-desktop").appendChild(a);
	return a;
}

function showAlbumArt(url, file) {
	var tags = ID3.getAllTags(url);
	var image = tags.picture;
	var img = document.getElementById(file.name);
	if (image) {
		var base64String = "";
		for (var i = 0; i < image.data.length; i++) {
			base64String += String.fromCharCode(image.data[i]);
		}
		var base64 = "data:" + image.format + ";base64," + window.btoa(base64String);
		img.setAttribute('src', base64);
	} else {
		img.src = "images/audio.jpg";
	}
}

function handleImageFileSelect(evt) {
	openImageFiles(evt.target.files);
}

function handleMediaFileSelect(evt) {
  openMediaFiles(evt.target.files);
}

function handleAttachmentFileSelect(evt) {
	openAttachmentFiles(evt.target.files);
}


//******************************************************
function hideControl(controlId) {
	var control = $("#"+controlId);
	if (control.length > 0) {
		control.css("display", "none");
		control[0].target = null;
	}
/*	
	var control = document.getElementById(controlId);
	var css = '.bento:hover #' + controlId + '{display: none;}';
	var style = document.createElement('style');
	if (style.styleSheet)
		style.styleSheet.cssText = css;
	else
		style.appendChild(document.createTextNode(css));
	document.getElementsByTagName('head')[0].appendChild(style);
	control.style.zIndex = -9999;
	control.target = null;
*/	
}

function showControl(controlId, target) {
	var control = $("#"+controlId);
	if (control.length > 0) {
		control.css("display", "block");
		control.css("zIndex", 2);
		control[0].target = target;
	}
/*	
	var control = document.getElementById(controlId);
	var css = '.bento:hover #' + controlId + '{display: block;}';
	var style = document.createElement('style');
	if (style.styleSheet)
		style.styleSheet.cssText = css;
	else
		style.appendChild(document.createTextNode(css));
	document.getElementsByTagName('head')[0].appendChild(style);

	// put the control on top
	control.style.zIndex = 9999;
	control.target = target;
*/	
}

function closeClicked(event, closeButton) {
	var bento = closeButton.parentNode;
	if (closeButton.target) {
		if (closeButton.target.nodeName === "VIDEO") {
			bento.video = null;
		} else if (closeButton.target.nodeName === "AUDIO") {
			bento.audio = null;
		} else if (closeButton.target.nodeName === "DIV") {
			bento.imageContainer = null;
			removeImage($("#"+closeButton.target.id).find("img"));
		}
		bento.removeChild(closeButton.target);
		bento.iframe = null;
		bento.contentURI = null;
	}
	if (closeButton.target.nodeName === "AUDIO") {
		bento.removeChild(closeButton);
	} else {
		hideControl(closeButton.id);
		hideControl(bento.id + "-slider");
		hideControl(bento.id + "-link-icon");
	}
	event.stopPropagation();
	unsaved();
}

function resizeContainer(bento, img, div) {
	// resize the container so that the image covers the bento with no white space
	var widthDiff = img.width - bento.offsetWidth;
	var heightDiff = img.height - bento.offsetHeight;
	var newContainerWidth = bento.offsetWidth + (widthDiff * 2);
	var newContainerHeight = bento.offsetHeight + (heightDiff * 2);
	div.style.width = newContainerWidth + 'px';
	div.style.height = newContainerHeight + 'px';
	div.style.left = Math.round(0 - ((newContainerWidth - bento.offsetWidth) / 2)) + 'px';
	div.style.top = Math.round(0 - ((newContainerHeight - bento.offsetHeight) / 2)) + 'px';
	img.style.left = Math.round((newContainerWidth / 2) - (img.width / 2)) + 'px';
	img.style.top = Math.round((newContainerHeight / 2) - (img.height / 2)) + 'px';
}

function resizeImage(img, bento) {
	var imgAspectRatio = img.height / img.width;
	var bentoAspectRatio = bento.offsetHeight / bento.offsetWidth;
	if (bentoAspectRatio < imgAspectRatio) {
		img.style.width = bento.offsetWidth + "px";
		img.style.height = "auto";
	} else {
		img.style.height = bento.offsetHeight + "px";
		img.style.width = "auto";
	}
	img.style.top = 0;
	img.style.left = 0;

	// set values for slider scaling
	img.originalWidth = img.width;
	img.originalHeight = img.height;
}

function resizeBento(bento) {
	var image = document.getElementById(bento.id + "-image");
	if (image) {
		resizeImage(image, bento);
		var container = document.getElementById(bento.id + "-image-container");
		resizeContainer(bento, image, container);
	}
}

function handleSearch(field) {
	var textLength = field.value.length;
	var index = 1;

	var images = document.querySelectorAll('.result-image');
	[].forEach.call(images, function(image) {
		if (index <= textLength) {
			image.style.display = "inline";
		} else {
			image.style.display = "none";
		}
		index += 1;
	});
}

function handleSliderEvent(event, ui) {
	var bento = event.target.parentNode;
	var nameRoot = bento.id;
	var image = document.getElementById(nameRoot + "-image");
	var container = document.getElementById(nameRoot + "-image-container");

	// change the value into a float (e.g. 1.5);
	var value = ui.value / 100;

	// scale the image
	image.style.width = Math.round(image.originalWidth * value) + "px";
	image.style.height = Math.round(image.originalHeight * value) + "px";

	// now resize the container so the image can be moved around
	resizeContainer(bento, image, container);
	unsaved();
}

$(function() {
	$(".image-slider").slider({
		orientation: "vertical",
		min: 100,
		max: 400,
		slide: function(event, ui) {
			handleSliderEvent(event, ui);
		}
	});
});

function handleHorizontalDrag(target, movement) {
	var index;
	var width;
	var newWidth;
	var newLeft;
	if (movement !== 0) {
		for (index = 0; index < target.leftDependents.length; ++index) {
			var leftDependent = target.leftDependents[index];
			width = parseFloat(getComputedStyle(leftDependent).width);
// console.log("movement: "+movement+", left: "+leftDependent.id+", width: "+width);
			newWidth = width + movement;
			leftDependent.style.width = newWidth + "px";

			var bentos = leftDependent.getElementsByClassName("bento");
			for (var i = 0; i < bentos.length; ++i) {
				resizeBento(bentos[i]);
			}
		}

		for (index = 0; index < target.rightDependents.length; ++index) {
			var rightDependent = target.rightDependents[index];
			width = parseFloat(getComputedStyle(rightDependent).width);
// console.log("movement: "+movement+", right: "+rightDependent.id+", width: "+width);
			newWidth = width - movement;
			newLeft = parseFloat(getComputedStyle(rightDependent).left, 10) + movement;
			rightDependent.style.left = newLeft + "px";
			rightDependent.style.width = newWidth + "px";

			var bentos = rightDependent.getElementsByClassName("bento");
			for (var i = 0; i < bentos.length; ++i) {
				resizeBento(bentos[i]);
			}
		}
		unsaved();
	}
}

function handleVerticalDrag(target, movement) {
	var index;

	if (movement !== 0) {
		for (index = 0; index < target.topDependents.length; ++index) {
			var topDependent = target.topDependents[index];
			var newHeight = parseFloat(getComputedStyle(topDependent).height, 10) + movement;
			topDependent.style.height = newHeight + "px";

			var bentos = topDependent.getElementsByClassName("bento");
			for (var i = 0; i < bentos.length; ++i) {
				resizeBento(bentos[i]);
			}
		}

		for (index = 0; index < target.bottomDependents.length; ++index) {
			var bottomDependent = target.bottomDependents[index];
			bottomDependent.style.top = (parseFloat(getComputedStyle(bottomDependent, null).top, 10) + movement) + "px";
			var newHeight = parseFloat(getComputedStyle(bottomDependent).height, 10) - movement;
			bottomDependent.style.height = newHeight + "px";

			var bentos = bottomDependent.getElementsByClassName("bento");
			for (var i = 0; i < bentos.length; ++i) {
				resizeBento(bentos[i]);
			}
		}
		unsaved();
	}
}

$(function() {
	var divider;

	// Template #1
	divider = document.getElementById("divider-1-1");
	divider.leftDependents = [document.getElementById("column-1-1")];
	divider.rightDependents = [
		document.getElementById("column-1-2"),
		document.getElementById("divider-container-1-2")];

	divider = document.getElementById("divider-1-2");
	divider.leftDependents = [
		document.getElementById("column-1-2"),
		document.getElementById("divider-container-1-1")];
	divider.rightDependents = [
		document.getElementById("column-1-3"),
		document.getElementById("divider-1-3"),
		document.getElementById("divider-container-1-3")];

	divider = document.getElementById("divider-1-3");
	divider.topDependents = [document.getElementById("column-1-4")];
	divider.bottomDependents = [document.getElementById("column-1-5")];

	$("#divider-1-1").draggable({
		axis: "x",
		containment: "#divider-container-1-1",
		drag: function(event, ui) {
			handleHorizontalDrag(event.target, ui.position.left - ui.originalPosition.left);
			ui.originalPosition.left = ui.position.left;
		},
	});

	$("#divider-1-2").draggable({
		axis: "x",
		containment: "#divider-container-1-2",
		drag: function(event, ui) {
			handleHorizontalDrag(event.target, ui.position.left - ui.originalPosition.left);
			ui.originalPosition.left = ui.position.left;
		}
	});

	$("#divider-1-3").draggable({
		axis: "y",
		containment: "#divider-container-1-3",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	// Template #2
	divider = document.getElementById("divider-2-1");
	divider.leftDependents = [
		document.getElementById("column-2-1"),
		document.getElementById("divider-2-2"),
		document.getElementById("divider-container-2-2")];
	divider.rightDependents = [
		document.getElementById("column-2-2"),
		document.getElementById("divider-2-3"),
		document.getElementById("divider-2-4"),
		document.getElementById("divider-container-2-3"),
		document.getElementById("divider-container-2-4")];

	divider = document.getElementById("divider-2-2");
	divider.topDependents = [document.getElementById("column-2-3")];
	divider.bottomDependents = [document.getElementById("column-2-4")];

	divider = document.getElementById("divider-2-3");
	divider.topDependents = [document.getElementById("column-2-5")];
	divider.bottomDependents = [
		document.getElementById("column-2-6"),
		document.getElementById("divider-container-2-4")];

	divider = document.getElementById("divider-2-4");
	divider.topDependents = [
		document.getElementById("column-2-6"),
		document.getElementById("divider-container-2-3")];
	divider.bottomDependents = [document.getElementById("column-2-7")];

	$("#divider-2-1").draggable({
		axis: "x",
		containment: "#divider-container-2-1",
		drag: function(event, ui) {
			handleHorizontalDrag(event.target, ui.position.left - ui.originalPosition.left);
			ui.originalPosition.left = ui.position.left;
		}
	});

	$("#divider-2-2").draggable({
		axis: "y",
		containment: "#divider-container-2-2",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	$("#divider-2-3").draggable({
		axis: "y",
		containment: "#divider-container-2-3",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	$("#divider-2-4").draggable({
		axis: "y",
		containment: "#divider-container-2-4",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	// Template #3
	divider = document.getElementById("divider-3-1");
	divider.leftDependents = [
		document.getElementById("column-3-1"),
		document.getElementById("divider-3-3"),
		document.getElementById("divider-container-3-3")];
	divider.rightDependents = [
		document.getElementById("column-3-2"),
		document.getElementById("divider-3-4"),
		document.getElementById("divider-container-3-4")];

	divider = document.getElementById("divider-3-2");
	divider.leftDependents = [
		document.getElementById("column-3-2"),
		document.getElementById("divider-3-4"),
		document.getElementById("divider-container-3-4"),
		document.getElementById("divider-container-3-1")];
	divider.rightDependents = [
		document.getElementById("column-3-3"),
		document.getElementById("divider-3-5"),
		document.getElementById("divider-container-3-5")];

	divider = document.getElementById("divider-3-3");
	divider.topDependents = [document.getElementById("column-3-4")];
	divider.bottomDependents = [document.getElementById("column-3-5")];

	divider = document.getElementById("divider-3-4");
	divider.topDependents = [document.getElementById("column-3-6")];
	divider.bottomDependents = [document.getElementById("column-3-7")];

	divider = document.getElementById("divider-3-5");
	divider.topDependents = [document.getElementById("column-3-8")];
	divider.bottomDependents = [document.getElementById("column-3-9")];

	$("#divider-3-1").draggable({
		axis: "x",
		containment: "#divider-container-3-1",
		drag: function(event, ui) {
			handleHorizontalDrag(event.target, ui.position.left - ui.originalPosition.left);
			ui.originalPosition.left = ui.position.left;
		}
	});

	$("#divider-3-2").draggable({
		axis: "x",
		containment: "#divider-container-3-2",
		drag: function(event, ui) {
			handleHorizontalDrag(event.target, ui.position.left - ui.originalPosition.left);
			ui.originalPosition.left = ui.position.left;
		}
	});

	$("#divider-3-3").draggable({
		axis: "y",
		containment: "#divider-container-3-3",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	$("#divider-3-4").draggable({
		axis: "y",
		containment: "#divider-container-3-4",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

	$("#divider-3-5").draggable({
		axis: "y",
		containment: "#divider-container-3-5",
		drag: function(event, ui) {
			handleVerticalDrag(event.target, ui.position.top - ui.originalPosition.top);
			ui.originalPosition.top = ui.position.top;
		}
	});

});

function setPreviewLink (template) {
	var linkText;
	if (template.wrapperType) {
		linkText = "second-harvest.php?ut=" + template.wrapperType + "&uc=" + template.unloadCount + "&tid=" + template.giftboxId;
	} else {
		linkText = "preview.php?id=" + template.giftboxId;
	}
	$("#preview-link").val(template.appURL + linkText);
	if (template.giftboxId) {
		$("#send-link-input").val(template.appURL + linkText);
	} else {
		$("#send-link-input").val("");
	}
}

function stack(top, middle, bottom) {
	var top_template = document.getElementById(top);

	// Only shuffle the stack if the top is not on top
	if (!Object.is(top_template, window.top_template)) {
		closeImageDialog();
		var middle_template = document.getElementById(middle);
		var bottom_template = document.getElementById(bottom);
		window.top_template = top_template;

		setPreviewLink(top_template);
		top_template.style.zIndex = 3;
		middle_template.style.zIndex = 2;
		bottom_template.style.zIndex = 1;
	}
}

function calcTop(bento, image, container) {
	var top = null;
	var imageTop = parseInt(image.style.top, 10);
	var bentoHeight = parseInt(bento.height, 10);
	var containerHeight = parseInt(container.style.height, 10);
	top = imageTop - ((containerHeight - bentoHeight)/2);
	return top + "px";
}

function calcLeft(bento, image, container) {
	var left = null;
	var imageLeft = parseInt(image.style.left, 10);
	var bentoWidth = parseInt(bento.width, 10);
	var containerWidth = parseInt(container.style.width, 10);
	left = imageLeft - ((containerWidth - bentoWidth)/2);
	return left + "px";
}

function saveButton() {
	if (!window.top_template.giftboxId) {
		$('#save-name').val(window.top_template.giftboxName);
		$('#save-dialog').dialog('open');
	} else {
		save();
	}

}
function save() {
	if ($("#save-dialog" ).dialog("isOpen")) {
		// Get the name
		var giftboxName = $("#save-name").val();
		$("#save-dialog" ).dialog("close");
		window.top_template.giftboxName = giftboxName;
	}
	var giftboxName = window.top_template.giftboxName;

	openStatus("Save", "Saving " + giftboxName + "...");
	var template = window.top_template;
	var giftboxId = template.giftboxId;
	var cssId = template.id;
	var letterText = template.letterText;
	var wrapperType = template.wrapperType;
	var unloadCount = template.unloadCount;
	var userAgent = navigator.userAgent;
	var giftbox = {
		id: giftboxId,
		css_id: cssId,
		css_width: template.clientWidth,
		css_height: template.clientHeight,
		name: giftboxName,
		letter_text: letterText,
		wrapper_type: wrapperType,
		unload_count: unloadCount,
		user_agent: userAgent,
		bentos: new Array(),
		dividers: new Array(),
		columns: new Array(),
		attachments: new Array()
	};

	$("#"+template.id+" div.bento").each(function(i) {
		var bento = new Object();
		bento.giftbox_id = giftboxId;
		bento.css_id = $(this).attr("id");
		bento.css_width = $(this).css("width");
		bento.css_height = $(this).css("height");
		bento.css_top = $(this).css("top");
		bento.css_left = $(this).css("left");
		bento.image_file_name = null;
		bento.download_file_name = null;
		bento.download_mime_type = null;
		bento.content_uri = null;
		bento.slider_value = null;
		bento.image_width = null;
		bento.image_height = null;
		bento.image_top = null;
		bento.image_left = null;
		bento.image_left_in_container = null;
		bento.image_top_in_container = null;
		bento.image_hyperlink = null;

		giftbox.bentos[i] = bento;
		var image = document.getElementById(bento.css_id + "-image");
		if (image) {
			bento.image_file_name = this.image_file_name;
			bento.slider_value = $("#"+bento.css_id+"-slider").slider("value");
			var container = document.getElementById(bento.css_id + '-image-container');
			bento.image_width = image.style.width;
			bento.image_height = image.style.height;
			bento.image_top = calcTop(bento, image, container);
			bento.image_left = calcLeft(bento, image, container);
			bento.image_left_in_container = image.style.left;
			bento.image_top_in_container = image.style.top;
			bento.image_hyperlink = image.hyperlink;
			var croppedImage = createCroppedImage(bento, image, container);
			uploadFileData(croppedImage.src, bento.css_id+"-cropped_"+this.image_file_name);
			if (!image.saved) {
				if (image.file) {
					uploadFile(image.file);
				} else {
					uploadFileData(image.src, this.image_file_name);
				}
				image.saved = true;
			}
		}
		if (this.video || this.audio) {
			bento.download_file_name = this.download_file_name;
			bento.download_mime_type = this.download_mime_type;
			if (this.file) {
				uploadFile(this.file);
				this.file = null;
			}
		}
		if (this.contentURI) {
			bento.content_uri = this.contentURI;
		}
	});

	$("#add-attachment-desktop > a").each(function(i) {
		var attachment = new Object();
		attachment.download_file_name = this.download_file_name;
		attachment.download_mime_type = this.download_mime_type;
		giftbox.attachments[i] = attachment;
		if (this.file) {
			uploadFile(this.file);
			this.file = null;
		}
	});

	$("#"+template.id+" div.divider").each(function(i) {
		var divider = new Object();
		divider.giftbox_id = giftboxId;
		divider.css_id = $(this).attr("id");
		divider.css_width = $(this).css("width");
		divider.css_height = $(this).css("height");
		divider.css_top = $(this).css("top");
		divider.css_left = $(this).css("left");
		giftbox.dividers[i] = divider;
	});

	$("#"+template.id+" div.divider-container").each(function(i) {
		var container = new Object();
		container.giftbox_id = giftboxId;
		container.css_id = $(this).attr("id");
		container.css_width = $(this).css("width");
		container.css_height = $(this).css("height");
		container.css_top = $(this).css("top");
		container.css_left = $(this).css("left");
		giftbox.dividers[giftbox.dividers.length] = container;
	});

	$("#"+template.id+" div.column").each(function(i) {
		var column = new Object();
		column.giftbox_id = giftboxId;
		column.css_id = $(this).attr("id");
		column.css_width = $(this).css("width");
		column.css_height = $(this).css("height");
		column.css_top = $(this).css("top");
		column.css_left = $(this).css("left");
		column.parent_css_id = $(this).parent().attr("id");
		giftbox.columns[giftbox.columns.length] = column;
	});

	// Save the template first
	$.post("save_token_ajax.php", giftbox, function(result) {
		closeStatus();
		if (result.status === "SUCCESS") {
			template.giftboxId = result.giftbox_id;
			template.appURL = result.app_url;
			setPreviewLink(template);
		} else if (result.status === "ERROR") {
			openMessage("Error", "Save failed with the following error:  "+result.message);
		} else {
			openMessage("Save", "Save failed!");
		}
	}).fail(function() {
		openMessage("Save", "Save failed!");
	});
	saved();
}

function send() {
	var giftboxId = window.top_template.giftboxId;
	if (!giftboxId) {
		openMessage("Send", "The Token must be saved before it can be sent.");
	} else {
		$('#send-dialog').dialog('open');
	}
}

function preview() {
	var giftboxId = window.top_template.giftboxId;
	var unloadType = window.top_template.wrapperType;
	var unloadCount = window.top_template.unloadCount
	if (!giftboxId) {
		openMessage("Preview", "The Token must be saved before it can be previewed.");
	} else {
		if (window.top_template.wrapperType) {
			window.open("second-harvest.php?ut=" + unloadType + "&uc=" + unloadCount + "&tid=" + giftboxId, "_blank");
		} else {
			window.open("preview.php?id=" + giftboxId, "_blank");
		}
	}
}

function saveLetter() {
	var letterTextInput = document.getElementById("letter-text");
	var newValue = letterTextInput.value;
	var oldValue = window.top_template.letterText;
	if (newValue !== oldValue) {
		window.top_template.letterText = newValue;
		unsaved();
	}
}

function wrapper() {
	$('#wrapper-type').val(window.top_template.wrapperType);
	$('#unload-count').spinner("value", window.top_template.unloadCount);
	$('#wrapper-dialog').dialog('open');
}

function save_wrapper() {
	var wrapperType = document.getElementById;
	var unloadCount = document.getElementById("unload-count");
	window.top_template.wrapperType = $("#wrapper-type").val();
	window.top_template.unloadCount = $("#unload-count").spinner("value");
	$("#wrapper-dialog" ).dialog("close");
}

function inputURL(site) {
	$('#url').val("");
	$('#url-dialog').dialog({title: site});
	$('#url-dialog').dialog('open');
}

function openURL() {
	// Get the url
	var url = document.getElementById("url").value;
	var title = $("#url-dialog").dialog("option", "title");
	$("#url-dialog").dialog("close");
	if (isYouTube(url)) {
		openYouTube(url);
	} else if (isSoundCloud(url)) {
		openSoundCloud(url);
	} else if (isSpotify(url)) {
		openSpotify(url);
	} else {
		var error = "The URL specified is not a valid "+title+" URL.\n\n"+url;
		console.log(error);
		alert(error);
	}
}

function selectSaved() {
	openStatus("Open", "Retrieving saved Tokens...");
	$.get("get_user_tokens_ajax.php", function(data) {
		var output = [];
		$.each(data, function(key, value)
		{
		  output.push('<option value="'+ value['id'] + '"' + ((key === 0) ? " selected" : "") +'>' + value['name'] + '</option>');
		});
		$('#token-list').html(output.join(''));
		closeStatus();
		$('#open-dialog').dialog('open');
	});
}

function loadSaved() {
	var tokenId = $('#token-list').find(":selected").val();
	if (tokenId) {
		$('#open-dialog').dialog('close');
		openStatus("Loading", "Loading saved Token...");
		$.get("get_token_ajax.php", {id: tokenId}, function(data) {
			var token = data;
			closeStatus();

			// Bring the correct template to the top
			if (token.css_id === 'template-1') {
				stack('template-1', 'template-2', 'template-3');
			} else if (token.css_id === 'template-2') {
				stack('template-2', 'template-3', 'template-1')
			} else {
				stack('template-3', 'template-1', 'template-2')
			}

			// Populate the top template properties
			window.top_template.giftboxId = token.id;
			window.top_template.giftboxName = token.name;
			window.top_template.appURL = token.app_url;
			window.top_template.letterText = token.letter_text;
			window.top_template.wrapperType = token.wrapper_type;
			window.top_template.unloadCount = token.unload_count;
			setPreviewLink(window.top_template);

			// Bento properties
			var index;
			var bento;
			for (index = 0; index < token.bentos.length; ++index) {
				bento = document.getElementById(token.bentos[index].css_id);
				bento.style.width = "100%";
				bento.style.height = "100%";
				bento.style.top = "0px";
				bento.style.left = "0px";
				clearBento(bento);
				loadBento(bento, token.bentos[index]);
			}

			// Divider properties
			var divider;
			for (index = 0; index < token.dividers.length; ++index) {
				divider = document.getElementById(token.dividers[index].css_id);
				if (token.dividers[index].parent_css_id.indexOf("column") > -1) {
					divider.style.width = "100%";
				} else {
					divider.style.width = token.dividers[index].css_width;
				}
				divider.style.height = token.dividers[index].css_height;
				divider.style.top = token.dividers[index].css_top;
				divider.style.left = token.dividers[index].css_left;
			}

			$("#add-attachment-desktop").empty();
			for (index = 0; index < token.attachments.length; ++index) {
				appendAttachmentDisplay(token.attachments[index]);
			}

		});
	}
	closeImageDialog();
}

function clearBento(bento) {
	hideControl(bento.id+"-close");
	hideControl(bento.id+"-slider");
	if (bento.imageContainer) {
		bento.removeChild(bento.imageContainer);
		bento.imageContainer = null;
	}
	if (bento.video) {
		bento.removeChild(bento.video);
		bento.video = null;
	}
	if (bento.audio) {
		var closeButton = document.getElementById(bento.audio.id+"-close");
		bento.removeChild(closeButton);
		bento.removeChild(bento.audio);
		bento.audio = null;
	}
	if (bento.iframe) {
		bento.removeChild(bento.iframe);
		bento.iframe = null;
	}
	bento.contentURI = null;
	bento.file = null;
}

function loadBento(bento, savedBento) {
	if (savedBento.content_uri) {
		if (isYouTube(savedBento.content_uri)) {
			addYouTube(bento, savedBento.content_uri);
		} else if (isSoundCloud(savedBento.content_uri)) {
			addSoundCloud(bento, savedBento.content_uri);
		} else if (isSpotify(savedBento.content_uri)) {
			var trackId = spotifyTrackId(savedBento.content_uri);
			addSpotify(bento, trackId);
		}
	}
	if (savedBento.image_file_name) {
		addImage(bento, savedBento.image_file_path, null, savedBento);
		bento.image_file_name = savedBento.image_file_name;
		if (savedBento.image_hyperlink) {
			var image = $("#"+bento.id+"-image");
			image[0].hyperlink = savedBento.image_hyperlink;
			showControl(bento.id+"-link-icon");
		}
	}
	if (savedBento.download_file_name) {
		bento.download_file_name = savedBento.download_file_name;
		bento.download_mime_type = savedBento.download_mime_type;
		if (bento.download_mime_type.match(videoType)) {
			addVideo(bento, savedBento.download_file_path, null, savedBento);
		}
		if (bento.download_mime_type.match(audioType)) {
			addAudio(bento, savedBento.download_file_path, null, savedBento);
		}
	}
}

function createCroppedImage (bento, image, container) {
	// draw the original image to a scaled canvas
	var canvas = document.createElement('canvas');
	var imageStyle = getComputedStyle(image);
	var width = parseInt(imageStyle.width, 10);
	var height = parseInt(imageStyle.height, 10);
	canvas.width = width;
	canvas.height = height;
	var context = canvas.getContext('2d');
	context.drawImage(image, 0, 0, width, height);

	// now crop the canvas
	var containerStyle = getComputedStyle(container);
	var containerLeft = parseInt(containerStyle.left, 10);
	var imageLeft = parseInt(imageStyle.left, 10);
	var sourceX = (containerLeft * -1) - imageLeft;
	var containerTop = parseInt(containerStyle.top, 10);
	var imageTop = parseInt(imageStyle.top, 10);
	var sourceY = (containerTop * -1) - imageTop;
	var croppedCanvas = document.createElement('canvas');
	croppedCanvas.width = parseInt(bento.css_width, 10);
	croppedCanvas.height = parseInt(bento.css_height, 10);
	var croppedContext = croppedCanvas.getContext('2d');
	var cropWidth = parseInt(bento.css_width, 10);
	var cropHeight = parseInt(bento.css_height, 10);
	croppedContext.drawImage(canvas, sourceX, sourceY, cropWidth, cropHeight, 0, 0,cropWidth, cropHeight);
	var croppedImage = new Image();
	croppedImage.src = croppedCanvas.toDataURL();
	return croppedImage;
}

function featureNotAvailable(feature) {
	openMessage(feature, "This feature is not available yet.");
}

function standardFeature() {
	openMessage("Add Hyperlink", "This feature is only available to \"Standard\" level and higher members.");
}

function selectSidebarTab(tab) {
	var selectedIcon = $("#"+tab.id);

	// restore all icons
	$(".sidebar-tab").each(function(i) {
		$(this).removeClass("sidebar-tab-hover");
		$(this).addClass("sidebar-tab-hover");
		$(this).removeClass($(this).attr("id"));
		$(this).addClass($(this).attr("id"));
		$(this).removeClass($(this).attr("id")+"-selected");
	});

	// set the selected icon
	selectedIcon.removeClass("sidebar-tab-hover");
	selectedIcon.removeClass(tab.id);
	selectedIcon.addClass(tab.id+"-selected");

	// hide all sidebar tab containers
	$(".sidebar-tab-container").css("display", "none");

	// show the selected container
	$("#"+tab.id+"-container").css("display", "block");
}

function showTemplates(number) {
	var selectedButton = $("#template-number-"+number);

	// restore all number buttons
	$(".template-number").each(function(i) {
		$(this).removeClass("template-number-hover");
		$(this).addClass("template-number-hover");
		$(this).removeClass("template-number-selected");
	});

	// set the selected number button
	selectedButton.removeClass("template-number-hover");
	selectedButton.addClass("template-number-selected");

	// show only those template for the selected button
	if (number === "all") {
		$(".template-thumbnail").css("display", "inline-block");
	} else {
		$(".template-thumbnail").css("display", "none");
		$("#template-thumbnail-"+number).css("display", "inline-block");
	}
}

function bentoClick(bento) {
	$("#add-dialog").attr("target-bento", bento.id);
	$("#add-dialog").dialog("open");
}

function textIconClicked() {
	$("#add-dialog").dialog("open");
	selectAddNav("add-letter");
}

function selectAddNav(navId) {
	var selectedNav = $("#"+navId);

	// restore all link styles
	$(".add-nav-item").each(function(i) {
  		$(this).removeClass("add-nav-item-hover");
		$(this).addClass("add-nav-item-hover");
		$(this).removeClass("add-nav-item-selected");

	});

	// set the selected icon
	selectedNav.removeClass("add-nav-item-hover");
	selectedNav.addClass("add-nav-item-selected");

	// hide all sidebar nav containers
	$(".add-content-container").css("display", "none");

	// show the selected container
	$("#"+navId+"-container").css("display", "block");
}

function selectThumbnail(thumbnail) {
	var selectedThumbnail = $("#"+thumbnail.id);

	// restore all number buttons
	$(".thumbnail-container").each(function(i) {
		$(this).removeClass("thumbnail-container-hover");
		$(this).addClass("thumbnail-container-hover");
		$(this).removeClass("thumbnail-container-selected");
	});

	// set the selected number button
	selectedThumbnail.removeClass("thumbnail-container-hover");
	selectedThumbnail.addClass("thumbnail-container-selected");
}

function removeSelection(parentId) {
	var jqueryContainer = $("#"+parentId+" > .thumbnail-container-selected");
	jqueryContainer.removeClass("thumbnail-container-selected");
	jqueryContainer.addClass("thumbnail-container-hover");
}

function doAdd() {
	var jqueryObject;
	var bentoId;
	var element;
	var bento;
	$('#add-dialog').dialog('close');

	// LETTER
	saveLetter();

	bentoId = $("#add-dialog").attr("target-bento");
	bento = $("#"+bentoId)[0];

	// IMAGE
	jqueryObject = $("#add-images-desktop > .thumbnail-container-selected > .inner-thumbnail-container > img");
	if (jqueryObject.size() > 0) {
		removeSelection("add-images-desktop");
		element = jqueryObject[0];
		addImage(bento, element.src, element.file, null);
		unsaved();
	}

	// VIDEO/AUDIO
	jqueryObject = $("#add-av-desktop > .thumbnail-container-selected > .inner-thumbnail-container > img");
	if (jqueryObject.size() == 0) {
		jqueryObject = $("#add-av-desktop > .thumbnail-container-selected > .inner-thumbnail-container > video");
	}
	if (jqueryObject.size() > 0) {
		removeSelection("add-av-desktop");
		element = jqueryObject[0];
		if (element.file) {
			if (element.file.type.match(audioType)) {
				addImage(bento, element.src, null, null);
				bento.image_file_name = element.file.name.replace(".", "_") + ".jpg";
				addAudio(bento, window.URL.createObjectURL(element.file), element.file, null);
			} else if (element.file.type.match(videoType)) {
				addVideo(bento, null, element.file, null);
			}
		} else if (element.youTubeURL) {
			addYouTube(bento, element.youTubeURL);
		} else if (element.spotifyTrackId) {
			addSpotify(bento, element.spotifyTrackId)
		} else if (element.soundCloudURL) {
			addSoundCloud(bento, element.soundCloudURL);
		}
	}

	// ATTACHMENT

}

function hidePalette() {
	$("#palette").animate({width: "25px"});
	$("#palette-body").addClass("hidden")
	$("#hide-palette").addClass("hidden");
	$("#show-palette").removeClass("hidden");
}

function showPalette() {
	$("#palette").animate({width: "260px"});
	$("#show-palette").addClass("hidden");
	$("#hide-palette").removeClass("hidden");
	$("#palette-body").removeClass("hidden");
}

function imageClicked(image) {
	selectImage(image);
	event.stopPropagation();
}

function videoClicked(event, video) {
	event.stopPropagation();
}

function selectImage(image) {
	var linkAddress = image[0].hyperlink;

	// de-select the current target image
	var currentImage = getImageDialogImage();
	if (currentImage) {
		deselectImage(currentImage);
	}

	// Set the dialog's target image
	setImageDialogImage(image);
	
	// Change all the dialog values to match the target image
	$("#hyperlink-text").val(linkAddress);

	// Set the hyperlink control buttons
	setHyperlinkButtons(linkAddress);

	// Highlight the bento
	image.closest(".bento").addClass("selected-bento");

	// Show the dialog if it's not already open
	openImageDialog();
}

function deselectImage(image) {
	if (image) {
		image.closest(".bento").removeClass("selected-bento");
	}
}

function openHyperlinkInput() {
	$("#hyperlink-dialog-url").val($("#hyperlink-text").val());
	$("#add-hyperlink-dialog").dialog("open");
}

function addImageHyperlink() {
	var validLink = false;

	// Get the link address from the hyperlink input dialog
	var linkAddress = $("#hyperlink-dialog-url").val();

	// Get the target image from the image dialog
	var image = getImageDialogImage();

	if (linkAddress.length > 0) {

		// Make sure it has an 'http' or 'https' prefix
		if (linkAddress.substring(0, 4).toLowerCase() !== 'http') {
			linkAddress = "http://" + linkAddress;
		}

		// Validate the link address
		validLink = true;
	} else {
		validLink = true;
	}

	if (validLink) {
		setHyperlink(linkAddress);
	}

}

function setHyperlink(linkAddress) {
	// Set the image dialog hyperlink text
	$("#hyperlink-text").val(linkAddress);

	// Set the images hyperlink text
	getImageDialogImage()[0].hyperlink = linkAddress;

	// Adjust the image dialogs buttons
	setHyperlinkButtons(linkAddress)
	
	// Show the link icon
	var icon = getImageDialogImage().parent().parent().children(".bento-link-icon");
	if (linkAddress && linkAddress.length > 0) {
		showControl(icon.attr("id"), null);
	} else {
		hideControl(icon.attr("id"));
	}
	
	// Close the modal input dialog
	$("#add-hyperlink-dialog").dialog("close");
}

function setHyperlinkButtons(linkAddress) {
	if (linkAddress && linkAddress.length > 0) {
		$("#add-hyperlink-button").css("display", "none");
		$("#remove-hyperlink-button").css("display", "inline-block");
		$("#change-hyperlink-button").css("display", "inline-block");
	} else {
		$("#add-hyperlink-button").css("display", "block");
		$("#remove-hyperlink-button").css("display", "none");
		$("#change-hyperlink-button").css("display", "none");
	}
}

function removeHyperlink() {
	setHyperlink(null);
}

function changeHyperlink() {
	openHyperlinkInput();
}

function removeImage(image) {
	deselectImage(image);
	
	if (image.is(getImageDialogImage())) {

		// Clear the hyperlink text in the image dialog
		$("#hyperlink-text").val(null);

		// Clear the image dialog's target image
		setImageDialogImage(null);

		// Close the image dialog
		closeImageDialog();
	}
}

function openImageDialog() {
	$(imageDialogSelector).dialog("open");
}

function closeImageDialog() {
	$(imageDialogSelector).dialog("close");
}

function setImageDialogImage(image) {
	$(imageDialogSelector).data("target-image", image);
}

function getImageDialogImage() {
	return $(imageDialogSelector).data("target-image");
}