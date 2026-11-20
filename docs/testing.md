# Testing

The project uses PHPUnit 12 through Laravel's test runner. Tests are grouped by behavior under `tests/Feature/` and `tests/Unit/`.

## Run tests

```bash
php artisan test
```

Run a focused test file while developing:

```bash
php artisan test tests/Feature/LeaveWorkflowTest.php
php artisan test tests/Unit/Services/TaxEngineTest.php
```

Composer's `test` script clears the configuration cache and then invokes Laravel's test runner:

```bash
composer test
```

## Test organization

- Feature tests exercise HTTP behavior, access control, workflows, and persistence.
- Unit tests cover calculations and isolated services such as payroll, attendance, benefits, and authentication.
- `phpunit.xml` configures the test environment. Keep tests independent from developer and production databases; use the configured test database and Laravel testing helpers.

## Adding coverage

For a behavior change, cover the expected success path and relevant failure or authorization path. For calculations, include boundary cases and explicit expected values. Use factories or test fixtures rather than real personal data. Avoid coupling tests to implementation details when observable behavior is sufficient.

Tests are not run automatically as part of every code change. Report commands and results when verification is requested or performed.
