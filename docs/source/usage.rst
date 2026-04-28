Usage
=====

Setup
-----

1. Install PHP 8.2
~~~~~~~~~~~~~~~~~~

On Ubuntu/Debian, the default repositories only ship PHP 8.1.
Add the official PPA to get PHP 8.2:

.. code-block:: bash

   sudo apt install -y software-properties-common
   sudo add-apt-repository ppa:ondrej/php
   sudo apt update
   sudo apt install -y php8.2-cli

Verify:

.. code-block:: bash

   php --version
   # PHP 8.2.x

2. Install required PHP extensions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

PHPUnit 11 requires ``dom``, ``mbstring``, and ``curl``:

.. code-block:: bash

   sudo apt install -y php8.2-xml php8.2-mbstring php8.2-curl

3. Install Composer
~~~~~~~~~~~~~~~~~~~

Do not use ``apt install composer`` — it ships an outdated version.
Use the official installer:

.. code-block:: bash

   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
   php -r "unlink('composer-setup.php');"

Verify:

.. code-block:: bash

   composer --version
   # Composer 2.x

4. Install project dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

   composer install

Dependencies are installed in ``.venv/``.

Run the tests
-------------

.. code-block:: bash

   .venv/bin/phpunit

Expected output:

.. code-block:: text

   OK (22 tests, 48 assertions)

Run the demo
------------

.. code-block:: bash

   php bin/simulate.php

Expected output
---------------

.. code-block:: text

   Patient: Marie Lambert | Disorder: Parkinson | Initial level: EASY
   ──────────────────────────────────────────────────────────────────
   Round 1 | Score: 90.0% | Time: 1.2s | Decision: MAINTAIN
   Level: EASY (insufficient data — 1/3 responses)
   Round 2 | Score: 88.0% | Time: 1.1s | Decision: MAINTAIN
   Level: EASY (insufficient data — 2/3 responses)
   Round 3 | Score: 92.0% | Time: 1.0s | Decision: INCREASE
   Level: EASY → MEDIUM (3 consecutive high scores — avg: 90.0%)
   Round 4 | Score: 45.0% | Time: 4.2s | Decision: MAINTAIN
   Level: MEDIUM (insufficient new window)
   Round 5 | Score: 40.0% | Time: 4.8s | Decision: MAINTAIN
   Level: MEDIUM (insufficient new window)
   Round 6 | Score: 38.0% | Time: 5.1s | Decision: DECREASE
   Level: MEDIUM → EASY (Score below threshold — avg: 41.0%)
   ──────────────────────────────────────────────────────────────────
   PERFORMANCE REPORT
   Total responses  : 6
   Avg precision    : 65.5%
   Avg response     : 2.9s
   Trend            : REGRESSION
   Recommendation   : Performance declining — review exercise parameters

Build the documentation
-----------------------

Install Sphinx and the theme:

.. code-block:: bash

   python -m venv .sphinx-venv
   .sphinx-venv/bin/pip install -r docs/requirements.txt

Build HTML output:

.. code-block:: bash

   cd docs && make html

Open ``docs/_build/html/index.html`` in a browser.
