<?php

/**
 * @file tests/data/70-sanity/FilePermissionsTest.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CreateJournalTest
 * @ingroup tests_data
 *
 * @brief Data build suite: Check file download permissions
 */

import('tests.ContentBaseTestCase');

class FilePermissionsTest extends ContentBaseTestCase {
	/**
	 * Prepare for tests.
	 */
	function testSectionEditorAccess() {
		$title = 'Hansen & Pinto: Reason Reclaimed';

		$this->findSubmissionAsEditor('dbarnes', null, $title);
		$this->waitForElementPresent($s='//li[contains(@class,\'pkp_workflow_externalReview\')]/a'); // Review stage
		$this->click($s);

		// Get the review files grid file download URL.
		$this->waitForElementPresent($s='//div[contains(@id,\'editorreviewfilesgrid\')]//a[contains(@id,\'downloadFile\')]');
		$reviewFileDownloadUrl = $this->getAttribute($s . '@href');
		parent::logOut();

		// Log in as dbuskins and check that he can't download the file.
		parent::logIn('dbuskins');
		$this->open($reviewFileDownloadUrl);
		$this->assertTextPresent('You don\'t currently have access to that stage of the workflow.');
		parent::logOut();
	}
}
