<?php

namespace tpext\builder\search;

use tpext\builder\common\Search;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;
use tpext\builder\inface\Renderable;

class SRow extends SWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    protected $filter = '';

    /**
     * Undocumented variable
     *
     * @var Search
     */
    protected $form;

    public function __construct($name, $label = '', $colSize = 2, $filter = '')
    {
        $this->name = trim($name);
        $this->label = $label;
        $this->cloSize = $colSize;
        $this->filter = $filter;
    }

    /**
     * Undocumented function
     *
     * @param mixed $filter
     * @return $this
     */
    public function filter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter ?: 'eq';
    }

    /**
     * Undocumented function
     *
     * @param Search $val
     * @return $this
     */
    public function setForm($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Search
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->displayer->beforRender();
        return $this;
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
        $this->form = null;
        $this->displayer->destroy();
        $this->displayer = null;
    }
}
