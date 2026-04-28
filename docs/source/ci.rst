CI/CD & GitHub Pages
====================

Two pipelines run automatically on every push to ``main``.

CI — Tests
----------

**File:** ``.github/workflows/ci.yml``

Runs on every push and pull request to ``main``.

Steps:

1. Checkout code
2. Setup PHP 8.2
3. ``composer install`` — installs PHPUnit into ``.venv/``
4. ``.venv/bin/phpunit`` — runs all 22 tests

.. code-block:: yaml

   on:
     push:
       branches: [main]
     pull_request:
       branches: [main]

   jobs:
     test:
       runs-on: ubuntu-latest
       steps:
         - uses: actions/checkout@v4
         - uses: shivammathur/setup-php@v2
           with:
             php-version: "8.2"
         - run: composer install --no-interaction --prefer-dist
         - run: .venv/bin/phpunit --colors=always

Docs — GitHub Pages
-------------------

**File:** ``.github/workflows/docs.yml``

Runs on every push to ``main``. Builds the Sphinx documentation and deploys it to GitHub Pages.

Steps:

1. Checkout code
2. Setup Python 3.12
3. ``pip install -r docs/requirements.txt`` — installs Sphinx + RTD theme
4. ``sphinx-build`` — builds HTML into ``docs/_build/html``
5. Upload artifact and deploy to GitHub Pages

.. code-block:: yaml

   on:
     push:
       branches: [main]

   permissions:
     pages: write
     id-token: write

Enable GitHub Pages
-------------------

In the GitHub repository settings:

1. Go to **Settings → Pages**
2. Set **Source** to **GitHub Actions**
3. Save

The documentation will be published at:

.. code-block:: text

   https://<your-github-username>.github.io/motoadapt/

After the first successful deployment, the URL appears in the **Pages** section of the repository settings.
