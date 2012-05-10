// Redefinition of <myfunction>.bind, very useful to keep the scope and <myelement>.addEventListener not defined in IE.
// Useless because browsers that don't define these methods will not manage canvas and so essential.
if(!Function.prototype.bind)
	Function.prototype.bind = function(binding) {
		return function() {
			this.apply(binding, arguments);
		};
	};
if(!HTMLElement.prototype.addEventListener)
	HTMLElement.prototype.addEventListener = function(eventType, listener) {
		this.attachEvent("on" + eventType, function(e) {
			e = e || window.event;
			listener(e);
		});
	};

// A little helper to create an HTML element fast with a parameters list and the classic $ for getElementById
// @params (type, params)
function c(t, o) {
	var d = document.createElement(t);
	o = o || {};
	for(var k in o)
	if(k === 'attributes')
		for(var a in o[k])
		d.setAttribute(a, o[k][a]);
	else
		d[k] = o[k];
	return d;
}

// @params (id)
function $(i) {
	return document.getElementById(i);
}

// Useful for the callbacks in order to do (callback || noop)() instead of a painful if(callback) { callback(); }
function noop() {
}

// Load a picture from its URL and call a function when it's loaded (with metadata)
// @params (url, callback)
function loadImg(u, cb) {
	var i = new Image();
	i.src = u;
	hide(i);
	i.onload = function(e) {
		cb(e);
		i.parentNode.removeChild(i);
	};
	return i;
}

// A round to n instead of 1, a little min/max to assert bounds of a value and a random [0,m]
// @params (value, round)
function r(v, s) {
	s = s || 1;
	return Math.ceil(v / s) * s;
}

// @params (min, value, max)
function mm(mi, v, ma) {
	if(ma>mi) return Math.max(mi, Math.min(v, ma));
	else return Math.max(ma, Math.min(v, mi));
}

// @params (max bound)
function rd(m) {
	return Math.round(Math.random() * m);
}

// Try to log an id for some times. If already locked, return false, else, lock and return true
// @params (lock id, lock time (in s))
var lock = (function() {
	var l = {};
	return function(i, t) {
		var n = new Date().getTime();
		if(l[i] && l[i] > n)
			return false;
		else
			l[i] = n + t * 1000;
		return true;
	};
})();

// File upload via iframe.
// @params (form, callback)
function fuajax(f, cb) {
	var d = 'fu' + rd(999);
	var e = c('iframe', {
		name : d
	});
	hide(e);
	f.setAttribute('target', d);
	e.onload = function() {
		cb(e.contentWindow.document.body.innerHTML);
		e.parentNode.removeChild(e);
	};
}

function ctx(id) {
	return $(id).getContext('2d');
}

window.URL_PATTERN = '^([^:/?#]+:)//[^/?#]+[^?#]*[^#]*(#.*)?';

Array.prototype.max = function() {
	var max = this[0];
	var len = this.length;

	for(var i = 1; i < len; i++)
	if(this[i] > max)
		max = this[i];

	return max;
};

Array.prototype.min = function() {
	var min = this[0];
	var len = this.length;

	for(var i = 1; i < len; i++)
	if(this[i] < min)
		min = this[i];

	return min;
};

function hide(element) {
	if(!$('hidden')) {
		var div = c('div', {
			attributes: {
				style: 'width: 0 !important; height: 0 !important; position: absolute !important; top: -10000px !important;',
				id: 'hidden'
			}
		});
		document.body.appendChild(div);
	}
	$('hidden').appendChild(element);
}