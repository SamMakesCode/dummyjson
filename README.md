# DummyJSON

A PHP implementation of the DummyJSON API.

## Get a single user

```php
$dummy = new DummyJSON;

$user = $dummy->users()->getById(1);
```

## Get a page of users

```php
$dummy = new DummyJSON;

$users = $dummy->users()->getPage();
```

## Create a user

```php
$dummy = new DummyJSON;

$dummy->users()->create('John', 'Smith', 'john@example.org');
```

## Tests

### PHPStan

```bash
vendor/bin/phpstan analyse src --level=8
```

### Unit/Integration

```bash
vendor/bin/phpunit tests
```
