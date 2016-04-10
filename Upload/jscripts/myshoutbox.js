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
var ShoutBox = {
	
	refreshInterval: 60,
	lastID: 0,
	totalEntries: 0,
	firstRun: true,
	MaxEntries: 5,
	DataStore: new Array(),
	shouting: false,
	orderShoutboxDesc: false,
	lang: ['Shouting...', 'Shout Now!', 'Loading...', 'Flood check! Please try again in <interval> seconds.', 'Couldn\'t shout or perform action. Please try again!', 'Sending message...', 'Send!'],
	newLang: {},
	
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
	

	showShouts: function() {
		setTimeout("ShoutBox.showShouts();", ShoutBox.refreshInterval * 1000);
		/*
		if (typeof Ajax == 'object') {
			new Ajax.Request('xmlhttp.php?action=show_shouts&last_id='+ShoutBox.lastID, {method: 'get', onComplete: function(request) { ShoutBox.shoutsLoaded(request); } });
		}
		*/		
		$.get("xmlhttp.php?action=show_shouts&last_id="+ShoutBox.lastID, function(data){
			ShoutBox.shoutsLoaded(data);
		});
	},

	shoutsLoaded: function(responseData) {
		
		var theHTML = "";
		var curData = "";
		var data = responseData.split('^--^');
		var lastID = parseInt(data[0]);
		var theEntries = parseInt(data[1]);
		var lastEntryIsEmpty = false;

		if (lastID <= ShoutBox.lastID) {
			return;
		}

		// add to data store now...
		curData = data[2].split("\r\n");
		
		
		if(curData[curData.length - 1] === ""){
			lastEntryIsEmpty = true;
		}

		var numberOfShouts = lastEntryIsEmpty ? curData.length - 1 : curData.length;
		// only 1 message?
		if (numberOfShouts == 1) 
		{
			length = ShoutBox.DataStore.length;
			ShoutBox.DataStore[ length ] = curData[0];
		} 
		else 
		{
			// hush, lots of em
			var collectData = "";
			var length = 0;
			for (var i = numberOfShouts; i >= 0; i--) 
			{
				if (curData[i] != "" && curData[i] != undefined) {
					length = ShoutBox.DataStore.length;
					ShoutBox.DataStore[ length ] = curData[i];
				}	
			}
		}

		ShoutBox.lastID = lastID;
		ShoutBox.totalEntries += theEntries;

		var shouldScrollToBottom = ShoutBox.firstRun && !ShoutBox.orderShoutboxDesc;
		if (ShoutBox.firstRun) {
			ShoutBox.renderStructure();
		}
		
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

		postData = "shout_data="+encodeURIComponent(message).replace(/\+/g, "%2B");
		//new Ajax.Request('xmlhttp.php?action=add_shout', {method: 'post', postBody: postData, onComplete: function(request) { ShoutBox.postedShout(request, message); }});
		$.post("xmlhttp.php?action=add_shout", postData)
			.done(function(data){
				ShoutBox.postedShout(data, message);
			});
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
		ShoutBox.showShouts();
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
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
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
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
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
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
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
	
	alert: function(msg) {
		$("#shoutbox-alert").css("display","table-row");
		$("#shoutbox-alert-contents").html(msg);
		setTimeout(function(){$("#shoutbox-alert").css("display","none");}, 5000);
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
		var output = "";
		
		for (var i = 0; i < ShoutBox.DataStore.length; i++) {
			if(ShoutBox.orderShoutboxDesc){
				output = ShoutBox.DataStore[i] + output;
			}
			else {
				output = output + ShoutBox.DataStore[i];
			}
		}
		
		$("#shoutbox_data").html(output);
		
		if(ShoutBox.orderShoutboxDesc){
			ShoutBox.scrollToTopOfMessages();
		}
		else {
			ShoutBox.scrollToBottomOfMessages();
		}
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
	}
};
