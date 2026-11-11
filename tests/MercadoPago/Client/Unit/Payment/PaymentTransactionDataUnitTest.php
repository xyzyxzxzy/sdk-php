<?php

namespace MercadoPago\Tests\Client\Unit\Payment;

use MercadoPago\Resources\Payment;
use MercadoPago\Resources\Payment\PointOfInteraction;
use MercadoPago\Resources\Payment\TransactionData;
use MercadoPago\Serialization\Serializer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PaymentTransactionDataUnitTest extends TestCase
{
    #[DataProvider('subscriptionIdProvider')]
    public function testDeserializePaymentWithOptionalSubscriptionId(
        array $transaction_data,
        ?string $expected_subscription_id
    ): void {
        $filepath = __DIR__ . '/../../../Resources/Mocks/Response/Payment/payment_base.json';
        $response = json_decode(file_get_contents($filepath), true);
        $response['point_of_interaction'] = [
            'transaction_data' => ['transaction_id' => 'test-transaction'] + $transaction_data,
        ];

        $payment = Serializer::deserializeFromJson(Payment::class, $response);

        $this->assertSame($response['id'], $payment->id);
        $this->assertInstanceOf(PointOfInteraction::class, $payment->point_of_interaction);
        $this->assertInstanceOf(TransactionData::class, $payment->point_of_interaction->transaction_data);
        $this->assertSame('test-transaction', $payment->point_of_interaction->transaction_data->transaction_id);
        $this->assertSame($expected_subscription_id, $payment->point_of_interaction->transaction_data->subscription_id);
    }

    public static function subscriptionIdProvider(): array
    {
        return [
            'subscription ID present' => [['subscription_id' => 'test-subscription'], 'test-subscription'],
            'subscription ID absent' => [[], null],
            'subscription ID null' => [['subscription_id' => null], null],
        ];
    }
}
