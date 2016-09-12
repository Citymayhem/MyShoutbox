// MyShoutbox for MyBB
// (c) Pirata Nervo, www.consoleworld.net
//
// Based off:
// SpiceFuse AJAX ShoutBox for MyBB
// (c) Asad Niazi, www.spicefuse.com!
//
// Code is copyrighted and does not belong to public domain.
// Copying or reusing in different forms/softwares isn't allowed.
// 
//
//
var ShoutboxMessageTypes = {
	Text: 1,
	Image: 2
};

var ShoutBox = {
	
	refreshInterval: 60,
	lastID: 0,
	totalEntries: 0,
	firstRun: true,
	MaxEntries: 5,
	Shouts: new Array(),
	shouting: false,
	orderShoutboxDesc: false,
	lang: ['Shouting...', 'Shout Now!', 'Loading...', 'Flood check! Please try again in <interval> seconds.', 'Couldn\'t shout or perform action. Please try again!', 'Sending message...', 'Send!'],
	newLang: {},
	Templates: {},
	ActiveUserId: 0,
	SelectedMessageType: ShoutboxMessageTypes.Text,
	
	getLanguageValue: function(key){
		var languageValue = ShoutBox.newLang[key];
		if(languageValue === undefined){
			return "";
		}
		
		return languageValue;
	},

	// Escape HTML. Source: http://stackoverflow.com/a/6020820
	// Escape a string for HTML interpolation.
	escape: function(string) {
		// List of HTML entities for escaping.
		var htmlEscapes = {
		  '&': '&amp;',
		  '<': '&lt;',
		  '>': '&gt;',
		  '"': '&quot;',
		  "'": '&#x27;',
		  '/': '&#x2F;'
		};

		// Regex containing the keys listed immediately above.
		var htmlEscaper = /[&<>"'\/]/g;
		return ('' + string).replace(htmlEscaper, function(match) {
			return htmlEscapes[match];
		});
	},
	
	startRefreshTimer: function(){
		setTimeout(function() { ShoutBox.getShouts(ShoutBox.startRefreshTimer); }, ShoutBox.refreshInterval * 1000);
	},
	
	load: function(){
		$.get("xmlhttp.php?action=mysb_get_templates", function(data){
			ShoutBox.hideAlert();
			ShoutBox.Templates = data;
			
			ShoutBox.renderStructure();
			
			ShoutBox.getShouts(ShoutBox.startRefreshTimer);
		}).error(function(){
			ShoutBox.alert("Error loading shoutbox. Trying again...", -1);
			setTimeout(ShoutBox.load, 3000);
		});
	},
	
	getShouts: function(finishedCallback=null) {
		/*
		if (typeof Ajax == 'object') {
			new Ajax.Request('xmlhttp.php?action=show_shouts&last_id='+ShoutBox.lastID, {method: 'get', onComplete: function(request) { ShoutBox.shoutsLoaded(request); } });
		}
		*/		
		$.get("xmlhttp.php?action=get_shouts&last_id="+ShoutBox.lastID, function(data){
			ShoutBox.shoutsRetrieved(data);
		}).always(function(){
			if(finishedCallback != null){
				finishedCallback();
			}
		});
	},
	
	shoutsRetrieved: function(response) {
		var lastID = response.lastShoutId;
		var messages = response.messages;
		
		ShoutBox.ActiveUserId = response.currentUserId;

		if (lastID <= ShoutBox.lastID) {
			return;
		}

		var numberOfShouts = messages.length;

		for (var i = numberOfShouts - 1; i >= 0; i--) 
		{
			var currentShout = messages[i];
			var shoutMessageViewModel = ShoutBox.buildShoutMessageViewModel(currentShout);
			
			if(ShoutBox.Shouts.length != 0){
				var previousEntryViewModel = ShoutBox.Shouts[ShoutBox.Shouts.length - 1];
				
				if(ShoutBox.shouldMergeShouts(previousEntryViewModel, currentShout)){
					previousEntryViewModel.messages.push(shoutMessageViewModel);
					continue;
				}
			}
			
			var shoutViewModel = ShoutBox.buildShoutViewModel(currentShout, shoutMessageViewModel);
			ShoutBox.Shouts.push(shoutViewModel);
		}

		ShoutBox.lastID = lastID;

		var shouldScrollToBottom = ShoutBox.firstRun && !ShoutBox.orderShoutboxDesc;
		
		ShoutBox.renderShoutbox();
		
		if(shouldScrollToBottom){
			ShoutBox.scrollToBottomOfMessages();
		}
		else if(ShoutBox.firstRun) {
			ShoutBox.scrollToTopOfMessages();
		}
		
		ShoutBox.firstRun = false;
	},
	
	pvtAdd: function(uid) {
		var msg = $("#shout_data").val();
		$("#shout_data").val('/pvt ' + uid + ' ' + msg);
	},

	postShout: function() {
		message = $("#shout_data").val();
		if (message == "" || ShoutBox.shouting) {
			return false;
		}

		// Disable input, make button say "Shouting..."
		$("#shouting-status").html(ShoutBox.lang[0]);
		$("#shout_data").attr("disabled", "disabled");
		$("#shouting-status").attr("disabled", "disabled");
		ShoutBox.shouting = true;

		if(ShoutBox.SelectedMessageType == ShoutboxMessageTypes.Image){
			ShoutBox.postImageShout(message);
		}
		else {
			ShoutBox.postTextShout(message);
		}
	},
	
	postImageShout: function(url){
		var request = { imageUrl: url };
		
		// TODO: Error
		$.post("xmlhttp.php?action=mysb_add_image_shout", request)
			.done(function(data){
				ShoutBox.emptyMessageBox();
				ShoutBox.indicateShoutPostingFinished();
			}
		);
	},
	
	postTextShout: function(message){
		postData = "shout_data="+encodeURIComponent(message).replace(/\+/g, "%2B");
		$.post("xmlhttp.php?action=add_shout", postData)
			.done(function(data){
				ShoutBox.postedShout(data, message);
			}
		);
	},

	postedShout: function(responseData, message) {
		if (responseData.indexOf("success") > -1) {
			// Empty text box
			$("#shout_data").val("");
		}
		// Super secret /delete command
		else if (responseData.indexOf("deleted") > -1) {
			ShoutBox.firstRun = 1;
			ShoutBox.lastID = 0;
			ShoutBox.alert("Shouts deleted as requested.");
		}
		else if (responseData.indexOf('flood') > -1) {
			var split = new Array();
			split = responseData.split('|');			
			var interval = split[1]; 
			ShoutBox.alert(ShoutBox.lang[3].replace('<interval>', interval));
		}
		else {
			ShoutBox.alert(ShoutBox.lang[4]);
		}

		// Reset button, re-enable text box, reload shouts
		$("#shouting-status").html(ShoutBox.lang[1]);
		$("#shout_data").removeAttr("disabled");
		$("#shouting-status").removeAttr("disabled");
		ShoutBox.resizeMessageBoxToFitContents();
		ShoutBox.shouting = false;
		ShoutBox.getShouts();
	},
	
	// report shout
	reportShout: function(reason, id) {
		
		reason = reason;
		sid = parseInt(id);
		
		if (reason == "" || sid == "") {
			return false;
		}

		postData = "reason="+encodeURIComponent(reason).replace(/\+/g, "%2B")+"&sid="+sid;
		//new Ajax.Request('xmlhttp.php?action=report_shout', {method: 'post', postBody: postData, onComplete: function(request) { ShoutBox.shoutReported(request); }});
		$.post("xmlhttp.php?action=report_shout",postData)
			.done(function(data){
				ShoutBox.shoutReported(data);
			});
	},

	shoutReported: function(responseData) {
		var msg = "";
		if (responseData == 'invalid_shout') {
			msg = ShoutBox.lang[9];
		}
		else if (responseData == 'already_reported') {
			msg = ShoutBox.lang[11];
		}
		else if (responseData == 'shout_reported') {
			msg = ShoutBox.lang[10];
		}
		if(msg == "")return;
		ShoutBox.alert(msg);
	},
	
	// prompt reason
	promptReason: function(id) {
		
		var reason = prompt("Why are you reporting this shout:", "");
		
		if (reason == "" || reason == null || id == "") {
			return false;
		}
		
		id = parseInt(id);
		
		return ShoutBox.reportShout(reason, id);
	},
	
	// Hide Shout
	deleteShout: function(id, type) {
		if (type == 1) {
			$("#shoutbox_data").html(ShoutBox.lang[2]);
		}
		
		id = parseInt(id);

		//new Ajax.Request('xmlhttp.php?action=delete_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.deletedShout(request, id, type); } });
		$.get("xmlhttp.php?action=delete_shout&id=" + id, function(data){
			ShoutBox.deletedShout(data, id, type);
		});
	},
	
	// Shout hidden
	deletedShout: function(responseData, id, type) {
		if (responseData.indexOf("success") == -1) {
			ShoutBox.alert("Error deleting shout... Try again!");
		} else if (type == 2) {
			ShoutBox.alert("Shout deleted.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.Shouts = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.getShouts();
		} else {
			// Hide the hide button, show the recover button and HIDDEN message
			$("#shout-hide-"+id).css("display", "none");
			$("#shout-hidemsg-"+id).css("display","inline");
			$("#shout-recover-"+id).css("display","inline");
		}

	},
	
	removeShout: function(id, type, message) {
		
		message = ShoutBox.escape(message); // escape HTML before outputting the message
		
		var confirmation = confirm(message);
		
		if (!confirmation)
			return false;
			
		if (type == 1) {
			$("#shoutbox_data").html(ShoutBox.lang[2]);
		}
		
		id = parseInt(id);

		//new Ajax.Request('xmlhttp.php?action=remove_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.removedShout(request, id, type); } });
		$.get("xmlhttp.php?action=remove_shout&id=" + id, function(data){
			ShoutBox.removedShout(data, id, type);
		});
	},
	
	removedShout: function(responseData, id, type) {
		if (responseData.indexOf("success") == -1) {
			ShoutBox.alert("Error removing shout... Try again!");
		} else if (type == 2) {
			ShoutBox.alert("Shout removed.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.Shouts = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.getShouts();
		} else {
			$("#shout-"+id).css("display","none");
		}

	},
	
	// Show shout
	recoverShout: function(id, type) {
		if (type == 1) {
			$("#shoutbox_data").html(ShoutBox.lang[2]);
		}

		id = parseInt(id);
		
		//new Ajax.Request('xmlhttp.php?action=recover_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.recoveredShout(request, id, type); } });
		$.get("xmlhttp.php?action=recover_shout&id=" + id, function(data){
			ShoutBox.recoveredShout(data, id, type);
		});
	},
	
	// Shout shown
	recoveredShout: function(responseData, id, type) {
		if (responseData.indexOf("success") == -1) {
			ShoutBox.alert("Error recovering shout... Try again!");
		} else if (type == 2) {
			ShoutBox.alert("Shout recovered.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.Shouts = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.getShouts();
		} else {
			// Show the hide button, hide the recover button and HIDDEN message
			$("#shout-hide-"+id).css("display","inline");
			$("#shout-hidemsg-"+id).css("display","none");
			$("#shout-recover-"+id).css("display","none");
		}

	},

	disableShout: function() {
		$("#shouting-status").attr("disabled","disabled");
	},
	
	alert: function(msg, hideAfterInMs=5000) {
		$("#shoutbox-alert").css("display","block");
		$("#shoutbox-alert-contents").html(msg);
		
		if(hideAfterInMs != -1)
			setTimeout(function(){ShoutBox.hideAlert();}, hideAfterInMs);
	},
	
	hideAlert: function(){
		$("#shoutbox-alert").css("display","none");
	},
	
	resizeMessageBoxToFitContents: function(){
		/*
		Auto-resize height solution by:
			http://stephanwagner.me/auto-resizing-textarea
			Stephan Wagner
		*/
		var messageBox = $("#shout_data");
		var offset = messageBox[0].offsetHeight - messageBox[0].clientHeight;
		
		messageBox
			.css('height', 'auto')
			.css('height', messageBox[0].scrollHeight + offset);
	},
	
	toggleShoutboxOrder: function() {
		$.get("xmlhttp.php?action=toggle_shoutbox_order")
		.done(function(newStatus) {
			ShoutBox.orderShoutboxDesc = newStatus == 1;
			ShoutBox.renderStructure();
			ShoutBox.renderShoutbox();
		})
		.fail(function(){
			// TODO: Remove debug message. Proper error plz.
			console.log("Error changing shout order");
		});
	},
	
	/*
		Auto-scroll to bottom of shoutbox
		dotnetCarpenter
		http://stackoverflow.com/questions/18614301/keep-overflow-div-scrolled-to-bottom-unless-user-scrolls-up
	*/
	isScrolledToBottom: function() {
		var chatBoxBodyElement = $("#shoutbox_data")[0];
        var chromeScrollInaccuracy = 1;
        return chatBoxBodyElement.scrollHeight - chatBoxBodyElement.clientHeight
                <= chatBoxBodyElement.scrollTop + chromeScrollInaccuracy;
	},
	
	scrollToBottomOfMessages: function() {
		var chatBoxBodyElement = $("#shoutbox_data")[0];
		chatBoxBodyElement.scrollTop = chatBoxBodyElement.scrollHeight - chatBoxBodyElement.clientHeight;
	},
	
	scrollToTopOfMessages: function() {
		var chatBoxBodyElement = $("#shoutbox_data")[0];
		chatBoxBodyElement.scrollTop = 1;
	},
	
	renderShoutbox: function() {
		ShoutBox.renderMessages();
		ShoutBox.renderReverseOrderButton();
	},
	
	renderStructure: function()  {
		if(ShoutBox.orderShoutboxDesc){
			$("#shoutbox_wrapper").removeClass("shoutbox_wrapper_reverse");
			$("#shoutbox_data_wrapper").removeClass("shoutbox_data_wrapper_reverse");
		}
		else {
			$("#shoutbox_wrapper").addClass("shoutbox_wrapper_reverse");
			$("#shoutbox_data_wrapper").addClass("shoutbox_data_wrapper_reverse");
		}
	},
	
	renderMessages: function() {
		var messages = new Array();
		
		for (var i = 0; i < ShoutBox.Shouts.length; i++) {
			var shout = ShoutBox.Shouts[i];
			
			messages.push(ShoutBox.renderShout(shout));
		}
		
		var output = "";
		messages.forEach(function(message) {
			if(ShoutBox.orderShoutboxDesc){
				output = message + output;
			}
			else {
				output = output + message;
			}
		});
		
		$("#shoutbox_data").html(output);
		
		if(ShoutBox.orderShoutboxDesc){
			ShoutBox.scrollToTopOfMessages();
		}
		else {
			ShoutBox.scrollToBottomOfMessages();
		}
	},
	
	renderShout: function(shout){
		var pmMessage = "";
		
		if(shout.pmTargetUserId != null){
			pmMessage = shout.pmTargetUserId == ShoutBox.ActiveUserId
						? "PM to you"
						: "PM to " + shout.pmTargetUsername;
		}
		
		var pmButton = "";
		if(shout.userId != ShoutBox.ActiveUserId){
			pmButton = ShoutBox.renderPmButton(shout.userId);
		}
		
		var messages = "";
		shout.messages.forEach(function(message) {
			switch(message.type){
				case 1:
					messages += ShoutBox.renderTextMessage(message);
					break;
				case 2:
					messages += ShoutBox.renderImageMessage(message);
					break;
			}
		});
		
		
		return ShoutBox.Templates['mysb_shout']
						.replace(new RegExp("{{pmMessage}}", 'g'), pmMessage)
						.replace(new RegExp("{{pmButton}}", 'g'), pmButton)
						.replace(new RegExp("{{datetime}}", 'g'), shout.dateTime.format("dddd, MMMM Do YYYY, h:mm:ss a"))
						.replace(new RegExp("{{avatarUrl}}", 'g'), shout.avatarUrl)
						.replace(new RegExp("{{formattedName}}", 'g'), shout.formattedUsername)
						.replace(new RegExp("{{messages}}", 'g'), messages);
	},

	renderTextMessage: function(message){
		return ShoutBox.Templates['mysb_shout_message_text']
						.replace(new RegExp("{{dateTime}}", 'g'), message.dateTime.format("dddd, MMMM Do YYYY, h:mm:ss a"))
						.replace(new RegExp("{{message}}", 'g'), message.content);
	},

	renderImageMessage: function(message){
		return ShoutBox.Templates['mysb_shout_message_image']
						.replace(new RegExp("{{dateTime}}", 'g'), message.dateTime.format("dddd, MMMM Do YYYY, h:mm:ss a"))
						.replace(new RegExp("{{image_src}}", 'g'), message.content);
	},

	renderPmButton: function(userId) {
		return ShoutBox.Templates['mysb_shout_button_pm'].replace(new RegExp("{{uid}}", 'g'), userId);
	},

	renderReverseOrderButton: function(){
		if(ShoutBox.orderShoutboxDesc){
			$("#shout-reverse-button").html("<i class=\"fa fa-arrow-down\"></i>");
			$("#shout-reverse-button").attr("title", ShoutBox.getLanguageValue("mysb_reverse_shout_order_to_asc"));
		}
		else {
			$("#shout-reverse-button").html("<i class=\"fa fa-arrow-up\"></i>");
			$("#shout-reverse-button").attr("title", ShoutBox.getLanguageValue("mysb_reverse_shout_order_to_desc"));
		}
	},
	
	shouldMergeShouts: function(previousShoutViewModel, shout) {
		if(shout.userId != previousShoutViewModel.userId)
			return false;
		
		if(shout.pmTargetUserId != previousShoutViewModel.pmTargetUserId)
			return false;
		
		var timeDifferenceInHours = moment.unix(shout.dateTime).diff(previousShoutViewModel.dateTime, 'hours')
		
		if(timeDifferenceInHours > 1)
			return false;
		
		return true;
	},
	
	buildShoutViewModel: function(shout, shoutMessageViewModel){
		return {
			userId: shout.userId,
			avatarUrl: shout.avatarUrl,
			dateTime: shoutMessageViewModel.dateTime,
			formattedUsername: shout.formattedUsername,
			isPm: shout.isPm,
			pmTargetUserId: shout.pmTargetUserId,
			pmTargetUsername: shout.pmTargetUsername,
			messages: new Array(shoutMessageViewModel)
		};
	},
	
	buildShoutMessageViewModel: function(shout){
		return {
			id: shout.id,
			type: parseInt(shout.type),
			content: shout.message,
			ip: shout.userIp,
			dateTime: moment.unix(shout.dateTime)
		};
	},

	selectMessageType: function(type) {
		ShoutBox.SelectedMessageType = type;
	},
	
	emptyMessageBox: function(){
		$("#shout_data").val("");
	},
	
	indicateShoutPostingFinished: function(){
		$("#shouting-status").html(ShoutBox.lang[1]);
		$("#shout_data").removeAttr("disabled");
		$("#shouting-status").removeAttr("disabled");
		ShoutBox.resizeMessageBoxToFitContents();
		ShoutBox.shouting = false;
		ShoutBox.getShouts();
	}
};
