/**
 * @file cypress/tests/docs/learning-ojs/Chapter06.spec.js
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 */

describe('Documentation suite tests', function() {
	it('Generates screenshots for Chapter 6', function() {
		cy.login('dbarnes', null, 'publicknowledge');
		cy.get('a').contains('Settings').click();
		cy.get('a').contains('Website').click();
		cy.get('div[id^="settings-context"] > div.pkpTabs > div[role=tablist] > button#setup-button').click();
		cy.get('button#navigationMenus-button').click();
		cy.scrollTo('topLeft');
		cy.screenshot('learning-ojs3.1-jm-settings-web-navmenu', {capture: 'viewport'});

		cy.get('a[id^="component-grid-navigationmenus-navigationmenuitemsgrid-addNavigationMenuItem-button-"]').click();
		cy.wait(500); // Wait for form init
		cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="title-en_US-"]').type('Our Society', {delay: 0});
		cy.get('fieldset[id="navigationMenuItemInfo"] select#menuItemType').select('Remote URL');
		cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="url-"]').type('http://www.oursociety.com', {delay: 0});
		cy.get('div.pkp_modal_panel').screenshot('learning-ojs3.1-jm-settings-web-navmenu-add');
		cy.get('div.pkp_modal_panel button:contains("Save")').click();

		// FIXME: Pesky PNotify wants to photobomb next screenshot
		cy.visit('index.php/publicknowledge/management/settings/website');
		cy.get('div[id^="settings-context"] > div.pkpTabs > div[role=tablist] > button#setup-button').click();
		cy.get('button#navigationMenus-button').click();
		cy.scrollTo('topLeft');

		// This is tricky -- drag and drop with real estate issues is finicky.
		cy.get('a:contains("Primary Navigation Menu")').click();
		cy.wait(500); // Form initialization
		cy.get('ul#pkpNavUnassigned').scrollIntoView();
		cy.get('ul#pkpNavAssigned li:contains("Current")').then($targetE => {
			var targetRect = Cypress.$($targetE)[0].getBoundingClientRect();
			cy.get('ul#pkpNavUnassigned li:contains("Our Society")').then($itemE => {
				var itemRect = Cypress.$($itemE)[0].getBoundingClientRect();
				cy.get('ul#pkpNavUnassigned li:contains("Our Society")').trigger('mousedown', {which: 1, pageX: itemRect.x + (itemRect.width / 2), pageY: itemRect.y + (itemRect.height / 2)});
				cy.wait(250);
				cy.get('ul#pkpNavUnassigned li:contains("Our Society")').trigger('mousemove', {which: 1, pageX: targetRect.x + (targetRect.width / 2), pageY: targetRect.y});
				cy.wait(250);
				cy.get('li div.item:contains("Our Society")').trigger('mouseup');
				cy.wait(250);
				cy.get('div.pkp_modal').scrollTo('topLeft');
				cy.get('div.pkp_modal_panel').screenshot('learning-ojs3.1-jm-settings-web-navmenu-add-nav', {clip: {x:0, y:0, width:800, height:650}});
			});
		});
	})
})
