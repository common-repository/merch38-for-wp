(function($) {
	function kebabToCamel (str) {
    return str.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
  }

	$(document).ready(function () {
    $('.m38-wp-widget').each(function () {
      var dataArgs = $(this).attr('data-args');
      var elId = $(this).attr('id');
      var args = JSON.parse(atob(dataArgs));
      var contentArgs = Object
        .entries(args)
        .filter(function (entry) { return entry[0] !== 'id' })
        .reduce(function (acc, entry) {
          acc[kebabToCamel(entry[0])] = entry[1]
          return acc
        }, {})

      var el = '#' + elId;

			window.merch38Widget(el, {
				campaignId: args.id,
				content: contentArgs,
				// appearance: {
				// 	mainColor: '#2845d4',
				// 	btnText: 'BUY',
				// 	btnTextColor: '#ffffff'
				// }
			})
    })
	});

})(jQuery);