<?php

namespace tpext\builder\common;

use tpext\think\View;
use tpext\builder\tree\Tree;
use tpext\builder\traits\HasDom;
use tpext\builder\inface\Renderable;
use tpext\builder\inface\ReleaseAble;
use tpext\builder\traits\HasDestroyOnce;

class Row extends Widget implements Renderable, ReleaseAble
{
    use HasDom;
    use HasDestroyOnce;

    /**
     * Undocumented variable
     *
     * @var Column[] 
     */
    protected $cols = [];

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
     * @param integer|string $size
     * @return Column
     */
    public function column($size = 12)
    {
        $col = self::makeWidget('Column', $size);
        $this->cols[] = $col;
        return $col;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * 获取一个工具栏
     *
     * @param integer|string $size
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->toolbar();
    }

    /**
     * 默认获取一个ZTree树
     *
     * @param integer|string $size
     * @return Tree
     */
    public function tree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * 获取一个Tree树
     * @deprecated tree()
     * @param integer|string $size
     * @return Tree
     */
    public function zTree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * 获取一个Tree树
     * @deprecated tree()
     * @param integer|string $size
     * @return Tree
     */
    public function jsTree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Content
     */
    public function content($size = 12)
    {
        return $this->column($size)->content();
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function fetch($template = '', $vars = [], $size = 12)
    {
        $this->content($size)->fetch($template, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function display($content = '', $vars = [], $size = 12)
    {
        $this->content($size)->display($content, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Tab
     */
    public function tab($size = 12)
    {
        return $this->column($size)->tab();
    }

    /**
     * 获取一个分割面板
     * 
     * @param integer|string $size
     * @return Split
     */
    public function Split($size = 12)
    {
        return $this->column($size)->Split();
    }

    /**
     * 获取一Swiper
     *
     * @param integer|string $size col大小
     * @return Swiper
     */
    public function swiper($size = 12)
    {
        return $this->column($size)->swiper();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->cols as $col) {
            $col->beforRender();
        }

        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'row.html';

        $viewshow = new View($template);

        $vars = [
            'cols' => $this->cols,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function destroy()
    {
        // 已销毁直接返回：同一组件可被多归属路径重复触达（契约见 traits\HasDestroyOnce）
        if ($this->__destroyed__) {
            return;
        }
        foreach ($this->cols as $col) {
            $col->destroy();
        }

        // 数组属性复位为空数组（保持类型恒定，二次 destroy 自然幂等）
        $this->cols = [];
        $this->__destroyed__ = true;
    }
}
