# motoadapt

Adaptive engine for motor rehabilitation exercises.

Automatically adjusts exercise difficulty based on patient performance using a sliding window algorithm over the last 3 responses. No database, no framework, no frontend — pure PHP 8.2, strict TDD, strict SOLID.

---

## Requirements

- PHP >= 8.2
- Composer

---

## Setup

### 1. Install PHP 8.2

On Ubuntu/Debian, the default repos only ship PHP 8.1. Add the official PPA:

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2-cli
```

Verify:

```bash
php --version
# PHP 8.2.x
```

### 2. Install required PHP extensions

```bash
sudo apt install -y php8.2-xml php8.2-mbstring php8.2-curl
```

### 3. Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

Verify:

```bash
composer --version
# Composer 2.x
```

### 4. Install project dependencies

```bash
composer install
```

Dependencies are installed in `.venv/`.

---

## Run tests

```bash
.venv/bin/phpunit
```

Expected output:

```
OK (22 tests, 48 assertions)
```

---

## Run the demo

```bash
php bin/simulate.php
```

Expected output:

```
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
```

---

## Architecture

Lightweight Hexagonal Architecture (Ports & Adapters), no framework.

```
bin/simulate.php             ← wiring only, zero business logic
└── Service/
    ├── AdaptiveEngine       ← orchestrates decisions (depends on interfaces)
    ├── DifficultyCalculator ← sliding window algorithm
    └── PerformanceEvaluator ← session performance summary
Port/
    ├── AdaptiveEngineInterface
    └── DifficultyCalculatorInterface
Model/
    ├── DifficultyLevel      (enum: EASY / MEDIUM / HARD / EXPERT)
    ├── Trend                (enum: PROGRESSION / STABLE / REGRESSION)
    ├── PatientResponse      (readonly)
    ├── AdaptiveDecision     (readonly)
    └── PerformanceReport    (readonly)
```

---

## Adaptive algorithm

Window size: last 3 `PatientResponse` objects.

| Avg score    | Action                         |
|--------------|--------------------------------|
| >= 85%       | Increase difficulty one level  |
| < 50%        | Decrease difficulty one level  |
| 50% – 84.9%  | Maintain current level         |
| Window < 3   | Maintain — insufficient data   |

Boundary rules: EASY cannot decrease, EXPERT cannot increase.

---

## SOLID principles

| Principle | Application |
|-----------|-------------|
| **S** | One class = one responsibility: calculate / evaluate / orchestrate |
| **O** | New strategy = new `AdaptiveEngineInterface` implementation, no modification |
| **L** | `MockAdaptiveEngine` and `AdaptiveEngine` are interchangeable in tests |
| **I** | Two interfaces: one for the engine, one for the calculator |
| **D** | `AdaptiveEngine` receives `DifficultyCalculatorInterface` via constructor injection |

---

## Documentation

Full Sphinx documentation is available in `docs/`.

Install Sphinx:

```bash
python -m venv .sphinx-venv
.sphinx-venv/bin/pip install -r docs/requirements.txt
```

Build HTML:

```bash
cd docs && make html
```

Open `docs/_build/html/index.html` in a browser.

---

## Project context

Built to demonstrate adaptive system design and clean PHP engineering for the OBSERVACT/CeRCA research position.
