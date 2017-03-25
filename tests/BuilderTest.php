<?php

namespace Test;

use AsciiTable\Builder;
use AsciiTable\Exception\BuilderException;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function testAddRows()
    {
        $builder = new Builder();
        $builder->createTable();
        $builder->addRow(['age' => 21, 'name' => 'John']);

        $person = new class implements \JsonSerializable {

            function jsonSerialize()
            {
                return [
                    'age' => 32,
                    'name' => 'Bill'
                ];
            }
        };

        $builder->addRow($person);

        $table = $builder->getTable();
        $rows = $table->getRows();

        $this->assertEquals(21, $rows[0]->getCell('age')->getValue());
        $this->assertEquals('John', $rows[0]->getCell('name')->getValue());

        $this->assertEquals(32, $rows[1]->getCell('age')->getValue());
        $this->assertEquals('Bill', $rows[1]->getCell('name')->getValue());
    }

    public function testAddRowWithoutCreatingTable()
    {
        $this->expectException(BuilderException::class);

        $builder = new Builder();
        $builder->addRow(['foo' => 'bar']);
    }

    public function testAddInvalidRow()
    {
        $this->expectException(BuilderException::class);

        $person = new class {
            public $name;
        };

        $person->name = 'Rick';

        $builder = new Builder();
        $builder->addRow($person);
    }

    public function testRenderEmptyTable()
    {
        $this->expectException(BuilderException::class);

        $builder = new Builder();
        $builder->createTable();
        $builder->renderTable();
    }

    public function testRenderWithoutCreatingTable()
    {
        $this->expectException(BuilderException::class);

        $builder = new Builder();
        $builder->renderTable();
    }


    public function testRender()
    {
        $builder = new Builder();
        $builder->createTable();
        $builder->addRows([
            [
                'name' => 'John',
                'age' => 23,
                'sex' => 'male'
            ],
            [
                'name' => 'Catherine',
                'age' => 22,
                'sex' => 'female'
            ],
            [
                'name' => 'Johnathan',
                'age' => 44,
                'sex' => 'male'
            ]
        ]);

        $result = $builder->renderTable();

        $expected = <<<EOD
+-----------+-----+--------+
| name      | age | sex    |
+-----------+-----+--------+
| John      | 23  | male   |
| Catherine | 22  | female |
| Johnathan | 44  | male   |
+-----------+-----+--------+
EOD;

        $this->assertEquals($expected, $result);
    }

    public function testRenderWithSomeEmptyCells()
    {
        $builder = new Builder();
        $builder->createTable();
        $builder->addRows([
            [
                'name' => 'John',
                'age' => 23,
                'sex' => 'male'
            ],
            [
                'name' => 'Catherine',
                'sex' => 'female'
            ],
            [
                'name' => 'Johnathan',
                'age' => 44,
            ]
        ]);

        $result = $builder->renderTable();

        $expected = <<<EOD
+-----------+-----+--------+
| name      | age | sex    |
+-----------+-----+--------+
| John      | 23  | male   |
| Catherine |     | female |
| Johnathan | 44  |        |
+-----------+-----+--------+
EOD;

        $this->assertEquals($expected, $result);
    }

    public function testShowsOnlyVisibleColumns()
    {
        $builder = new Builder();
        $builder->createTable();
        $builder->addRows([
            [
                'name' => 'John',
                'age' => 23,
                'sex' => 'male'
            ],
            [
                'name' => 'Catherine',
                'age' => 22,
                'sex' => 'female'
            ],
            [
                'name' => 'Johnathan',
                'age' => 44,
                'sex' => 'male'
            ]
        ]);

        $builder->showColumns(['name', 'age']);
        $result = $builder->renderTable();

        $expected = <<<EOD
+-----------+-----+
| name      | age |
+-----------+-----+
| John      | 23  |
| Catherine | 22  |
| Johnathan | 44  |
+-----------+-----+
EOD;
        $this->assertEquals($expected, $result);
    }

    public function testRenderTableWithFloatingPoint()
    {
        $builder = new Builder();
        $builder->createTable();
        $builder->addRows([
            [
                'Order No' => 'A0001',
                'Product Name' => 'Intel CPU',
                'Price' => 700.00,
                'Quantity' => 1
            ],
            [
                'Order No' => 'A0002',
                'Product Name' => 'Hard disk 10TB',
                'Price' => 500.00,
                'Quantity' => 2
            ],
            [
                'Order No' => 'A0003',
                'Product Name' => 'Dell Laptop',
                'Price' => 11600.00,
                'Quantity' => 8
            ],
            [
                'Order No' => 'A0004',
                'Product Name' => 'Intel CPU',
                'Price' => 5200.00,
                'Quantity' => 3
            ]
        ]);

        $builder->addRow([
            'Order No' => 'A0005',
            'Product Name' => 'A4Tech Mouse',
            'Price' => 100.00,
            'Quantity' => 10
        ]);

        $result = $builder->renderTable();

        $expected = <<<EOD
+----------+----------------+-----------+----------+
| Order No | Product Name   | Price     | Quantity |
+----------+----------------+-----------+----------+
| A0001    | Intel CPU      | 700.00    | 1        |
| A0002    | Hard disk 10TB | 500.00    | 2        |
| A0003    | Dell Laptop    | 11 600.00 | 8        |
| A0004    | Intel CPU      | 5 200.00  | 3        |
| A0005    | A4Tech Mouse   | 100.00    | 10       |
+----------+----------------+-----------+----------+
EOD;
        $this->assertEquals($expected, $result);
    }
}