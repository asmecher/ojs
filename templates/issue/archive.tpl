{**
 * templates/issue/archive.tpl
 *
 * Copyright (c) 2014-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Present a list of published issues in the journal's archive.
 *
 * Available data:
 *  $issues: ItemIterator of issues in the journal's archive.
 *}
{include file="common/header.tpl" pageTitle="archive.archives"}

<div id="issues">
	{assign var=lastYear value=null}
	{iterate from=issues item=issue}
		{if $issue->getYear() != $lastYear}
			{if !isset($notFirstYear)}
				{assign var=notFirstYear value=1}
			{else}
				</div>
				<br />
				<div class="separator" style="clear:left;"></div>
			{/if}
			<div style="float: left; width: 100%;">
			<h3>{$issue->getYear()|escape}</h3>
			{assign var=lastYear value=$issue->getYear()}
		{/if}

		<div id="issue" style="clear:left;">
			{if $issue->getLocalizedFileName() && $issue->getShowCoverPage($currentLocale) && !$issue->getHideCoverPageArchives($currentLocale)}
				<div class="issueCoverImage"><a href="{url op="view" path=$issue->getBestIssueId($currentJournal)}"><img src="{$coverPagePath|escape}{$issue->getFileName($currentLocale)|escape}"{if $issue->getCoverPageAltText($currentLocale) != ''} alt="{$issue->getCoverPageAltText($currentLocale)|escape}"{else} alt="{translate key="issue.coverPage.altText"}"{/if}/></a>
				</div>
				<h4><a href="{url op="view" path=$issue->getBestIssueId($currentJournal)}">{$issue->getIssueIdentification()|escape}</a></h4>
				<div class="issueCoverDescription">{$issue->getLocalizedCoverPageDescription()|strip_unsafe_html|nl2br}</div>
			{else}
				<h4><a href="{url op="view" path=$issue->getBestIssueId($currentJournal)}">{$issue->getIssueIdentification()|escape}</a></h4>
				<div class="issueDescription">{$issue->getLocalizedDescription()|strip_unsafe_html|nl2br}</div>
			{/if}
		</div>
	{/iterate}
	{if $notFirstYear}<br /></div>{/if}

	{if !$issues->wasEmpty()}
		{page_info iterator=$issues}&nbsp;&nbsp;&nbsp;&nbsp;
		{page_links anchor="issues" name="issues" iterator=$issues}
	{else}
		{translate key="current.noCurrentIssueDesc"}
	{/if}
</div>

{include file="common/footer.tpl"}
