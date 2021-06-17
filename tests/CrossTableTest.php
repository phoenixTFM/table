<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Table\CrossTable\CrossTable;

final class CrossTableTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->assertEquals(
            [],
            CrossTable::addOrCreate(
                [],
                []
            )
        );
        $this->assertEquals(
            [],
            CrossTable::addOrCreate(
                [],
                [1,2,3,4,5]
            )
        );
    }

    public function testOnlyDifferentItems(): void
    {
        $items = [
            0 => [
                'GROUP_ID' => 'A',
                100 => '111',
                200 => '222'
            ],
            1 => [
                'GROUP_ID' => 'A',
                100 => '111-111',
                200 => '222-222'
            ],
            2 => [
                'GROUP_ID' => 'A',
                100 => '111-111-111',
                200 => '222-222-222'
            ]
        ];

        $expected = [
            0 => [
                'A' => [
                    100 => '111',
                    200 => '222',
                    'ITEM_ID' => 0,
                    'GROUP_ID' => 'A'
                ]
            ],
            1 => [
                'A' => [
                    100 => '111-111',
                    200 => '222-222',
                    'ITEM_ID' => 1,
                    'GROUP_ID' => 'A'
                ]
            ],
            2 => [
                'A' => [
                    100 => '111-111-111',
                    200 => '222-222-222',
                    'ITEM_ID' => 2,
                    'GROUP_ID' => 'A'
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            CrossTable::addOrCreate($items, [])
        );
    }

    public function testSameValueOnDifferentGroupIDs(): void
    {
        $items = [
            0 => [
                'GROUP_ID' => 'A',
                100 => 'Apple',
                200 => 'Banana'
            ],
            1 => [
                'GROUP_ID' => 'B',
                100 => 'Apple',
                200 => 'Banana'
            ],
            2 => [
                'GROUP_ID' => 'C',
                100 => 'Apple',
                200 => 'Banana'
            ]
        ];

        $expected = [
            0 => [
                'A' => [
                    100 => 'Apple',
                    200 => 'Banana',
                    'ITEM_ID' => 0,
                ],
                'B' => [
                    100 => 'Apple',
                    200 => 'Banana',
                    'ITEM_ID' => 1,
                ],
                'C' => [
                    100 => 'Apple',
                    200 => 'Banana',
                    'ITEM_ID' => 2,
                ],
            ]
        ];

        $this->assertEquals(
            $expected,
            CrossTable::addOrCreate($items, ['ITEM_ID', 'GROUP_ID'])
        );
    }
}