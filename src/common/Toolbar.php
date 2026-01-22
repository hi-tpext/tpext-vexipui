<?php

namespace tpext\builder\common;

use tpext\think\View;
use tpext\builder\toolbar\Bar;
use tpext\builder\table\TEmpty;
use tpext\builder\traits\HasDom;
use tpext\builder\toolbar\BWrapper;
use tpext\builder\inface\Renderable;

class Toolbar extends BWrapper implements Renderable
{
    use HasDom;

    protected $view = '';

    /**
     * Undocumented variable
     *
     * @var Bar[] 
     */
    protected $elms = [];

    /**
     * 当前元素
     *
     * @var Bar
     */
    protected $__elm__;

    protected $extKey = '';

    protected $elmsRight = [];

    protected $elmsLeft = [];

    protected $lockForExporting = false;

    /**
     * Undocumented variable
     *
     * @var TEmpty
     */
    protected $tEmpty = null;

    /**
     * Undocumented function
     *
     * @param bool $val
     * @return $this
     */
    public function lockForExporting($val = true)
    {
        $this->lockForExporting = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->tEmpty = new TEmpty;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->extKey = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Bar
     */
    public function getCurrent()
    {
        return $this->__elm__;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getElms()
    {
        return $this->elms;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->elms);
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clear()
    {
        $this->__elm__ = null;
        $this->elms = [];
        $this->elmsRight = [];
        $this->elmsLeft = [];

        return $this;
    }

    /**
     * 以下为代理方法，当前[$__elm__]生效
     *
     */

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function pullRight($val = true)
    {
        if ($this->__elm__) {
            $this->__elm__->pullRight($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function barPullRight($val = true)
    {
        if ($this->__elm__) {
            $this->__elm__->pullRight($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function barUseLayer($val, $size = [])
    {
        if ($this->__elm__) {
            $this->__elm__->useLayer($val, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function barLayerSize($size = [])
    {
        if ($this->__elm__) {
            $this->__elm__->useLayer(true, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barLabel($val)
    {
        if ($this->__elm__) {
            $this->__elm__->label($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barIcon($val)
    {
        if ($this->__elm__) {
            $this->__elm__->icon($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barHref($val)
    {
        if ($this->__elm__) {
            $this->__elm__->href($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barName($val)
    {
        if ($this->__elm__) {
            $this->__elm__->name($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function barClass($val)
    {
        if ($this->__elm__) {
            $this->__elm__->class($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAttr($val)
    {
        if ($this->__elm__) {
            $this->__elm__->attr($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barStyle($val)
    {
        if ($this->__elm__) {
            $this->__elm__->style($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barType($val)
    {
        if ($this->__elm__) {
            $this->__elm__->type($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddClass($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addClass($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddAttr($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addAttr($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddStyle($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addStyle($val);
        }
        return $this;
    }

    /**
     * 按钮点击时执行的脚本
     *
     * @param string $script
     * @return $this
     */
    public function barOnClick($script)
    {
        if ($this->__elm__) {
            $this->__elm__->onClick($script);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param mixed $arguments
     * 
     * @return mixed
     */
    protected function createBar($name, $arguments = [])
    {
        $this->__elm__ = BWrapper::makeBar($name, $arguments);
        $this->elms[] = $this->__elm__;
        return $this->__elm__;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->elmsLeft = $this->elmsRight = [];

        foreach ($this->elms as $elm) {

            if ($this->extKey) {
                $elm->extKey($this->extKey);
            }

            if ($elm->isPullRight()) {
                $this->elmsRight[] = $elm;
            } else {
                $this->elmsLeft[] = $elm;
            }

            $elm->beforRender();
        }

        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'toolbar.html';

        $viewshow = new View($template);

        $vars = [
            'elms' => $this->elms,
            'elmsLeft' => $this->elmsLeft,
            'elmsRight' => $this->elmsRight,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        if ($this->lockForExporting) {
            return $this->tEmpty;
        }

        $count = count($arguments);

        if ($count > 0 && self::isBar($name)) {

            $bar = $this->createBar($name, $arguments);

            return $bar;
        }

        throw new \InvalidArgumentException(__blang('builder_invalid_argument_exception') . ' : ' . $name);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function destroy()
    {
        $this->elms = null;
    }
}
