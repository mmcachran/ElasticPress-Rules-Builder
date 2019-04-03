import 'selectize';

(function ($) { // eslint-disable-line
	const initSelectizer = elements => {
		const n = elements.length;
		let i = 0;
		let element;
		let $element;
		const opts = {};
		let data;
		for (i = 0; i < n; i++) {
			element = elements[i];
			$element = $(element);
			data = $element.data();

			if (data) {
				if (data.multiple) {
					opts.maxItems = null;
					opts.plugins = ['drag_drop', 'remove_button'];
				} else {
					opts.maxItems = 1;
					opts.plugins = ['remove_button'];
				}
			} else {
				opts.maxItems = 1;
				opts.plugins = ['remove_button'];
			}

			$(element).selectize(opts);
		}
	};

	$(document).on('ready', () => {
		const selects = $('[data-selectizer=1]');
		initSelectizer(selects);
	});
})(jQuery);
