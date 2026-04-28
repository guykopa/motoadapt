Testing
=======

TDD cycle
---------

Every class follows the mandatory RED → GREEN → REFACTOR cycle:

1. **RED** — write all failing tests before the class exists
2. **GREEN** — write the minimum code to make tests pass
3. **REFACTOR** — clean without breaking any test
4. Run PHPUnit after every GREEN and REFACTOR phase

Run the test suite
------------------

.. code-block:: bash

   .venv/bin/phpunit

Test files
----------

.. list-table::
   :header-rows: 1
   :widths: 40 60

   * - File
     - Covers
   * - ``tests/DifficultyCalculatorTest.php``
     - ``Service\DifficultyCalculator``
   * - ``tests/AdaptiveEngineTest.php``
     - ``Service\AdaptiveEngine``
   * - ``tests/PerformanceEvaluatorTest.php``
     - ``Service\PerformanceEvaluator``

DifficultyCalculatorTest — cases
---------------------------------

- Increases level after three high scores (avg >= 85%)
- Maintains EXPERT at maximum boundary
- Decreases level after low scores (avg < 50%)
- Maintains EASY at minimum boundary
- Maintains level in target range (50% – 84.9%)
- Maintains with insufficient data (window < 3)
- Increases MEDIUM → HARD
- Decreases HARD → MEDIUM
- Empty window maintains level

AdaptiveEngineTest — cases
---------------------------

- ``process()`` delegates decision to calculator
- ``process()`` accumulates responses in the sliding window
- Window keeps only the last 3 responses (FIFO)
- ``getPerformanceReport()`` delegates to evaluator
- ``getPerformanceReport()`` throws ``\RuntimeException`` when no responses recorded

PerformanceEvaluatorTest — cases
---------------------------------

- Computes average precision correctly
- Computes average response time correctly
- Detects PROGRESSION trend
- Detects REGRESSION trend
- Detects STABLE trend
- Total responses count is correct
- Recommendation is not empty for high precision
- Recommendation is not empty for low precision
