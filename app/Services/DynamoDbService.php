<?php

namespace App\Services;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class DynamoDbService
{
    private DynamoDbClient $client;
    private Marshaler $marshaler;
    private string $pollsTable;
    private string $votesTable;
    private string $coversTable;

    public function __construct()
    {
        $config = [
            'region' => config('dynamodb.region', 'us-east-1'),
            'version' => 'latest',
        ];

        $endpoint = config('dynamodb.endpoint');
        if ($endpoint) {
            $config['endpoint'] = $endpoint;
            $config['credentials'] = [
                'key' => env('AWS_ACCESS_KEY_ID', 'local'),
                'secret' => env('AWS_SECRET_ACCESS_KEY', 'local'),
            ];
        }

        $this->client = new DynamoDbClient($config);
        $this->marshaler = new Marshaler();
        $this->pollsTable = config('dynamodb.tables.polls', 'movie-mercredi-polls');
        $this->votesTable = config('dynamodb.tables.votes', 'movie-mercredi-votes');
        $this->coversTable = config('dynamodb.tables.covers', 'movie-mercredi-covers');
    }

    public function putPoll(string $slug, array $movies, int $maxVotes): void
    {
        $this->client->putItem([
            'TableName' => $this->pollsTable,
            'Item' => $this->marshaler->marshalItem([
                'slug' => $slug,
                'movies' => $movies,
                'max_votes' => $maxVotes,
                'created_at' => now()->toIso8601String(),
            ]),
        ]);
    }

    public function getPoll(string $slug): ?array
    {
        $result = $this->client->getItem([
            'TableName' => $this->pollsTable,
            'Key' => $this->marshaler->marshalItem(['slug' => $slug]),
        ]);

        if (empty($result['Item'])) {
            return null;
        }

        return $this->marshaler->unmarshalItem($result['Item']);
    }

    public function putVote(string $pollId, string $voterName, array $ranking): void
    {
        $this->client->putItem([
            'TableName' => $this->votesTable,
            'Item' => $this->marshaler->marshalItem([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'poll_id' => $pollId,
                'voter_name' => $voterName,
                'ranking' => $ranking,
                'created_at' => now()->toIso8601String(),
            ]),
        ]);
    }

    public function getVotes(string $pollId): array
    {
        $result = $this->client->query([
            'TableName' => $this->votesTable,
            'IndexName' => 'poll_id-index',
            'KeyConditionExpression' => 'poll_id = :pid',
            'ExpressionAttributeValues' => $this->marshaler->marshalItem([':pid' => $pollId]),
        ]);

        return array_map(
            fn($item) => $this->marshaler->unmarshalItem($item),
            $result['Items']
        );
    }

    public function getCover(string $cacheKey): ?string
    {
        $result = $this->client->getItem([
            'TableName' => $this->coversTable,
            'Key' => $this->marshaler->marshalItem(['cover_key' => $cacheKey]),
        ]);

        if (empty($result['Item'])) {
            return null;
        }

        return $this->marshaler->unmarshalItem($result['Item'])['poster_url'];
    }

    public function putCover(string $cacheKey, string $posterUrl): void
    {
        $this->client->putItem([
            'TableName' => $this->coversTable,
            'Item' => $this->marshaler->marshalItem([
                'cover_key' => $cacheKey,
                'poster_url' => $posterUrl,
                'fetched_at' => now()->toIso8601String(),
            ]),
        ]);
    }

    public function ensureTableExists(): void
    {
        try {
            $this->client->describeTable(['TableName' => $this->pollsTable]);
        } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
            if ($e->getAwsErrorCode() === 'ResourceNotFoundException') {
                $this->client->createTable([
                    'TableName' => $this->pollsTable,
                    'KeySchema' => [
                        ['AttributeName' => 'slug', 'KeyType' => 'HASH'],
                    ],
                    'AttributeDefinitions' => [
                        ['AttributeName' => 'slug', 'AttributeType' => 'S'],
                    ],
                    'BillingMode' => 'PAY_PER_REQUEST',
                ]);
                $this->client->waitUntil('TableExists', ['TableName' => $this->pollsTable]);
            } else {
                throw $e;
            }
        }
    }
}
