<?php declare(strict_types=1);

namespace AsciiTable;

use AsciiTable\Exception\BuilderException;
use Ds\Map;

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

        $columnWidths = new Map();
        $lineSeparator = self::CHAR_CORNER_SEPARATOR;
        $header = PHP_EOL . self::CHAR_CELL_SEPARATOR;

        // render header and cache column widths for faster access
        foreach ($visibleColumns as $columnName) {
            $width = $this->table->getColumnWidth($columnName);
            $columnWidths->put($columnName, $width);

            $lineSeparator .= str_repeat(self::CHAR_LINE_SEPARATOR, ($width + 2)) . self::CHAR_CORNER_SEPARATOR;

            $header .= $this->getCellContent($columnName, strlen($columnName), $width)
                    . self::CHAR_CELL_SEPARATOR;
        }

        // render rows
        $result = $lineSeparator . $header . PHP_EOL . $lineSeparator;
        $rows = $this->table->getRows();
        foreach ($rows as $row) {
            $result .= PHP_EOL;

            $currentLine = $this->renderRow($row, $visibleColumns, $columnWidths);

            $result .= $currentLine . PHP_EOL . $lineSeparator;
        }

        return $result;
    }

    /**
     * Render single row and return string
     *
     * @param Row $row
     * @param $visibleColumns
     * @param Map $columnWidths
     * @return string
     */
    private function renderRow(Row $row, $visibleColumns, Map $columnWidths)
    {
        $line = self::CHAR_CELL_SEPARATOR;

        // render cells of the row
        foreach ($visibleColumns as $columnName) {
            $colWidth = $columnWidths->get($columnName);
            if ($row->hasCell($columnName)) {
                $cell = $row->getCell($columnName);

                $currentCell = $this->getCellContent($cell->getValue(), $cell->getWidth(), $colWidth);
            } else {
                $currentCell = $this->getCellContent('', 0, $colWidth);
            }

            $line .= $currentCell . self::CHAR_CELL_SEPARATOR;
        }

        return $line;
    }

    /**
     * Get cell content with left and right padding depending on the column width
     *
     * @param $input
     * @param int $inputLength
     * @param int $colWidth
     * @return string
     */
    private function getCellContent($input, int $inputLength, int $colWidth) : string
    {
        $content = self::CHAR_CELL_PADDING . (string) $input
                . str_repeat(self::CHAR_CELL_PADDING, ($colWidth -$inputLength + 1));

        return $content;
    }
}
