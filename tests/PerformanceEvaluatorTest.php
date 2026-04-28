<?php

declare(strict_types=1);

namespace Motoadapt\Tests;

use Motoadapt\Model\PatientResponse;
use Motoadapt\Model\Trend;
use Motoadapt\Service\PerformanceEvaluator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Motoadapt\Service\PerformanceEvaluator::class)]
class PerformanceEvaluatorTest extends TestCase
{
    private PerformanceEvaluator $evaluator;

    protected function setUp(): void
    {
        $this->evaluator = new PerformanceEvaluator();
    }

    public function testComputesAveragePrecision(): void
    {
        $responses = [
            new PatientResponse(80.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(90.0, 1.5, new \DateTimeImmutable()),
            new PatientResponse(70.0, 2.5, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEqualsWithDelta(80.0, $report->avgPrecision, 0.01);
    }

    public function testComputesAverageResponseTime(): void
    {
        $responses = [
            new PatientResponse(80.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(90.0, 1.0, new \DateTimeImmutable()),
            new PatientResponse(70.0, 3.0, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEqualsWithDelta(2.0, $report->avgResponseTime, 0.01);
    }

    public function testDetectsProgressionTrend(): void
    {
        $responses = [
            new PatientResponse(50.0, 3.0, new \DateTimeImmutable()),
            new PatientResponse(70.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(90.0, 1.0, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEquals(Trend::PROGRESSION, $report->trend);
    }

    public function testDetectsRegressionTrend(): void
    {
        $responses = [
            new PatientResponse(90.0, 1.0, new \DateTimeImmutable()),
            new PatientResponse(70.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(50.0, 3.0, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEquals(Trend::REGRESSION, $report->trend);
    }

    public function testDetectsStableTrend(): void
    {
        $responses = [
            new PatientResponse(70.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(72.0, 2.1, new \DateTimeImmutable()),
            new PatientResponse(71.0, 2.0, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEquals(Trend::STABLE, $report->trend);
    }

    public function testTotalResponsesCount(): void
    {
        $responses = [
            new PatientResponse(70.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(80.0, 1.5, new \DateTimeImmutable()),
            new PatientResponse(75.0, 1.8, new \DateTimeImmutable()),
            new PatientResponse(85.0, 1.2, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertEquals(4, $report->totalResponses);
    }

    public function testRecommendationForHighPrecision(): void
    {
        $responses = [
            new PatientResponse(90.0, 1.0, new \DateTimeImmutable()),
            new PatientResponse(92.0, 0.9, new \DateTimeImmutable()),
            new PatientResponse(88.0, 1.1, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertNotEmpty($report->recommendation);
    }

    public function testRecommendationForLowPrecision(): void
    {
        $responses = [
            new PatientResponse(40.0, 5.0, new \DateTimeImmutable()),
            new PatientResponse(35.0, 6.0, new \DateTimeImmutable()),
            new PatientResponse(45.0, 5.5, new \DateTimeImmutable()),
        ];

        $report = $this->evaluator->evaluate($responses);

        $this->assertNotEmpty($report->recommendation);
    }
}
