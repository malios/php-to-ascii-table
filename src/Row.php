<?php declare(strict_types=1);

namespace AsciiTable;

use Ds\Map;

class Row
{
    /**
     * @var Map
     */
    private $cells;

    public function __construct()
    {
        $this->cells = new Map();
    }

    /**
     * Add single cell to the row
     *
     * @param Cell $cell
     */
    public function addCell(Cell $cell)
    {
        $this->cells->put($cell->getColumnName(), $cell);
    }

    /**
     * Add multiple cells to row
     *
     * @param Cell[] ...$cells
     */
    public function addCells(Cell ...$cells)
    {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }
    }

    /**
     * Get single cell by name
     *
     * @param $columnName
     * @return Cell
     */
    public function getCell($columnName) : Cell
    {
        return $this->cells->get($columnName);
    }

    /**
     * Check if the row has a cell cell for given column
     *
     * @param $columnName
     * @return bool
     */
    public function hasCell($columnName) : bool
    {
        return $this->cells->hasKey($columnName);
    }

    /**
     * Get all cells
     *
     * @return Map
     */
    public function getCells() : Map
    {
        return $this->cells;
    }
}
