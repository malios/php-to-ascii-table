<?php declare(strict_types=1);

namespace AsciiTable;

class Cell implements CellInterface
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
     * {@inheritdoc}
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
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

        $this->width = mb_strwidth($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnName() : string
    {
        return $this->columnName;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnName(string $columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth() : int
    {
        return $this->width;
    }
}
