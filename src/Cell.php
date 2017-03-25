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
     * @var mixed
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
     * @return mixed
     */
    public function getValue()
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
        $this->width = strlen(strval($value));
        $this->value = $value;
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
