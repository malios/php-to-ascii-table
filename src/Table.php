<?php declare(strict_types=1);

namespace AsciiTable;

use Ds\Map;
use Ds\Set;

class Table
{
    /**
     * @var Row[]
     */
    private $rows = [];

    /**
     * @var Set
     */
    private $visibleColumns;

    /**
     * @var Set
     */
    private $allColumns;

    /**
     * @var Map
     */
    private $biggestValues;

    public function __construct()
    {
        $this->visibleColumns = new Set();
        $this->allColumns = new Set();
        $this->biggestValues = new Map();
    }

    /**
     * Add single row to the table
     *
     * @param Row $row
     */
    public function addRow(Row $row)
    {
        foreach ($row->getCells() as $cell) {
            $columnName = $cell->getColumnName();

            $this->allColumns->add($columnName);

            $width = $cell->getWidth();
            if ($this->biggestValues->hasKey($columnName)) {
                if ($width > $this->biggestValues->get($columnName)) {
                    $this->biggestValues->put($columnName, $width);
                }
            } else {
                $this->biggestValues->put($columnName, $width);
            }
        }

        array_push($this->rows, $row);
    }

    /**
     * Get all rows in the table
     *
     * @return Row[]
     */
    public function getRows() : array
    {
        return $this->rows;
    }

    /**
     * Check if the table is empty.
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->rows);
    }

    /**
     * Set visible columns
     *
     * @param $columnNames
     */
    public function setVisibleColumns($columnNames)
    {
        $this->visibleColumns->clear();
        $this->visibleColumns->allocate(count($columnNames));
        $this->visibleColumns->add(...$columnNames);
    }

    /**
     * Get visible columns
     *
     * @return Set
     */
    public function getVisibleColumns() : Set
    {
        if ($this->visibleColumns->isEmpty()) {
            return $this->getAllColumns();
        }

        return $this->visibleColumns;
    }

    /**
     * Get all columns in the table
     *
     * @return Set
     */
    public function getAllColumns() : Set
    {
        return $this->allColumns;
    }

    /**
     * Get the width of a column by name
     *
     * @param string $columnName
     * @return int
     */
    public function getColumnWidth(string $columnName) : int
    {
        $width = 0;
        if ($this->biggestValues->hasKey($columnName)) {
            $width = $this->biggestValues->get($columnName);
        }

        $visibleColumns = $this->getVisibleColumns();
        if ($visibleColumns->contains($columnName) && mb_strlen($columnName) > $width) {
            $width = mb_strlen($columnName);
        }

        return $width;
    }
}
