# Testing Mechanism

## Test Stack
- Framework: PHPUnit 11
- Application framework: Laravel 11
- Test types:
  - `tests/Feature` for application and HTTP behavior
  - `tests/Unit` for isolated unit tests
- Base test case: `tests/TestCase.php`

## Current Configuration
Testing is configured in `phpunit.xml` with these important defaults:
- `APP_ENV=testing`
- `CACHE_STORE=array`
- `SESSION_DRIVER=array`
- `MAIL_MAILER=array`
- `QUEUE_CONNECTION=sync`
- `BCRYPT_ROUNDS=4`

## How Tests Are Run
Run the full test suite:

```bash
php artisan test
```

Run PHPUnit directly:

```bash
vendor/bin/phpunit
```

Run only feature tests:

```bash
php artisan test tests/Feature
```

Run only unit tests:

```bash
php artisan test tests/Unit
```

Run a single test file:

```bash
php artisan test tests/Feature/HealthTest.php
```

Filter by test name:

```bash
php artisan test --filter=test_example
```

## Test Writing Rules
- Tests in this project use **PHPUnit classes only**.
- Pest is not used.
- New feature work should include or update a PHPUnit test.
- Feature coverage should be preferred for user-facing behavior.

## Existing Test Files
- `tests/Feature/AvatarTest.php`
- `tests/Feature/ExampleTest.php`
- `tests/Feature/HealthTest.php`
- `tests/Feature/HelloFileTest.php`
- `tests/Feature/ProfileTest.php`
- `tests/Unit/ExampleTest.php`

## Code Style Check
Before finalizing code changes, run:

```bash
vendor/bin/pint --dirty
```
