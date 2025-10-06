<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // E-Commerce Order Events
        $this->createOrderEvents($manager);

        // User Account Events
        $this->createUserEvents($manager);

        // Product Inventory Events
        $this->createInventoryEvents($manager);

        // Shopping Cart Events
        $this->createCartEvents($manager);

        $manager->flush();
    }

    private function createOrderEvents(ObjectManager $manager): void
    {
        $orders = [
            ['id' => 'order-550e8400', 'customer' => 'john@example.com', 'items' => 3],
            ['id' => 'order-6ba7b810', 'customer' => 'jane@example.com', 'items' => 2],
            ['id' => 'order-9e107d9d', 'customer' => 'bob@example.com', 'items' => 1],
        ];

        foreach ($orders as $orderData) {
            $version = 1;

            // Order Created
            $event = $this->createEvent(
                'OrderCreated',
                $orderData['id'],
                'Order',
                $version++,
                [
                    'orderId' => $orderData['id'],
                    'customerId' => str_replace('@', '-', $orderData['customer']),
                    'customerEmail' => $orderData['customer'],
                    'currency' => 'EUR',
                    'totalAmount' => 0,
                ],
                ['ipAddress' => '192.168.1.' . rand(1, 255), 'userAgent' => 'Mozilla/5.0']
            );
            $manager->persist($event);

            // Add items
            for ($i = 1; $i <= $orderData['items']; $i++) {
                $event = $this->createEvent(
                    'OrderItemAdded',
                    $orderData['id'],
                    'Order',
                    $version++,
                    [
                        'itemId' => 'product-' . rand(100, 999),
                        'productName' => $this->getRandomProduct(),
                        'quantity' => rand(1, 3),
                        'unitPrice' => rand(10, 500),
                        'subtotal' => rand(10, 1500),
                    ]
                );
                $manager->persist($event);
            }

            // Apply discount (50% chance)
            if (rand(0, 1)) {
                $event = $this->createEvent(
                    'DiscountApplied',
                    $orderData['id'],
                    'Order',
                    $version++,
                    [
                        'discountCode' => 'SAVE' . rand(10, 50),
                        'discountType' => 'percentage',
                        'discountValue' => rand(5, 25),
                        'discountAmount' => rand(10, 100),
                    ]
                );
                $manager->persist($event);
            }

            // Order Confirmed
            $event = $this->createEvent(
                'OrderConfirmed',
                $orderData['id'],
                'Order',
                $version++,
                [
                    'finalAmount' => rand(50, 2000),
                    'paymentMethod' => ['credit_card', 'paypal', 'bank_transfer'][rand(0, 2)],
                    'shippingAddress' => [
                        'street' => 'Main St ' . rand(1, 999),
                        'city' => ['Berlin', 'Munich', 'Hamburg'][rand(0, 2)],
                        'postalCode' => rand(10000, 99999),
                        'country' => 'DE',
                    ],
                ]
            );
            $manager->persist($event);

            // Order Shipped (70% chance)
            if (rand(0, 9) < 7) {
                $event = $this->createEvent(
                    'OrderShipped',
                    $orderData['id'],
                    'Order',
                    $version++,
                    [
                        'trackingNumber' => 'DHL' . rand(100000, 999999),
                        'carrier' => 'DHL',
                        'estimatedDelivery' => (new \DateTimeImmutable('+3 days'))->format('Y-m-d'),
                    ]
                );
                $manager->persist($event);
            }
        }
    }

    private function createUserEvents(ObjectManager $manager): void
    {
        $users = [
            ['id' => 'user-alice', 'email' => 'alice@example.com', 'username' => 'alice'],
            ['id' => 'user-bob', 'email' => 'bob@example.com', 'username' => 'bob'],
            ['id' => 'user-charlie', 'email' => 'charlie@example.com', 'username' => 'charlie'],
            ['id' => 'user-diana', 'email' => 'diana@example.com', 'username' => 'diana'],
        ];

        foreach ($users as $userData) {
            $version = 1;

            // User Registered
            $event = $this->createEvent(
                'UserRegistered',
                $userData['id'],
                'User',
                $version++,
                [
                    'email' => $userData['email'],
                    'username' => $userData['username'],
                    'registrationMethod' => ['email', 'google', 'facebook'][rand(0, 2)],
                ]
            );
            $manager->persist($event);

            // Email Verified (80% chance)
            if (rand(0, 9) < 8) {
                $event = $this->createEvent(
                    'EmailVerified',
                    $userData['id'],
                    'User',
                    $version++,
                    ['verifiedAt' => (new \DateTimeImmutable('-' . rand(1, 30) . ' days'))->format('c')]
                );
                $manager->persist($event);
            }

            // Profile Updated (60% chance)
            if (rand(0, 9) < 6) {
                $event = $this->createEvent(
                    'ProfileUpdated',
                    $userData['id'],
                    'User',
                    $version++,
                    [
                        'firstName' => ucfirst($userData['username']),
                        'lastName' => ['Smith', 'Johnson', 'Williams', 'Brown'][rand(0, 3)],
                        'phoneNumber' => '+49' . rand(1000000000, 9999999999),
                    ]
                );
                $manager->persist($event);
            }

            // Password Changed (30% chance)
            if (rand(0, 9) < 3) {
                $event = $this->createEvent(
                    'PasswordChanged',
                    $userData['id'],
                    'User',
                    $version++,
                    ['changedAt' => (new \DateTimeImmutable('-' . rand(1, 60) . ' days'))->format('c')],
                    ['reason' => 'user_requested']
                );
                $manager->persist($event);
            }
        }
    }

    private function createInventoryEvents(ObjectManager $manager): void
    {
        $products = [
            'product-789' => 'MacBook Pro 16"',
            'product-790' => 'USB-C Cable',
            'product-791' => 'Wireless Mouse',
            'product-792' => 'Mechanical Keyboard',
            'product-793' => '4K Monitor',
        ];

        foreach ($products as $productId => $productName) {
            $version = 1;

            // Stock Added
            $event = $this->createEvent(
                'StockAdded',
                $productId,
                'Product',
                $version++,
                [
                    'quantity' => rand(50, 200),
                    'location' => ['warehouse-A', 'warehouse-B', 'warehouse-C'][rand(0, 2)],
                    'reason' => 'initial_stock',
                ]
            );
            $manager->persist($event);

            // Multiple stock operations
            $operations = rand(2, 5);
            for ($i = 0; $i < $operations; $i++) {
                $operationType = ['StockReserved', 'StockShipped', 'StockReturned'][rand(0, 2)];

                $event = $this->createEvent(
                    $operationType,
                    $productId,
                    'Product',
                    $version++,
                    [
                        'quantity' => rand(1, 10),
                        'reservationId' => 'res-' . rand(1000, 9999),
                        'orderId' => 'order-' . rand(100000, 999999),
                    ]
                );
                $manager->persist($event);
            }
        }
    }

    private function createCartEvents(ObjectManager $manager): void
    {
        $carts = [
            'cart-001' => 'user-alice',
            'cart-002' => 'user-bob',
            'cart-003' => 'user-charlie',
        ];

        foreach ($carts as $cartId => $userId) {
            $version = 1;

            // Cart Created
            $event = $this->createEvent(
                'CartCreated',
                $cartId,
                'ShoppingCart',
                $version++,
                ['userId' => $userId, 'sessionId' => 'sess-' . rand(10000, 99999)]
            );
            $manager->persist($event);

            // Items added
            $itemCount = rand(1, 4);
            for ($i = 0; $i < $itemCount; $i++) {
                $event = $this->createEvent(
                    'CartItemAdded',
                    $cartId,
                    'ShoppingCart',
                    $version++,
                    [
                        'productId' => 'product-' . rand(789, 793),
                        'productName' => $this->getRandomProduct(),
                        'quantity' => rand(1, 3),
                        'price' => rand(10, 500),
                    ]
                );
                $manager->persist($event);
            }

            // Cart abandoned or converted (50/50)
            if (rand(0, 1)) {
                $event = $this->createEvent(
                    'CartAbandoned',
                    $cartId,
                    'ShoppingCart',
                    $version++,
                    ['reason' => 'timeout', 'abandonedAt' => (new \DateTimeImmutable('-' . rand(1, 7) . ' days'))->format('c')]
                );
            } else {
                $event = $this->createEvent(
                    'CartConvertedToOrder',
                    $cartId,
                    'ShoppingCart',
                    $version++,
                    ['orderId' => 'order-' . rand(100000, 999999)]
                );
            }
            $manager->persist($event);
        }
    }

    private function createEvent(
        string $eventType,
        string $aggregateId,
        string $aggregateType,
        int $version,
        array $payload,
        ?array $metadata = null
    ): Event {
        $event = new Event();
        $event->setEventType($eventType);
        $event->setAggregateId($aggregateId);
        $event->setAggregateType($aggregateType);
        $event->setVersion($version);
        $event->setPayload($payload);

        if ($metadata !== null) {
            $event->setMetadata($metadata);
        }

        // Randomize occurred_at to simulate historical data
        $daysAgo = rand(0, 90);
        $event->setOccurredAt(new \DateTimeImmutable('-' . $daysAgo . ' days'));

        return $event;
    }

    private function getRandomProduct(): string
    {
        $products = [
            'MacBook Pro 16"',
            'USB-C Cable',
            'Wireless Mouse',
            'Mechanical Keyboard',
            '4K Monitor',
            'External SSD 1TB',
            'Webcam HD',
            'Headphones',
            'Standing Desk',
            'Ergonomic Chair',
        ];

        return $products[array_rand($products)];
    }
}
