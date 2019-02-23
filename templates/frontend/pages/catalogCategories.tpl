{**
 * templates/frontend/pages/catalogCategories.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Display the page to view a category of the catalog.
 *
 * @uses $categories ItemIterator List of categories to browse
 * @uses $prevPage int The previous page number
 * @uses $nextPage int The next page number
 * @uses $showingStart int The number of the first item on this page
 * @uses $showingEnd int The number of the last item on this page
 * @uses $total int Count of all published submissions in this category
 *}
{include file="frontend/components/header.tpl" pageTitle="category.categories"}

<div class="page page_catalog_categories">
	{* Breadcrumb *}
	{capture assign="categoriesTitle"}{translate key="navigation.categories"}{/capture}
	{include file="frontend/components/breadcrumbs_catalog.tpl" type="categories" currentTitle=$categoriesTitle}

	<a id="categories"></a>
	<ul>
	{iterate from=categories item=category}
		<li>
			<a class="categoryTitle" href="{url op="category" path=$category->getPath()}">
				{$category->getLocalizedTitle()|escape}
			</a>
			<div class="categoryDescription">{$category->getLocalizedDescription()}</div>
		</li>
	{/iterate}
	</ul>
	{if !$categories->wasEmpty()}
		<br />
		{page_info iterator=$categories}&nbsp;&nbsp;&nbsp;&nbsp;{page_links anchor="categories" iterator=$categories name="categories"}
	{else}
		<br />
		{translate key="search.noResults"}
	{/if}

</div><!-- .page -->

{include file="frontend/components/footer.tpl"}
