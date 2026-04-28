Services
========

DifficultyCalculator
--------------------

**Namespace:** ``Motoadapt\Service``

**Implements:** ``DifficultyCalculatorInterface``

Calculates the new difficulty level from a sliding window of patient responses.
This class has a single responsibility: apply the threshold rules and boundary rules
to produce an ``AdaptiveDecision``.

.. code-block:: php

   public function calculate(array $window, DifficultyLevel $current): AdaptiveDecision

- If ``count($window) < 3`` → returns MAINTAIN with reason ``"Insufficient data"``
- If ``avgScore >= 85.0`` → increases one level (or maintains EXPERT)
- If ``avgScore < 50.0`` → decreases one level (or maintains EASY)
- Otherwise → maintains current level

PerformanceEvaluator
--------------------

**Namespace:** ``Motoadapt\Service``

Evaluates patient performance over a full session and produces a ``PerformanceReport``.

.. code-block:: php

   public function evaluate(array $responses): PerformanceReport

Trend detection compares the first and last response scores:

- Delta >= +10 → ``PROGRESSION``
- Delta <= -10 → ``REGRESSION``
- Otherwise    → ``STABLE``

AdaptiveEngine
--------------

**Namespace:** ``Motoadapt\Service``

**Implements:** ``AdaptiveEngineInterface``

Orchestrates the adaptation decision. Maintains the sliding window and the full
response history. Depends on ``DifficultyCalculatorInterface`` and ``PerformanceEvaluator``
via constructor injection — never instantiates them internally.

.. code-block:: php

   public function __construct(
       private readonly DifficultyCalculatorInterface $calculator,
       private readonly PerformanceEvaluator $evaluator,
   ) {}

   public function process(PatientResponse $response, DifficultyLevel $current): AdaptiveDecision

   public function getPerformanceReport(): PerformanceReport

- ``process()`` appends the response to the sliding window (max 3) and delegates to ``$calculator->calculate()``.
- ``getPerformanceReport()`` delegates to ``$evaluator->evaluate()`` over all recorded responses.
- Throws ``\RuntimeException`` if ``getPerformanceReport()`` is called before any response is recorded.

Ports (interfaces)
------------------

.. code-block:: php

   // src/Port/DifficultyCalculatorInterface.php
   interface DifficultyCalculatorInterface
   {
       public function calculate(array $window, DifficultyLevel $current): AdaptiveDecision;
   }

.. code-block:: php

   // src/Port/AdaptiveEngineInterface.php
   interface AdaptiveEngineInterface
   {
       public function process(PatientResponse $response, DifficultyLevel $current): AdaptiveDecision;
       public function getPerformanceReport(): PerformanceReport;
   }
