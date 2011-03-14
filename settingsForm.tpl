{**
 * settingsForm.tpl
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Statistics Aggregation plugin settings
 *
 * $Id$
 *}
{strip}
{assign var="pageTitle" value="plugins.generic.statisticsAggregation.manager.statisticsAggregationSettings"}
{include file="common/header.tpl"}
{/strip}
<div id="statisticsAggregationSettings">
<div id="description">{translate key="plugins.generic.statisticsAggregation.manager.settings.description"}</div>

<div class="separator"></div>

<br />

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

<script>
	<!--
		
		function getHashCode() {ldelim}
		
			if ($('#statisticsAggregationSiteEmail').attr('value') != '' && ($('#statisticsAggregationSiteEmail').attr('value') == $('#statisticsAggregationSiteEmailConfirm').attr('value'))) {ldelim}
				$.ajax ( 
					{ldelim}
						type: "POST",
						url: '{plugin_url path="getNewHash"}',
						data: "email=" + $('#statisticsAggregationSiteEmail').attr('value'),
						success: function (data) {ldelim} 
									var jsonObject = jQuery.parseJSON(data); 
									$('#statisticsAggregationSiteId').attr('value', jsonObject.hashCode);
									$('#statisticsAggregationFetchButton').attr('disabled', 'disabled');
								{rdelim} 
				 	{rdelim}
				 );
			{rdelim} else {ldelim}
				alert('{translate key="plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteEmailRequired"}');
			{rdelim} 
		{rdelim}
	// -->
</script>

<form method="post" action="{plugin_url path="settings"}">
{include file="common/formErrors.tpl"}

<table width="100%" class="data">
	<tr valign="top">
		<td width="30%" class="label">{fieldLabel name="statisticsAggregationSiteEmail" required="true" key="plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteEmail"}</td>
		<td width="70%" class="value"><input type="text" name="statisticsAggregationSiteEmail" id="statisticsAggregationSiteEmail" value="{$statisticsAggregationSiteEmail|escape}" size="32" maxlength="150" class="textField" />
	</td>
	</tr>
	<tr valign="top">
		<td width="30%" class="label">{fieldLabel name="statisticsAggregationSiteEmailConfirm" required="true" key="plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteEmailConfirm"}</td>
		<td width="70%" class="value"><input type="text" name="statisticsAggregationSiteEmailConfirm" id="statisticsAggregationSiteEmailConfirm" value="{$statisticsAggregationSiteEmailConfirm|escape}" size="32" maxlength="150" class="textField" />
	</td>
	</tr>
	<tr valign="top">
		<td width="30%" class="label">{fieldLabel name="statisticsAggregationSiteId" required="true" key="plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteId"}</td>
		<td width="70%" class="value"><input type="text" name="statisticsAggregationSiteId" id="statisticsAggregationSiteId" value="{$statisticsAggregationSiteId|escape}" size="32" maxlength="32" class="textField" />
		<input type="button" class="button" id="statisticsAggregationFetchButton" value="{translate key="plugins.generic.statisticsAggregation.manager.settings.getButtonLabel"}" onclick="getHashCode()"/>
	</td>
	</tr>
</table>

<br/>

<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}"/><input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
{include file="common/footer.tpl"}
