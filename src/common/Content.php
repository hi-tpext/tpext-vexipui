<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\think\View;

class Content extends Widget implements Renderable
{
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
        $this->content = null;
    }
}
