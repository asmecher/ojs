/**
 * @file cypress/tests/docs/learning-ojs/Chapter06.spec.js
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 */

describe('Documentation suite tests', function() {
	before(() => {
		// Set up locale fixtures: locale, localeConfig, primaryLocaleConfig
		cy.wrap(Cypress.env('LOCALE')).as('locale').then(function() {
			cy.fixture(this.locale).as('localeConfig');
			cy.fixture('en_US').as('primaryLocaleConfig');
		});
		// Change OJS to the desired locale
		cy.setLocale(Cypress.env('LOCALE'));
	});

	it('Generates screenshots for Chapter 6', function() {
		cy.login('dbarnes', null, 'publicknowledge');

		// Go to the "Navigation Menu" area of setup
		cy.get('.app__nav a[href$="settings/website"]').click(); // Website sidebar link
		cy.get('button[id="setup-button"]:first').click(); // FIXME: There are two "Setup" buttons
		cy.get('button[id="navigationMenus-button"]').click();
		cy.scrollTo('topLeft');
		cy.screenshot('learning-ojs3.1-jm-settings-web-navmenu-' + this.locale, {capture: 'viewport'});

		// Create a new navigation menu item with an external URL
		cy.get('a[id^="component-grid-navigationmenus-navigationmenuitemsgrid-addNavigationMenuItem-button-"]').click();
		cy.wait(500); // Wait for form init
		cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="title-' + this.locale + '-"]').type(this.localeConfig.chapter6.navigationMenuName, {delay: 0});
		if (this.locale != 'en_US') {
			cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="title-en_US-"]').type(this.primaryLocaleConfig.chapter6.navigationMenuName, {delay: 0});
		}
		cy.get('fieldset[id="navigationMenuItemInfo"] select#menuItemType').select('NMI_TYPE_REMOTE_URL');
		cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="remoteUrl-' + this.locale + '-"]').type(this.localeConfig.chapter6.navigationMenuUrl, {delay: 0});
		if (this.locale != 'en_US') {
			cy.get('fieldset[id="navigationMenuItemInfo"] input[id^="remoteUrl-en_US-"]').type(this.primaryLocaleConfig.chapter6.navigationMenuUrl, {delay: 0});
		}
		cy.get('label[for^="remoteUrl-localization"]').click(); // FIXME: Close multilingual popover
		cy.get('div.pkp_modal_panel').screenshot('learning-ojs3.1-jm-settings-web-navmenu-add-' + this.locale);
		cy.get('div.pkp_modal_panel button[id="saveButton"]').click();

		// FIXME: Pesky PNotify wants to photobomb next screenshot
		cy.visit('index.php/publicknowledge/management/settings/website');
		cy.get('.app__nav a[href$="settings/website"]').click(); // Website sidebar link
		cy.get('button[id="setup-button"]:first').click(); // FIXME: There are two "Setup" buttons
		cy.get('button[id="navigationMenus-button"]').click();
		cy.scrollTo('topLeft');

		// Move the newly created navigation menu item to the top of the list and screenshot.
		// This is tricky -- drag and drop with real estate issues is finicky.
		cy.get('a:contains("Primary Navigation Menu")').click();
		cy.wait(500); // Form initialization
		cy.get('ul#pkpNavUnassigned').scrollIntoView();
		cy.get('ul#pkpNavAssigned li:contains("' + this.localeConfig.chapter6.currentNavigationMenuName + '")').then($targetE => {
			var targetRect = Cypress.$($targetE)[0].getBoundingClientRect();
			cy.get('ul#pkpNavUnassigned li:contains("' + this.localeConfig.chapter6.navigationMenuName + '")').then($itemE => {
				var itemRect = Cypress.$($itemE)[0].getBoundingClientRect();
				cy.get('ul#pkpNavUnassigned li:contains("' + this.localeConfig.chapter6.navigationMenuName + '")').trigger('mousedown', {which: 1, pageX: itemRect.x + (itemRect.width / 2), pageY: itemRect.y + (itemRect.height / 2)});
				cy.wait(250);
				cy.get('ul#pkpNavUnassigned li:contains("' + this.localeConfig.chapter6.navigationMenuName + '")').trigger('mousemove', {which: 1, pageX: targetRect.x + (targetRect.width / 2), pageY: targetRect.y});
				cy.wait(250);
				cy.get('li div.item:contains("' + this.localeConfig.chapter6.navigationMenuName + '")').trigger('mouseup');
				cy.wait(250);
				cy.get('div.pkp_modal').scrollTo('topLeft');
				cy.get('div.pkp_modal_panel').screenshot('learning-ojs3.1-jm-settings-web-navmenu-add-nav-' + this.locale, {clip: {x:0, y:0, width:800, height:650}});
			});
		});
	})
})
