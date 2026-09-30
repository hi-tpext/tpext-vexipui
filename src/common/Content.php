<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\think\View;
use tpext\builder\inface\ReleaseAble;
use tpext\builder\traits\HasDestroyOnce;

class Content extends Widget implements Renderable, ReleaseAble
{
    use HasDestroyOnce;

    /**
     * Undocumented variable
     *
     * @var View
     */
    protected $content;

    protected $contentRaw = '';

    protected $partial = false;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function partial($val = true)
    {
        $this->partial = $val;
        return $this;
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
        $this->content = new View($template);

        $this->content->assign($vars);
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
        $this->content = new View($content);

        $this->content->assign($vars)->isContent(true);

        if (empty($vars)) {
            $this->contentRaw = $content;//如果没有变量，那么就不解析模板
        }

        return $this;
    }

    public function beforRender()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        if ($this->partial) {
            return $this->content;
        }

        if ($this->contentRaw) {
            return $this->contentRaw;
        }

        return $this->content->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function destroy()
    {
        // 已销毁直接返回：同一组件可被多归属路径重复触达（契约见 traits\HasDestroyOnce）
        if ($this->__destroyed__) {
            return;
        }
        $this->content = null;
        $this->__destroyed__ = true;
    }
}
