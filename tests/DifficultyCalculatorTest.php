<?php

declare(strict_types=1);

namespace Motoadapt\Tests;

use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Service\DifficultyCalculator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Motoadapt\Service\DifficultyCalculator::class)]
class DifficultyCalculatorTest extends TestCase
{
    private DifficultyCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new DifficultyCalculator();
    }

    public function testIncreasesLevelAfterThreeHighScores(): void
    {
        $window = [
            new PatientResponse(90.0, 1.2, new \DateTimeImmutable()),
            new PatientResponse(88.0, 1.1, new \DateTimeImmutable()),
            new PatientResponse(92.0, 1.0, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::EASY);

        $this->assertEquals(DifficultyLevel::MEDIUM, $decision->newLevel);
        $this->assertEquals(DifficultyLevel::EASY, $decision->previousLevel);
        $this->assertTrue($decision->levelChanged);
        $this->assertStringContainsString('high scores', $decision->reason);
    }

    public function testMaintainsExpertLevelAtMaximum(): void
    {
        $window = [
            new PatientResponse(95.0, 0.9, new \DateTimeImmutable()),
            new PatientResponse(97.0, 0.8, new \DateTimeImmutable()),
            new PatientResponse(96.0, 0.9, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::EXPERT);

        $this->assertEquals(DifficultyLevel::EXPERT, $decision->newLevel);
        $this->assertFalse($decision->levelChanged);
        $this->assertStringContainsString('Maximum level', $decision->reason);
    }

    public function testDecreasesLevelAfterLowScores(): void
    {
        $window = [
            new PatientResponse(40.0, 4.5, new \DateTimeImmutable()),
            new PatientResponse(35.0, 5.1, new \DateTimeImmutable()),
            new PatientResponse(42.0, 4.8, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::MEDIUM);

        $this->assertEquals(DifficultyLevel::EASY, $decision->newLevel);
        $this->assertEquals(DifficultyLevel::MEDIUM, $decision->previousLevel);
        $this->assertTrue($decision->levelChanged);
    }

    public function testMaintainsEasyLevelAtMinimum(): void
    {
        $window = [
            new PatientResponse(30.0, 6.0, new \DateTimeImmutable()),
            new PatientResponse(25.0, 7.0, new \DateTimeImmutable()),
            new PatientResponse(28.0, 6.5, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::EASY);

        $this->assertEquals(DifficultyLevel::EASY, $decision->newLevel);
        $this->assertFalse($decision->levelChanged);
        $this->assertStringContainsString('Minimum level', $decision->reason);
    }

    public function testMaintainsLevelInTargetRange(): void
    {
        $window = [
            new PatientResponse(70.0, 2.0, new \DateTimeImmutable()),
            new PatientResponse(75.0, 1.9, new \DateTimeImmutable()),
            new PatientResponse(72.0, 2.1, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::MEDIUM);

        $this->assertEquals(DifficultyLevel::MEDIUM, $decision->newLevel);
        $this->assertFalse($decision->levelChanged);
    }

    public function testMaintainsLevelWithInsufficientData(): void
    {
        $window = [
            new PatientResponse(90.0, 1.0, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::EASY);

        $this->assertEquals(DifficultyLevel::EASY, $decision->newLevel);
        $this->assertFalse($decision->levelChanged);
        $this->assertStringContainsString('Insufficient data', $decision->reason);
    }

    public function testIncreasesFromMediumToHard(): void
    {
        $window = [
            new PatientResponse(86.0, 1.0, new \DateTimeImmutable()),
            new PatientResponse(90.0, 0.9, new \DateTimeImmutable()),
            new PatientResponse(88.0, 1.1, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::MEDIUM);

        $this->assertEquals(DifficultyLevel::HARD, $decision->newLevel);
        $this->assertTrue($decision->levelChanged);
    }

    public function testDecreasesFromHardToMedium(): void
    {
        $window = [
            new PatientResponse(45.0, 3.5, new \DateTimeImmutable()),
            new PatientResponse(48.0, 3.8, new \DateTimeImmutable()),
            new PatientResponse(40.0, 4.0, new \DateTimeImmutable()),
        ];

        $decision = $this->calculator->calculate($window, DifficultyLevel::HARD);

        $this->assertEquals(DifficultyLevel::MEDIUM, $decision->newLevel);
        $this->assertTrue($decision->levelChanged);
    }

    public function testEmptyWindowMaintainsLevel(): void
    {
        $decision = $this->calculator->calculate([], DifficultyLevel::MEDIUM);

        $this->assertEquals(DifficultyLevel::MEDIUM, $decision->newLevel);
        $this->assertFalse($decision->levelChanged);
        $this->assertStringContainsString('Insufficient data', $decision->reason);
    }
}
