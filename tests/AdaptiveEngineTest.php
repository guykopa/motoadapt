<?php

declare(strict_types=1);

namespace Motoadapt\Tests;

use Motoadapt\Model\AdaptiveDecision;
use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Port\DifficultyCalculatorInterface;
use Motoadapt\Service\AdaptiveEngine;
use Motoadapt\Service\PerformanceEvaluator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Motoadapt\Service\AdaptiveEngine::class)]
class AdaptiveEngineTest extends TestCase
{
    private DifficultyCalculatorInterface $calculator;
    private PerformanceEvaluator $evaluator;
    private AdaptiveEngine $engine;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(DifficultyCalculatorInterface::class);
        $this->evaluator  = $this->createMock(PerformanceEvaluator::class);
        $this->engine     = new AdaptiveEngine($this->calculator, $this->evaluator);
    }

    public function testProcessDelegatesDecisionToCalculator(): void
    {
        $response = new PatientResponse(90.0, 1.0, new \DateTimeImmutable());
        $expected = new AdaptiveDecision(
            DifficultyLevel::MEDIUM,
            DifficultyLevel::EASY,
            '3 consecutive high scores — avg: 90.0%',
            true,
        );

        $this->calculator
            ->expects($this->once())
            ->method('calculate')
            ->willReturn($expected);

        $decision = $this->engine->process($response, DifficultyLevel::EASY);

        $this->assertSame($expected, $decision);
    }

    public function testProcessAccumulatesResponsesInWindow(): void
    {
        $r1 = new PatientResponse(90.0, 1.0, new \DateTimeImmutable());
        $r2 = new PatientResponse(88.0, 1.1, new \DateTimeImmutable());
        $r3 = new PatientResponse(92.0, 1.0, new \DateTimeImmutable());

        $decision = new AdaptiveDecision(DifficultyLevel::EASY, DifficultyLevel::EASY, 'Insufficient data', false);

        $this->calculator->method('calculate')->willReturn($decision);

        $this->engine->process($r1, DifficultyLevel::EASY);
        $this->engine->process($r2, DifficultyLevel::EASY);

        $this->calculator
            ->expects($this->once())
            ->method('calculate')
            ->with(
                $this->callback(fn(array $w) => count($w) === 3),
                DifficultyLevel::EASY,
            )
            ->willReturn($decision);

        $this->engine->process($r3, DifficultyLevel::EASY);
    }

    public function testWindowKeepsOnlyLastThreeResponses(): void
    {
        $responses = [
            new PatientResponse(50.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(55.0, 2.1, new \DateTimeImmutable()),
            new PatientResponse(60.0, 2.2, new \DateTimeImmutable()),
            new PatientResponse(65.0, 2.3, new \DateTimeImmutable()),
        ];

        $decision = new AdaptiveDecision(DifficultyLevel::EASY, DifficultyLevel::EASY, 'Stable', false);
        $this->calculator->method('calculate')->willReturn($decision);

        foreach (array_slice($responses, 0, 3) as $r) {
            $this->engine->process($r, DifficultyLevel::EASY);
        }

        $captured = [];
        $this->calculator
            ->expects($this->once())
            ->method('calculate')
            ->with(
                $this->callback(function (array $w) use (&$captured) {
                    $captured = $w;
                    return count($w) === 3;
                }),
                DifficultyLevel::EASY,
            )
            ->willReturn($decision);

        $this->engine->process($responses[3], DifficultyLevel::EASY);

        $this->assertCount(3, $captured);
        $this->assertSame($responses[1], $captured[0]);
        $this->assertSame($responses[2], $captured[1]);
        $this->assertSame($responses[3], $captured[2]);
    }

    public function testGetPerformanceReportDelegatesToEvaluator(): void
    {
        $response = new PatientResponse(70.0, 2.0, new \DateTimeImmutable());
        $decision = new AdaptiveDecision(DifficultyLevel::EASY, DifficultyLevel::EASY, 'Stable', false);

        $this->calculator->method('calculate')->willReturn($decision);
        $this->engine->process($response, DifficultyLevel::EASY);

        $this->evaluator
            ->expects($this->once())
            ->method('evaluate')
            ->with($this->callback(fn(array $r) => count($r) === 1));

        $this->engine->getPerformanceReport();
    }

    public function testGetPerformanceReportThrowsWhenNoResponses(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->engine->getPerformanceReport();
    }
}
