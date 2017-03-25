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

        self::assertEquals(21, $rows[0]->getCell('age')->getValue());
        self::assertEquals('John', $rows[0]->getCell('name')->getValue());

        self::assertEquals(32, $rows[1]->getCell('age')->getValue());
        self::assertEquals('Bill', $rows[1]->getCell('name')->getValue());
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
+-----------+-----+--------+
| Catherine | 22  | female |
+-----------+-----+--------+
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
+-----------+-----+--------+
| Catherine |     | female |
+-----------+-----+--------+
| Johnathan | 44  |        |
+-----------+-----+--------+
EOD;

        $this->assertEquals($expected, $result);
    }
}
