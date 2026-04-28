Domain Models
=============

All models are immutable. The Model layer never imports from the Service or Port layers.

DifficultyLevel
---------------

Enum representing the four exercise difficulty levels.

.. code-block:: php

   enum DifficultyLevel
   {
       case EASY;
       case MEDIUM;
       case HARD;
       case EXPERT;
   }

Trend
-----

Enum representing the performance trend over a session.

.. code-block:: php

   enum Trend
   {
       case PROGRESSION;
       case STABLE;
       case REGRESSION;
   }

PatientResponse
---------------

Immutable record of a single patient exercise response.

.. code-block:: php

   readonly class PatientResponse
   {
       public function __construct(
           public float $score,               // 0.0 to 100.0
           public float $responseTime,        // seconds
           public \DateTimeImmutable $recordedAt,
       ) {}
   }

AdaptiveDecision
----------------

Immutable result of an adaptation decision.

.. code-block:: php

   readonly class AdaptiveDecision
   {
       public function __construct(
           public DifficultyLevel $newLevel,
           public DifficultyLevel $previousLevel,
           public string $reason,
           public bool $levelChanged,
       ) {}
   }

PerformanceReport
-----------------

Immutable summary of patient performance over a full session.

.. code-block:: php

   readonly class PerformanceReport
   {
       public function __construct(
           public float $avgPrecision,        // percentage
           public float $avgResponseTime,     // seconds
           public Trend $trend,
           public string $recommendation,
           public int $totalResponses,
       ) {}
   }
