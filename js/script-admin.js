
(function($) {
	var MERCH_API_BASE = 'https://api.merch38.com/api/public'

	function camelToKebab (str) {
		return str.replace(/([a-z][A-Z])/g, function (g) { return g[0] + '-' + g[1].toLowerCase() });
	}

	function getApiKey () {
		return $('#m38ApiKeyOption').val();
	}

	function apiFormHandler (e) {
		e.preventDefault();
		$.post(ajaxurl,
			{
				data: { 'm38_option_api_key': $('#m38ApiKeyOption').val() },
				action: 'store_ajax_value'
			},
			function () {
				$('#m38SubmitMessage').show();
				displayCampaigns();
			}
		);
	}

	function fetchCampaigns (input, cb) {
		$.ajax({
			type: 'GET',
			beforeSend: function (request) {
				request.setRequestHeader('x-api-key', getApiKey());
			},
			url: MERCH_API_BASE + '/campaign',
			data: input ? input : {},
			success: function (data) {
				cb(null, data)
			},
			error: function (err) {
				cb(err)
			}
		});
	}

	function checkConnected () {
		if (getApiKey()) {
			$('#m38ApiStatus').html('CONNECTED');
			return true
		}
		$('#m38ApiStatus').html('NOT CONNECTED');
		return false
	}

	function renderCampaigns (items) {
		var htmlOutput = $.templates("#campaignRowTpl").render(items);
		$("#campaignRows").html(htmlOutput);
	}

	function displayCampaigns () {
		if (!checkConnected()) {
			return renderCampaigns([]);
		}
		fetchCampaigns({ _limit: 100, _sort: 'updatedAt:desc' }, function (err, data) {
			if (err) return renderCampaigns([]);
			data.items.map(function (item) {
				return Object.assign(item, {
					shortCode: getCampaignShortcode(item)
				})
			})
			renderCampaigns(data.items);
		})
	}

	function getCampaignShortcode (campaign) {
		function getTemplateArgsString (campaign) {
			return Object.entries(campaign.templateArgs)
				.map(function (entry) {
					return camelToKebab(entry[0]) + '="' + entry[1] + '" '
				})
				.join(' ')
				.trim()
		}

		return '[merch38 id="' + campaign._id + '" ' +  getTemplateArgsString(campaign) +  ']'
	}

	$(document).ready(function () {
		$('#m38ApiKeySettingsForm').on('submit', apiFormHandler);
		displayCampaigns();
	});

})(jQuery);