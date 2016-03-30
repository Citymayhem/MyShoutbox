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
	firstRun: 1,
	MaxEntries: 5,
	DataStore: new Array(),
	lang: ['Shouting...', 'Shout Now!', 'Loading...', 'Flood check! Please try again in <interval> seconds.', 'Couldn\'t shout or perform action. Please try again!', 'Sending message...', 'Send!'],


	showShouts: function() {
		setTimeout("ShoutBox.showShouts();", ShoutBox.refreshInterval * 1000);
		if (typeof Ajax == 'object') {
			new Ajax.Request('xmlhttp.php?action=show_shouts&last_id='+ShoutBox.lastID, {method: 'get', onComplete: function(request) { ShoutBox.shoutsLoaded(request); } });
		}
	},

	shoutsLoaded: function(request) {
		
		var theHTML = "";
		var curData = "";
		var data = request.responseText.split('^--^');
		var theID = parseInt(data[0]);
		var theEntries = parseInt(data[1]);

		if (theID <= ShoutBox.lastID) {
			return;
		}

		// add to data store now...
		curData = data[2].split("\r\n");

		// only 1 message?
		if (curData.length == 1) 
		{
			length = ShoutBox.DataStore.length;
			ShoutBox.DataStore[ length ] = curData[0];
		} 
		else 
		{
			// hush, lots of em
			var collectData = "";
			var length = 0;
			for (var i = curData.length; i >= 0; i--) 
			{
				if (curData[i] != "" && curData[i] != undefined) {
					length = ShoutBox.DataStore.length;
					ShoutBox.DataStore[ length ] = curData[i];
				}	
			}
		}

		ShoutBox.lastID = theID;
		ShoutBox.totalEntries += theEntries;

		if (ShoutBox.firstRun == 1) {
			theHTML = data[2];
			ShoutBox.firstRun = 0;
		} else {

			// the data is more than the limit? hard luck here then... just get it from datastore
			if ((theEntries + ShoutBox.totalEntries) > ShoutBox.MaxEntries) {
				for (var j=0, i = ShoutBox.DataStore.length-1; j < ShoutBox.MaxEntries; i--, j++) {
					theHTML += ShoutBox.DataStore[i];
				}

				ShoutBox.totalEntries = ShoutBox.MaxEntries;

			} else {
				theHTML = data[2] + $("shoutbox_data").innerHTML;
			}

		}

		$("shoutbox_data").innerHTML = theHTML;

		// clean up DataStore
		ShoutBox.cleanDataStore();
	},
	
	pvtAdd: function(uid) {
		var msg = $("shout_data").value;
		$("shout_data").value = '/pvt ' + uid + ' ' + msg;
	},

	postShout: function() {
		message = $("shout_data").value;
		if (message == "") {
			return false;
		}

		$("shouting-status").value = ShoutBox.lang[0];

		postData = "shout_data="+encodeURIComponent(message).replace(/\+/g, "%2B");
		new Ajax.Request('xmlhttp.php?action=add_shout', {method: 'post', postBody: postData, onComplete: function(request) { ShoutBox.postedShout(request); }});
	},

	postedShout: function(request) {
		
		if (request.responseText == 'deleted') {
			ShoutBox.firstRun = 1;
			ShoutBox.lastID = 0;
			alert("Shouts deleted as requested.");
		}
		else if (request.responseText.indexOf('flood') != -1) {
			var split = new Array();
			split = request.responseText.split('|');			
			var interval = split[1]; 
			
			alert(ShoutBox.lang[3].replace('<interval>', interval));
		}
		else if (request.responseText.indexOf("success") == -1) {
			alert(ShoutBox.lang[4]);
		}

		$("shouting-status").value = ShoutBox.lang[1];
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
		new Ajax.Request('xmlhttp.php?action=report_shout', {method: 'post', postBody: postData, onComplete: function(request) { ShoutBox.shoutReported(request); }});
	},

	shoutReported: function(request) {
		if (request.responseText == 'invalid_shout') {
			alert(ShoutBox.lang[9]);
			return false;
		}
		else if (request.responseText == 'already_reported') {
			alert(ShoutBox.lang[11]);
			return false;
		}
		else if (request.responseText == 'shout_reported') {
			alert(ShoutBox.lang[10]);
		}
		
		ShoutBox.showShouts();
	},
	
	// prompt reason
	promptReason: function(id) {
		
		var reason = prompt("Enter a reason:", "");
		
		if (reason == "" || reason == null || id == "") {
			return false;
		}
		
		id = parseInt(id);
		
		return ShoutBox.reportShout(reason, id);
	},
	
	deleteShout: function(id, type, message) {
		
		message = message.escapeHTML(); // escape HTML before outputting the message
		
		var confirmation = confirm(message);
		
		if (!confirmation)
			return false;
		
		if (type == 1) {
			$("shoutbox_data").innerHTML = ShoutBox.lang[2];
		}
		
		id = parseInt(id);

		new Ajax.Request('xmlhttp.php?action=delete_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.deletedShout(request, id, type); } });
	},
	
	deletedShout: function(request, id, type) {
		if (request.responseText.indexOf("success") == -1) {
			alert("Error deleting shout... Try again!");
		} else if (type == 2) {
			alert("Shout deleted.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
		} else {
			try {
				$("shout-"+id).style.display = "none";
			} catch (e) { 
				$("shout-"+id).style.display = "hidden";
			}
		}

	},
	
	removeShout: function(id, type, message) {
		
		message = message.escapeHTML(); // escape HTML before outputting the message
		
		var confirmation = confirm(message);
		
		if (!confirmation)
			return false;
			
		if (type == 1) {
			$("shoutbox_data").innerHTML = ShoutBox.lang[2];
		}
		
		id = parseInt(id);

		new Ajax.Request('xmlhttp.php?action=remove_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.deletedShout(request, id, type); } });
	},
	
	removedShout: function(request, id, type) {
		if (request.responseText.indexOf("success") == -1) {
			alert("Error removing shout... Try again!");
		} else if (type == 2) {
			alert("Shout removed.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
		} else {
			try {
				$("shout-"+id).style.display = "none";
			} catch (e) { 
				$("shout-"+id).style.display = "hidden";
			}
		}

	},
	
	recoverShout: function(id, type, message) {
		
		message = message.escapeHTML(); // escape HTML before outputting the message
		
		var confirmation = confirm(message);
		
		if (!confirmation)
			return false;

		if (type == 1) {
			$("shoutbox_data").innerHTML = ShoutBox.lang[2];
		}

		id = parseInt(id);
		
		new Ajax.Request('xmlhttp.php?action=recover_shout&id='+id, {method: 'get', onComplete: function(request) { ShoutBox.recoveredShout(request, id, type); } });
	},
	
	recoveredShout: function(request, id, type) {
		if (request.responseText.indexOf("success") == -1) {
			alert("Error recovering shout... Try again!");
		} else if (type == 2) {
			alert("Shout recovered.");
		}
		
		id = parseInt(id);

		if (type == 1) {
			ShoutBox.DataStore = new Array();
			ShoutBox.lastID = 0;
			ShoutBox.showShouts();
		} else {
			try {
				$("shout-"+id).style.display = "none";
			} catch (e) { 
				$("shout-"+id).style.display = "hidden";
			}
		}

	},

	cleanDataStore: function() {
		if (ShoutBox.DataStore.length > ShoutBox.MaxEntries) {
			for (var i = (ShoutBox.DataStore.length - ShoutBox.MaxEntries); i > 0; i--) {
				ShoutBox.DataStore[i] = "";
			}
		}
	},

	disableShout: function() {
		try { 
			$("shouting-status").disabled = true;
		} catch (e) {
			$("shouting-status").readonly = true;
		}
	}
};