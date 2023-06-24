# api-bundle
Bundle including some services for develop REST API in Symfony.
Includes a request body json schema validator and an exception listener to transforms from exceptions to api problem.

## Installation
```
composer require zisato/api-bundle
```


## Default configuration
api:
  api_problem:
    enabled: true
    exception_handlers: []
  json_schema_path: %kernel.project_dir%/public/schemas/