<?php

namespace App\Services;

use Illuminate\Support\Arr;
use App\Models\User;

class MockPaymentService
{
    /**
     * Process a mock payment.
     *
     * @param User $user
     * @param int $amount
     * @param string $currency
     * @param array $options
     * @param string $forceStatus 'success', 'failed', 'pending', 'random'
     * @return array
     */
    public function processPayment(int $userId, int $subscriptionPlanId, int $amount, string $currency = 'INR', array $options = [], string $forceStatus): array
    {
        $status = $this->determineStatus($forceStatus);
        $transactionId = 'TEST_' . strtoupper(uniqid());

        return [
            'success' => $status === 'succeeded',
            'status' => $status,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => $currency,
            'message' => 'Payment ' . $status,
        ];
    }

    /**
     * Determine the payment status based on the forced status.
     *
     * @param string $forceStatus
     * @return string
     */
    protected function determineStatus(string $forceStatus): string
    {
        $validStatuses = ['succeeded', 'failed', 'pending'];

        if ($forceStatus === 'success') {
            return 'succeeded';
        }

        if (in_array($forceStatus, $validStatuses)) {
            return $forceStatus;
        }

        if ($forceStatus === 'random') {
            return Arr::random($validStatuses);
        }

        // Default to succeeded if invalid input, or handle error
        return 'succeeded';
    }
}
