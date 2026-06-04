# moviemercredi

## Deployment (Serverless / Lambda)

`useDotenv: true` in `serverless.yml` lets Serverless Framework read `.env` locally to interpolate `${env:VAR}` references — it does **not** send the whole file to Lambda. Every env var the app needs in production must be explicitly listed under `provider.environment` in `serverless.yml`, e.g.:

```yaml
provider:
    environment:
        SOME_VAR: ${env:SOME_VAR}
```

If a var is missing from that block, `env('SOME_VAR')` will return `null` in production even if it exists in `.env` locally.
