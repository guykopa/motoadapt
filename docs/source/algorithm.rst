Adaptive Algorithm
==================

Overview
--------

The adaptive engine uses a **sliding window** of the last 3 ``PatientResponse`` objects
to decide whether to increase, decrease, or maintain the current difficulty level.

Input
-----

- ``window`` — last 3 ``PatientResponse`` objects (FIFO)
- ``current`` — current ``DifficultyLevel`` (``EASY`` / ``MEDIUM`` / ``HARD`` / ``EXPERT``)

Step-by-step
------------

**Step 1 — Check window size**

.. code-block:: text

   if count(window) < 3:
       return MAINTAIN, reason = "Insufficient data"

**Step 2 — Calculate average score**

.. code-block:: text

   avgScore = mean(response.score for response in window)

**Step 3 — Apply threshold rules**

.. code-block:: text

   if avgScore >= 85.0:  action = INCREASE
   elif avgScore < 50.0: action = DECREASE
   else:                 action = MAINTAIN

**Step 4 — Apply boundary rules**

.. code-block:: text

   if action == INCREASE and current == EXPERT:
       return MAINTAIN EXPERT, reason = "Maximum level reached"
   if action == DECREASE and current == EASY:
       return MAINTAIN EASY, reason = "Minimum level reached"

**Step 5 — Apply level transition**

.. list-table::
   :header-rows: 1
   :widths: 30 30 40

   * - Current
     - Action
     - New level
   * - EASY
     - INCREASE
     - MEDIUM
   * - MEDIUM
     - INCREASE
     - HARD
   * - HARD
     - INCREASE
     - EXPERT
   * - EXPERT
     - DECREASE
     - HARD
   * - HARD
     - DECREASE
     - MEDIUM
   * - MEDIUM
     - DECREASE
     - EASY

Output
------

``AdaptiveDecision(newLevel, previousLevel, reason, levelChanged)``

Threshold summary
-----------------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Avg score
     - Result
   * - >= 85%
     - Increase one level
   * - < 50%
     - Decrease one level
   * - 50% – 84.9%
     - Maintain current level
   * - Window < 3
     - Maintain — insufficient data
