# Phalcon Routes to OpenApi

This library generates a representation of the routes in your project ready to be used with any OpenApi-compatible service(swagger for example).

## Additional Annotations

You can define returns via `@return-{status code} {mime-type} {schema}` with all but the status code being optional. For example:

```php
     * @return-200 application/json {"type":"object"}
```