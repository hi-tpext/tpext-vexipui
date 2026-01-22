<?php

namespace tpext\builder\common;

use tpext\think\View;
use tpext\builder\tree\Tree;
use tpext\builder\common\Form;
use tpext\builder\common\Table;
use tpext\builder\traits\HasDom;
use tpext\builder\inface\Renderable;

class Column extends Widget implements Renderable
{
    use HasDom;

    /**
     * Undocumented variable
     *
     * @var int|string
     */
    public $size = 12;

    protected $elms = [];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->class = '';
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
    protected function createWidget($name, ...$arguments)
    {
        $widget = Widget::makeWidget($name, $arguments);
        $this->elms[] = $widget;
        return $widget;
    }

    /**
     * Undocumented function
     *
     * @param Renderable $rendable
     * @return $this
     */
    public function append($rendable)
    {
        $this->elms[] = $rendable;
        return $this;
    }

    /**
     * 获取一个form
     *
     * @return Form
     */

    public function form()
    {
        return $this->createWidget('Form');
    }

    /**
     * 获取一个表格
     *
     * @return Table
     */
    public function table()
    {
        return $this->createWidget('Table');
    }

    /**
     * 获取一个Toolbar
     *
     * @return Toolbar
     */
    public function toolbar()
    {
        return $this->createWidget('Toolbar');
    }

    /**
     * 获取一个Tree
     *
     * @return Tree
     */
    public function tree()
    {
        return $this->createWidget('Tree');
    }

    /**
     * 获取一个Tree
     * @deprecated tree()
     * @return Tree
     */
    public function zTree()
    {
        return $this->tree();
    }

    /**
     * 获取一个Tree
     * @deprecated tree()
     * @return Tree
     */
    public function jsTree()
    {
        return $this->tree();
    }

    /**
     * 获取一个自定义内容
     *
     * @return Content
     */
    public function content()
    {
        return $this->createWidget('Content');
    }

    /**
     * 获取一个 tab
     *
     * @return Tab
     */
    public function tab()
    {
        return $this->createWidget('Tab');
    }

    /**
     * 获取一新行
     *
     * @return Row
     */
    public function row()
    {
        return $this->createWidget('Row');
    }

    /**
     * 获取一个分割面板
     *
     * @return Split
     */
    public function Split()
    {
        return $this->createWidget('Split');
    }

    /**
     * 获取一Swiper
     *
     * @param integer|string $size col大小
     * @return Swiper
     */
    public function swiper()
    {
        return $this->createWidget('Swiper');
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
     * @return int|string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @return $this
     */
    public function fetch($template = '', $vars = [])
    {
        $this->content()->fetch($template, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @return $this
     */
    public function display($content = '', $vars = [])
    {
        $this->content()->display($content, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getSizeAttr()
    {
        return Widget::getSizeAdapter()->getColSizeAttrFromColClass($this->size);
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->elms as $elm) {
            if (!($elm instanceof Renderable)) {
                continue;
            }
            $elm->beforRender();
        }

        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'column.html';

        $viewshow = new View($template);

        $vars = [
            'elms' => $this->elms,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'sizeAttr' => $this->getSizeAttr(),
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        if (self::isWidget($name)) {

            $widget = $this->createWidget($name, $arguments);

            return $widget;
        }

        throw new \InvalidArgumentException(__blang('builder_invalid_argument_exception') . ' : ' . $name);
    }

    public function destroy()
    {
        foreach ($this->elms as $elm) {
            if (method_exists($elm, 'destroy')) {
                $elm->destroy();
            }
        }

        $this->elms = null;
    }
}
