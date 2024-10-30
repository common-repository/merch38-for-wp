<?php
$m38_api_key = get_option( 'm38_option_api_key', '' );
?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-base-template"><br></div>
	<h1><?php _e( "Merch38 for WordPress", 'm38base' ); ?></h1>
	<h2><?php _e( 'API Settings', 'm38base' ); ?></h2>
	<form id="m38ApiKeySettingsForm" action="">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						Status </th>
					<td>
						<span id="m38ApiStatus" class="status">...</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="mailchimp_api_key">API Key</label></th>
					<td>
						<input type="text" class="widefat" placeholder="Your Merch38 API key" id="m38ApiKeyOption"
							name="m38_option_api_key" value="<?php echo $m38_api_key; ?>">
						<p class="help">
							The API key for connecting with your Merch38 account. <a target="_blank"
								href="https://app.merch38.com">Get your API key here.</a>
						</p>
					</td>
				</tr>

			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary"
				value="<?php _e( "Save", 'm38base' ); ?>">
			<span id="m38SubmitMessage" class="m38_submit_message"
				style="display: none;"><?php _e( "Value updated successfully", 'm38base' ); ?>
			</span>
		</p>
	</form>
	<hr>
	<h2><?php _e( "Your Campaigns", 'm38base' ); ?></h2>
	<p>Copy shortcode and paste to a post or a page</p>
	<table class="widefat fixed" cellspacing="0">
    <thead>
			<tr>
				<th id="" class="manage-column column-name" scope="col">Campaign Name</th>
				<th id="" class="manage-column column-shortcode" scope="col">Shortcode</th>
				<th id="" class="manage-column column-code" scope="col">Edit</th>
			</tr>
    </thead>
    <tbody id="campaignRows">
    </tbody>
	</table>
	<script id="campaignRowTpl" type="text/x-jsrender">
		<tr class="alternate">
			<td class="column-name">{{:name}}</td>
			<td class="column-shortcode"><code>{{:shortCode}}</code></td>
			<td class="column-code"><span><a target="_blank" href="https://app.merch38.com/campaigns/{{:_id}}">Edit Campaign...</a></span></td>
		</tr>
	</script>
</div>