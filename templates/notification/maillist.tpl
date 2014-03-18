{**
 * templates/notification/maillist.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Displays the notification settings page and unchecks
 *
 *}
{strip}
{assign var="pageTitle" value="notification.mailList"}
{include file="common/header.tpl"}
{/strip}

<p><span class="instruct">{translate key="notification.mailListDescription"}</span></p>

{if $isError}
<p>
	<span class="formError">{translate key="form.errorsOccurred"}:</span>
	<ul class="formErrorList">
	{foreach key=field item=message from=$errors}
			<li>{$message}</li>
	{/foreach}
	</ul>
</p>
{/if}

{if $success}
	<p><span class="formSuccess">{translate key="$success"}</span></p>
{/if}
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#notificationSettings').pkpHandler('$.pkp.controllers.form.FormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="notificationSettings" method="post" action="{url op="saveSubscribeMailList"}">

<table class="data">
	<tr>
		<td class="label" width="5%">{fieldLabel name="email" key="user.email"}</td>
		<td class="value" width="45%"><input type="text" id="email" name="email" size="30" maxlength="90" class="textField" /></td>
	</tr>
	<tr>
		<td class="label" width="5%">{fieldLabel name="confirmEmail" key="user.confirmEmail"}</td>
		<td class="value" width="45%"><input type="text" id="confirmEmail" name="confirmEmail" size="30" maxlength="90" class="textField" /></td>
	</tr>
	{if $captchaEnabled}
		<tr>
			<td class="label" valign="top">{fieldLabel name="recaptcha_challenge_field" required="true" key="common.captchaField"}</td>
			<td class="value">
				{$reCaptchaHtml}
			</td>
		</tr>
	{/if}{* $captchaEnabled *}
	<tr>
		<td width="5%">&nbsp;</td>
		<td><p><input type="submit" value="{translate key="form.submit"}" class="button defaultButton" /></p></td>
	</tr>
</table>
</form>
<h5 style="margin-left:10%">{translate key="notification.mailList.register"}</h5>
<ul class="plain" style="margin-left:10%">
	{if $settings.allowRegReviewer}
		{url assign="url" page="user" op="register"}
		<li>&#187; {translate key="notification.mailList.review" reviewUrl=$url} </li>
	{/if}
	{if $settings.allowRegAuthor}
		{url assign="url" page="information" op="authors"}
		<li>&#187; {translate key="notification.mailList.submit" submitUrl=$url} </li>
	{/if}
	{if $settings.subscriptionsEnabled}
		{url assign="url" page="user" op="register"}
		<li>&#187; {translate key="notification.mailList.protectedContent" subscribeUrl=$url}
	{/if}
<li>&#187; <a href="{url page="about" op="submissions" anchor="privacyStatement"}">{translate key="about.privacyStatement"}</a></li>
</ul>

</form>

{include file="common/footer.tpl"}

