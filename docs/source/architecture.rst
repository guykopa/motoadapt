Architecture
============

Pattern
-------

motoadapt applies **Lightweight Hexagonal Architecture** (Ports & Adapters) without a framework.

Why Hexagonal
-------------

The domain (Model + Service) never depends on infrastructure.
Ports (interfaces) define the contracts between layers.
``simulate.php`` is the only file that knows about concrete classes.

- **Clean Architecture** would add unnecessary layers (Entities, UseCases, Interface Adapters) for a project with no database and no HTTP layer.
- **Layered** would couple ``DifficultyCalculator`` directly to ``AdaptiveEngine``.
- **Hexagonal** is the right level: clean, demonstrable, proportionate.

Dependency rule
---------------

.. code-block:: text

   bin/simulate.php  (wiring only)
         ↓
   AdaptiveEngine
         ↓ depends on interface, never on concrete class
   DifficultyCalculatorInterface    AdaptiveEngineInterface
         ↑ implemented by
   DifficultyCalculator         PerformanceEvaluator

- ``AdaptiveEngine`` never imports ``DifficultyCalculator`` directly.
- ``simulate.php`` is the only file that instantiates concrete classes.
- Model layer never imports from Service layer.
- Port layer never imports from Service layer.

Layer diagram
-------------

.. code-block:: text

   ┌──────────────────────────────────────────┐
   │           bin/simulate.php               │
   │   instantiates and wires concrete classes│
   └─────────────────────┬────────────────────┘
                         │
   ┌─────────────────────▼────────────────────┐
   │             Service layer                │
   │  AdaptiveEngine                          │
   │  DifficultyCalculator                    │
   │  PerformanceEvaluator                    │
   └─────────────────────┬────────────────────┘
                         │ depends on
   ┌─────────────────────▼────────────────────┐
   │              Port layer                  │
   │   AdaptiveEngineInterface                │
   │   DifficultyCalculatorInterface          │
   └─────────────────────┬────────────────────┘
                         │
   ┌─────────────────────▼────────────────────┐
   │             Model layer                  │
   │   DifficultyLevel   Trend                │
   │   PatientResponse   AdaptiveDecision     │
   │   PerformanceReport                      │
   └──────────────────────────────────────────┘

SOLID mapping
-------------

.. list-table::
   :header-rows: 1
   :widths: 10 90

   * - Principle
     - Application
   * - **S**
     - ``DifficultyCalculator`` only calculates. ``PerformanceEvaluator`` only evaluates. ``AdaptiveEngine`` only orchestrates.
   * - **O**
     - Add a new strategy by implementing ``AdaptiveEngineInterface`` — never modify existing classes.
   * - **L**
     - Any ``AdaptiveEngineInterface`` implementation is substitutable. ``MockAdaptiveEngine`` and ``AdaptiveEngine`` are interchangeable in tests.
   * - **I**
     - ``AdaptiveEngineInterface`` exposes only what callers need. ``DifficultyCalculatorInterface`` exposes only what ``AdaptiveEngine`` needs.
   * - **D**
     - ``AdaptiveEngine`` receives ``DifficultyCalculator`` via constructor injection. ``simulate.php`` is the only place where concrete classes are instantiated.

Project structure
-----------------

.. code-block:: text

   motoadapt/
   ├── src/
   │   ├── Model/       ← pure readonly value objects, no logic
   │   ├── Port/        ← interfaces only, no implementation
   │   └── Service/     ← business logic only
   ├── tests/           ← one test file per class
   ├── bin/
   │   └── simulate.php ← demo script, wiring only
   ├── docs/            ← Sphinx documentation
   ├── composer.json
   └── README.md
