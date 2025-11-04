<?php

namespace tpext\builder\displayer;

use tpext\think\View;

class Html extends Field
{
    protected $view = 'html';

    protected $input = false;

    protected $vBind = true; //是否通过v-html绑定值

    protected $content = null;

    public function __construct($html, $label = '')
    {
        $this->label = $label;
        $this->default = $html;
        $this->name = '__html' . mt_rand(100, 999);
    }

    public function created($type = '')
    {
        $this->getWrapper()->setName($this->name)->addStyle('min-height: 1px');
    }

    /**
     * Undocumented function
     * 
     * @param mixed $val
     * @return void
     */
    public function vBind($val = true)
    {
        $this->vBind = $val;
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
        return $this;
    }

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = parent::renderValue();
        if ($this->content) {
            $vars = ['__val__' => $value];
            $this->renderValue = $this->content->assign($vars)->getContent();
            $this->vBind = false;
        }

        return $this->renderValue;
    }

    public function customVars()
    {
        return [
            'vBind' => $this->vBind,
            'hasWrapper' => $this->label || $this->renderValue()
        ];
    }
}
