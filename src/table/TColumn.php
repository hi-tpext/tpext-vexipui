<?php

namespace tpext\builder\table;

use tpext\builder\common\Table;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;
use tpext\builder\inface\Renderable;

class TColumn extends TWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    /**
     * Undocumented variable
     *
     * @var Table
     */
    protected $table;

    protected $colAttr = [
        'sortable' => false,
        'hidden' => false,
        'width' => 0,
        'min-width' => 0,
        'align' => '',
        'header-align' => '',
    ];

    public function __construct($name, $label = '', $colSize = 12)
    {
        $this->name = trim($name);
        $this->label = $label;
        $this->cloSize = $colSize;
        $this->class = 'column-field';
    }

    /**
     * Undocumented function
     *
     * @param Table $val
     * @return $this
     */
    public function setTable($val)
    {
        $this->table = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->displayer->fill($data);
        return $this;
    }

    public function getColSizeClass()
    {
        return '0';
    }

    /**
     * Undocumented function
     *
     * @param array $arr
     * @return $this
     */
    public function colAttr($arr)
    {
        $this->colAttr = array_merge($this->colAttr, $arr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getColAttr()
    {
        return $this->colAttr;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function sortable($val = true)
    {
        $this->colAttr['sortable'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function hidden($val = true)
    {
        $this->colAttr['hidden'] = $val;
        return $this;
    }


    /**
     * Undocumented function
     *
     * @param string|int $val 60/60px/10%
     * @return $this
     */
    public function width($val)
    {
        $this->colAttr['width'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int $val 60/60px/10%
     * @return $this
     */
    public function minWidth($val)
    {
        $this->colAttr['min-width'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val center/left/right
     * @return $this
     */
    public function align($val)
    {
        $this->colAttr['align'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val center/left/right
     * @return $this
     */
    public function headerAlign($val)
    {
        $this->colAttr['header-align'] = $val;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (static::isDisplayer($name)) {

            $class = static::$displayersMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }

    public function destroy()
    {
        $this->table = null;
        $this->displayer->destroy();
        $this->displayer = null;
    }
}
