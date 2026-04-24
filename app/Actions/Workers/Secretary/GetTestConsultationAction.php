<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Test;

class GetTestConsultationAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(int $testId): array
    {
        $testTime = Test::query()->whereKey($testId)->value('time');

        if ($testTime === null) {
            return [
                'status' => 'error',
                'message' => 'Test no trobat',
                'data' => [],
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Test trobat',
            'data' => [
                'number' => (int) $testTime,
            ],
        ];
    }
}
