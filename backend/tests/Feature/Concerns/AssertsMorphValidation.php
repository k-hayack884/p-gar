<?php

namespace Tests\Feature\Concerns;

use Illuminate\Testing\TestResponse;

trait AssertsMorphValidation
{
    /**
     * @return array<string, array{payload: array{type: string, id: int}, expectedStatus: int}>
     */
    public static function invalidMorphPayloads(): array
    {
        return [
            'disallowed but known type' => ['payload' => ['type' => 'work_log', 'id' => 1], 'expectedStatus' => 422],
            'unknown type' => ['payload' => ['type' => 'device', 'id' => 1], 'expectedStatus' => 422],
            'other user target' => ['payload' => ['type' => 'site', 'id' => 200], 'expectedStatus' => 404],
            'missing target' => ['payload' => ['type' => 'site', 'id' => 999999], 'expectedStatus' => 404],
            'zero target ID' => ['payload' => ['type' => 'site', 'id' => 0], 'expectedStatus' => 422],
        ];
    }

    protected function assertMorphValidationResponse(TestResponse $response, int $expectedStatus): void
    {
        $response->assertStatus($expectedStatus);
    }
}
