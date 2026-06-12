# moviemercredi

This is a **Laravel** app. In production it is deployed with **Bref** (via the Serverless Framework) to **AWS Lambda**, and pages are served through **API Gateway** (`httpApi` events routing all requests to the `php-fpm` runtime handling `public/index.php`; artisan commands run on a separate `console` runtime function).

## Deployment (Serverless / Lambda)

`useDotenv: true` in `serverless.yml` lets Serverless Framework read `.env` locally to interpolate `${env:VAR}` references — it does **not** send the whole file to Lambda. Every env var the app needs in production must be explicitly listed under `provider.environment` in `serverless.yml`, e.g.:

```yaml
provider:
    environment:
        SOME_VAR: ${env:SOME_VAR}
```

If a var is missing from that block, `env('SOME_VAR')` will return `null` in production even if it exists in `.env` locally.

## Icons

Use **Heroicons** (inline SVGs). It's the icon pack used by Laravel Breeze/Jetstream. No CDN or npm package needed — copy the SVG path directly from [heroicons.com](https://heroicons.com) and inline it. Use the outline style (`stroke-width="1.5"`) to match existing icons.
