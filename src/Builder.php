<?php declare(strict_types=1);

namespace AsciiTable;

use AsciiTable\Exception\BuilderException;
use Ds\Set;

class Builder
{
    /**
     * @var string
     */
    const CHAR_CELL_SEPARATOR = '|';

    /**
     * @var string
     */
    const CHAR_LINE_SEPARATOR = '-';

    /**
     * @var string
     */
    const CHAR_CELL_PADDING = ' ';

    /**
     * @var string
     */
    const CHAR_CORNER_SEPARATOR = '+';

    /**
     * @var Table
     */
    private $table;

    /**
     * Create a table object to build
     *
     * @return void
     */
    public function createTable()
    {
        $this->table = new Table();
    }

    /**
     * Get the table
     *
     * @return Table
     */
    public function getTable() : Table
    {
        return $this->table;
    }

    /**
     * Add single row.
     * The value passed should be either an array or an JsonSerializable object
     *
     * @param array|\JsonSerializable $rowArrayOrObject
     * @throws BuilderException
     */
    public function addRow($rowArrayOrObject)
    {
        if ($this->table === null) throw new BuilderException('Table is not created');

        if (is_array($rowArrayOrObject)) {
            $rowArray = $rowArrayOrObject;
        } else if ($rowArrayOrObject instanceof \JsonSerializable) {
            $rowArray = $rowArrayOrObject->jsonSerialize();
        } else {
            throw new BuilderException(sprintf(
                'Row must be either an array or JsonSerializable, %s given instead',
                gettype($rowArrayOrObject)
            ));
        }

        $row = new Row();
        foreach ($rowArray as $columnName => $value) {
            $cell = new Cell($columnName, $value);
            $row->addCell($cell);
        }

        $this->table->addRow($row);
    }

    /**
     * Add multiple rows
     *
     * @param Row[] $rows
     * @return void
     */
    public function addRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    /**
     * Show only specific columns of the table
     *
     * @param array $columnNames
     * @return void
     * @throws BuilderException
     */
    public function showColumns(array $columnNames)
    {
        if ($this->table === null) throw new BuilderException('Table is not created');

        $this->table->setVisibleColumns($columnNames);
    }

    /**
     * Render table and return result string
     *
     * @return string
     * @throws BuilderException
     */
    public function renderTable() : string
    {
        if ($this->table === null) throw new BuilderException('Table is not created');

        if ($this->table->isEmpty()) throw new BuilderException('Cannot render empty table');

        $visibleColumns = $this->table->getVisibleColumns();

        // border for header and footer
        $borderParts = array_map(function ($columnName) {
            $width = $this->table->getColumnWidth($columnName);
            return str_repeat(self::CHAR_LINE_SEPARATOR, ($width + 2));
        }, $visibleColumns->toArray());

        $border = self::CHAR_CORNER_SEPARATOR
                . join(self::CHAR_CORNER_SEPARATOR, $borderParts)
                . self::CHAR_CORNER_SEPARATOR;

        $headerCells = array_map(function ($columnName) {
            return new Cell($columnName, $columnName);
        }, $visibleColumns->toArray());

        $headerRow = new Row();
        $headerRow->addCells(...$headerCells);
        $header = $this->renderRow($headerRow, $visibleColumns);

        $body = '';
        $rows = $this->table->getRows();
        $visibleColumns = $this->table->getVisibleColumns();
        foreach ($rows as $row) {
            $currentLine = $this->renderRow($row, $visibleColumns);
            $body .= $currentLine . PHP_EOL;
        }

        $tableAsString = $border . PHP_EOL . $header . PHP_EOL . $border . PHP_EOL . $body . $border;
        return $tableAsString;
    }

    /**
     * Render single row and return string
     *
     * @param Row $row
     * @param Set $columnNames
     * @return string
     */
    private function renderRow(Row $row, Set $columnNames)
    {
        $line = self::CHAR_CELL_SEPARATOR;

        // render cells of the row
        foreach ($columnNames as $columnName) {
            $colWidth = $this->table->getColumnWidth($columnName);
            if ($row->hasCell($columnName)) {
                $cell = $row->getCell($columnName);
                $currentCell = $this->renderCell($cell, $colWidth);
            } else {
                $currentCell = $this->renderCell(new Cell($columnName, ''), $colWidth);
            }

            $line .= $currentCell . self::CHAR_CELL_SEPARATOR;
        }

        return $line;
    }

    /**
     * Render cell content with left and right padding depending on the column width
     *
     * @param Cell $cell
     * @param int $colWidth
     * @return string
     */
    private function renderCell(Cell $cell, int $colWidth) : string
    {
        $content = self::CHAR_CELL_PADDING . (string) $cell->getValue()
                . str_repeat(self::CHAR_CELL_PADDING, ($colWidth - $cell->getWidth() + 1));

        return $content;
    }
}
