/**
 * RequireJS module for Trigger widget
 */
define(['jquery'], function() {
	var el;

	return {
		options: {
			onValue: 'on',
			offValue: 'off',
			onTitle: 'ON',
			offTitle: 'OFF'
		},

		isOn: false,
		onCallback: null,
		offCallback: null,

		init: function(selector, options, onCallback, offCallback) {
			var trigger = this;
			el = $(selector);

			this.onCallback = onCallback || null;
			this.offCallback = offCallback || null;

			for (var o in options) {
				if (this.options[o]) {
					this.options[o] = options[o];
				}
			}

			if(options.isOn) this.setState(true);
			else this.setState(false)

			el.click(function(e) {
				trigger.toggle();
			});
		},

		setState: function (isOn) {
			isOn = isOn || false;

			if(isOn) {
				this.isOn = true;
				el.addClass('on');
				el.text(this.options.onTitle);
				el.attr('data-value', this.options.onValue);

				var title = this.options.offTitle;
				setTimeout(function() {
					el.next('.offTitle').text(title);
					el.next('.offTitle').addClass('on');
				}, 300);
			} else {
				this.isOn = false;
				el.removeClass('on');
				el.text(this.options.offTitle);
				el.attr('data-value', this.options.offValue);


				var title = this.options.onTitle;
				setTimeout(function() {
					el.next('.offTitle').text(title);
					el.next('.offTitle').removeClass('on');
				}, 300);
			}
		},

		on: function () {
			this.setState(true);

			if($.isFunction(this.onCallback))
				this.onCallback(this.options.onValue, el);
		},

		off: function () {
			this.setState(false);

			if($.isFunction(this.offCallback))
				this.offCallback(this.options.offValue, el);
		},

		toggle: function () {
			if(this.isOn) this.off();
			else this.on();
		}
	}
});
