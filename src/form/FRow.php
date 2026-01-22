<?php

namespace tpext\builder\form;

use tpext\builder\common\Form;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;
use tpext\builder\inface\Renderable;

class FRow extends FWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    public function __construct($name, $label = '', $colSize = 12)
    {
        $this->name = trim($name);
        $this->label = $label;
        $this->cloSize = $colSize;
    }

    /**
     * Undocumented function
     *
     * @param Form $val
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
     * @return Form
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

        throw new \InvalidArgumentException(__blang('builder_invalid_argument_exception') . ' : ' . $name);
    }

    public function destroy()
    {
        $this->form = null;
        $this->displayer->destroy();
        $this->displayer = null;
    }
}
