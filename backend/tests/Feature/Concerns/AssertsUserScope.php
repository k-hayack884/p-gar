<?php

namespace Tests\Feature\Concerns;

use Illuminate\Testing\TestResponse;

trait AssertsUserScope
{
    /**
     * Cross-user resources must be indistinguishable from absent resources.
     */
    protected function assertNotFoundForOtherUser(TestResponse $response): void
    {
        $response->assertStatus(404);
    }
}
