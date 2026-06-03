# Movie Mercredi

A Laravel application deployed on AWS Lambda via [Bref](https://bref.sh).

## Setup

```bash
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

## Local development

```bash
docker-compose up
```

App available at [http://localhost:8000](http://localhost:8000).

## Deployment

```bash
php artisan config:clear
serverless deploy --stage prod
```

DynamoDB tables are created automatically on first deploy. If you need to recreate a table (e.g. after a key schema change), CloudFormation won't replace a custom-named resource in-place. Do a full stack reset instead:

```bash
serverless remove --stage prod
serverless deploy --stage prod
```

If you also need to manually delete a table first:

```bash
aws dynamodb delete-table --table-name movie-mercredi-polls --region us-east-1

# Wait for deletion (~10–30s), then check it's gone:
aws dynamodb describe-table --table-name movie-mercredi-polls --region us-east-1
# ResourceNotFoundException means it's gone
```

## Logs

```bash
serverless logs -f web --tail
```
