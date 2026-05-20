<?php

namespace App\Actions\Workers\Secretary;

use App\Models\Test;

/**
 * Action to fetch the duration (time) of a test by id. Returns the
 * numeric time in minutes to be used by appointment scheduling logic.
 *
 * Called from: `DatesController::ajaxTest()` for test time validation.
 */
class GetTestConsultationAction
{
    /**
    * @param int $testId
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
