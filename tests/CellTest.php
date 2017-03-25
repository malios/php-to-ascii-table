<?php

namespace Test;

use AsciiTable\Cell;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    public function testCellWithDifferentTypesOfValues()
    {
        $cell = new Cell('age', '21');
        $this->assertEquals('21', $cell->getValue());
        $this->assertEquals(2, $cell->getWidth());

        $cell->setValue(123);
        $this->assertEquals(123, $cell->getValue());
        $this->assertEquals(3, $cell->getWidth());

        $ageObject = new class (2008) {
            private $year;

            function __construct(int $year)
            {
                $this->year = $year;
            }

            function __toString()
            {
                return strval(2017 - $this->year);
            }
        };

        $cell->setValue($ageObject);
        $this->assertEquals(1, $cell->getWidth());
    }

}
