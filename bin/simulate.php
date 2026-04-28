<?php

declare(strict_types=1);

require_once __DIR__ . '/../.venv/autoload.php';

use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Service\AdaptiveEngine;
use Motoadapt\Service\DifficultyCalculator;
use Motoadapt\Service\PerformanceEvaluator;

// --- Wiring (only place concrete classes are instantiated) ---
$engine = new AdaptiveEngine(new DifficultyCalculator(), new PerformanceEvaluator());

// --- Session data ---
$patient = 'Marie Lambert';
$disorder = 'Parkinson';
$currentLevel = DifficultyLevel::EASY;

$rounds = [
    new PatientResponse(90.0, 1.2, new DateTimeImmutable()),
    new PatientResponse(88.0, 1.1, new DateTimeImmutable()),
    new PatientResponse(92.0, 1.0, new DateTimeImmutable()),
    new PatientResponse(45.0, 4.2, new DateTimeImmutable()),
    new PatientResponse(40.0, 4.8, new DateTimeImmutable()),
    new PatientResponse(38.0, 5.1, new DateTimeImmutable()),
];

// --- Output ---
$separator    = str_repeat('─', 66);
$levelOrder   = [DifficultyLevel::EASY->name => 0, DifficultyLevel::MEDIUM->name => 1, DifficultyLevel::HARD->name => 2, DifficultyLevel::EXPERT->name => 3];
$windowFill   = 0;
$afterChange  = false;

echo "Patient: {$patient} | Disorder: {$disorder} | Initial level: {$currentLevel->name}" . PHP_EOL;
echo $separator . PHP_EOL;

foreach ($rounds as $i => $response) {
    $roundNumber = $i + 1;
    $windowFill++;
    $decision    = $engine->process($response, $currentLevel);

    $action = match (true) {
        !$decision->levelChanged => 'MAINTAIN',
        $levelOrder[$decision->newLevel->name] > $levelOrder[$decision->previousLevel->name] => 'INCREASE',
        default => 'DECREASE',
    };

    echo sprintf(
        "Round %d | Score: %.1f%% | Time: %.1fs | Decision: %s",
        $roundNumber,
        $response->score,
        $response->responseTime,
        $action,
    ) . PHP_EOL;

    if (!$decision->levelChanged) {
        $suffix = match (true) {
            str_contains($decision->reason, 'Insufficient') && !$afterChange => " (insufficient data — {$windowFill}/3 responses)",
            str_contains($decision->reason, 'Insufficient')                  => " (insufficient new window)",
            default                                                           => " ({$decision->reason})",
        };
        echo "Level: {$currentLevel->name}{$suffix}" . PHP_EOL;
    } else {
        echo sprintf(
            "Level: %s → %s (%s)",
            $decision->previousLevel->name,
            $decision->newLevel->name,
            $decision->reason,
        ) . PHP_EOL;
        $currentLevel = $decision->newLevel;
        $windowFill   = 0;
        $afterChange  = true;
    }
}

echo $separator . PHP_EOL;
echo "PERFORMANCE REPORT" . PHP_EOL;

$report = $engine->getPerformanceReport();

echo sprintf("Total responses  : %d", $report->totalResponses) . PHP_EOL;
echo sprintf("Avg precision    : %.1f%%", $report->avgPrecision) . PHP_EOL;
echo sprintf("Avg response     : %.1fs", $report->avgResponseTime) . PHP_EOL;
echo sprintf("Trend            : %s", $report->trend->name) . PHP_EOL;
echo sprintf("Recommendation   : %s", $report->recommendation) . PHP_EOL;
