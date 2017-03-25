<?php declare(strict_types=1);

namespace AsciiTable;

class Cell
{
    /**
     * The name of the column that the cell belongs to
     *
     * @var string
     */
    private $columnName;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $width = 0;

    public function __construct($columnName, $value = '')
    {
        $this->setColumnName($columnName);
        $this->setValue($value);
    }

    /**
     * Get value of the cell
     *
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Set value of the cell.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        if (is_float($value)) {
            $round = round($value);
            if (($value - $round) === (float)0) {
                $this->value = number_format($value, 2, '.', ' ');
            } else {
                $this->value = (string) $value;
            }
        } else {
            $this->value = (string) $value;
        }

        $this->width = strlen($this->value);
    }

    /**
     * Get the name of the column that the cell belongs to
     *
     * @return string
     */
    public function getColumnName() : string
    {
        return $this->columnName;
    }

    /**
     * Set the name of the column that the cell belongs to
     *
     * @param string $columnName
     */
    public function setColumnName(string $columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * Get the width (string length) of the cell
     *
     * @return int
     */
    public function getWidth() : int
    {
        return $this->width;
    }
}
