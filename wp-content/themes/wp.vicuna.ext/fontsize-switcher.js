/*
*	※Licence: CC3.0 
*	http://creativecommons.org/licenses/by/3.0/deed.ja
*
*	Title: Fontsize Switcher v1.0
*	URI: http://10coin.com/products/fontsize-switcher/
*	Last Modified: 2008-01-06
*	Author: marble
*/

var fontSizeSwitcher = {
	config: {
		area:        ['div#main'],
		id:          ['fontSizeSwitcherSmall', 'fontSizeSwitcherMedium', 'fontSizeSwitcherLarge'],
		label:       ['S', 'M', 'L'],
		size:        ['85%', '100%', '122%'],
		description: 'Font Size:',
		cookieName:  'FontSizeSwitcher',
		cookieDate:  90
	},

	changeFontSize: function(size) {
		var config = this.config;
		var items  = document.getElementById('fontSizeSwitcherList').childNodes;
		var ss     = document.styleSheets[0];

		for (var i = 0, l = items.length; i < l ; i++) {
			if (size == i) {
				this.setClassName(items[i], 'current');
			}
			else {
				this.removeClassName(items[i]);
			}
		}


		for (var i = 0, l = config.area.length; i < l; i++) {
			if (window.attachEvent && !window.opera) {
				ss.addRule(config.area[i], 'font-size: ' + config.size[size]  + ';');
			}
			else {
				ss.insertRule(config.area[i] + '{ font-size: ' + config.size[size]  + '; }', ss.cssRules.length);
			}
		}

		this.setCookie(size);
	},

	setCookie: function(data) {
		var t = new Date();
		t.setTime(t.getTime() + (1000 * 60 * 60 * 24 * Number(this.config.cookieDate)));
		document.cookie = this.config.cookieName + '=' + encodeURIComponent(data) + '; path=/; expires=' + t.toGMTString();
	},

	getCookie: function(m) {
		return (m = ('; ' + document.cookie + ';').match('; ' + this.config.cookieName + '=(.*?);')) ? decodeURIComponent(m[1]) : null;
	},

	setClassName: function(elem, str) {
		(window.attachEvent && !window.opera) ? elem.className = str : elem.setAttribute('class', str);
	},

	removeClassName: function(elem) {
		(window.attachEvent && !window.opera) ? elem.removeAttribute('className') : elem.removeAttribute('class');
	},

	start: function() {
		var config = this.config;
		var size   = this.getCookie('s');
		var str    = '';

		for (var i = 0, l = config.id.length; i < l ; i++) {
			str += '<li id="' + config.id[i] + '" onclick="fontSizeSwitcher.changeFontSize(' + i + ')">' + config.label[i] + '</li>';
		}

		document.write('<dl id="fontSizeSwitcher"><dt>' + config.description + '</dt><dd><ul id="fontSizeSwitcherList">' + str + '</ul></dd></dl>');

		if (size == null) {
			size = 1;
		}

		this.changeFontSize(size);
	}
}

fontSizeSwitcher.start();