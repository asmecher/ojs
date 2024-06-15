<?php

/**
 * @file tests/jobs/statistics/CompileIssueMetricsTest.php
 *
 * Copyright (c) 2024 Simon Fraser University
 * Copyright (c) 2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Tests for compile issue metrics job.
 */

namespace APP\tests\jobs\statistics;

use APP\jobs\statistics\CompileIssueMetrics;
use Mockery;
use PKP\db\DAORegistry;
use PKP\tests\PKPTestCase;

/**
 * @runTestsInSeparateProcesses
 *
 * @see https://docs.phpunit.de/en/9.6/annotations.html#runtestsinseparateprocesses
 */
class CompileIssueMetricsTest extends PKPTestCase
{
    /**
     * base64_encoded serializion from OJS 3.4.0
     */
    protected string $serializedJobData = 'TzozOToiQVBQXGpvYnNcc3RhdGlzdGljc1xDb21waWxlSXNzdWVNZXRyaWNzIjozOntzOjk6IgAqAGxvYWRJZCI7czoyNToidXNhZ2VfZXZlbnRzXzIwMjQwMTMwLmxvZyI7czoxMDoiY29ubmVjdGlvbiI7czo4OiJkYXRhYmFzZSI7czo1OiJxdWV1ZSI7czo1OiJxdWV1ZSI7fQ==';

    /**
     * Test job is a proper instance
     */
    public function testUnserializationGetProperDepositIssueJobInstance(): void
    {
        $this->assertInstanceOf(
            CompileIssueMetrics::class,
            unserialize(base64_decode($this->serializedJobData))
        );
    }

    /**
     * Ensure that a serialized job can be unserialized and executed
     */
    public function testRunSerializedJob()
    {
        /** @var CompileIssueMetrics $compileIssueMetricsJob */
        $compileIssueMetricsJob = unserialize(base64_decode($this->serializedJobData));

        $temporaryTotalsDAOMock = Mockery::mock(\APP\statistics\TemporaryTotalsDAO::class)
            ->makePartial()
            ->shouldReceive([
                'compileIssueMetrics' => null,
            ])
            ->withAnyArgs()
            ->getMock();

        DAORegistry::registerDAO('TemporaryTotalsDAO', $temporaryTotalsDAOMock);

        $this->assertNull($compileIssueMetricsJob->handle());
    }
}
