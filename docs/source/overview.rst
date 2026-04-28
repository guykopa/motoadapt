Overview
========

Project context
---------------

motoadapt was built to demonstrate adaptive system design and clean PHP engineering
for the OBSERVACT/CeRCA research position. It mirrors the kind of adaptive logic
used in real motor rehabilitation assessment: automatic difficulty progression
based on measured patient performance.

Goals
-----

- Adaptive system logic — auto-adaptive proposals based on patient data
- Automated performance evaluation
- Clean software engineering in PHP

Constraints
-----------

- No database
- No framework
- No frontend
- PHP 8.2 minimum
- Strict TDD (every test written before implementation)
- Strict SOLID at every level

Requirements
------------

- PHP >= 8.2
- Composer

Installation
------------

.. code-block:: bash

   composer install

Dependencies are installed in ``.venv/``.
