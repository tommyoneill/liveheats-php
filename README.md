# LiveHeats PHP Client

This is an **early release** of a PHP library designed to interact with the [LiveHeats GraphQL API](https://liveheats.com/api/graphql).  
It allows developers to easily retrieve event data, athlete rankings, and competition results for snowboarding, surfing, and similar sports tracked on the LiveHeats platform.

## ⚠️ Status: Early Access

This library is actively being developed and may contain breaking changes. Use at your own risk until version 1.0 is released.

## Features

- Query event information
- Retrieve divisions, heats, leaderboards
- Fetch series rankings and athlete-specific performance
- Built-in Guzzle support
- Object-oriented architecture
- Error handling and input validation

## Installation

```bash
composer require tommyoneill/liveheats-php
```

> You may also clone this repo and include it directly in your project.

## Usage

```php
use LiveHeats\LiveHeatsService;

$service = new LiveHeatsService();
$org = $service->getOrganisationByShortName("usasaums");
print_r($org);
```

## License

MIT © 2025 Tom O'Neill

See [LICENSE](LICENSE) for details.
