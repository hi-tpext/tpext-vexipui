<?php

namespace tpext\builder\toolbar;

class Html extends Bar
{
    protected $view = 'html';

    public function __construct($html)
    {
        $this->label = $html;
        $this->name = 'html' . mt_rand(100, 999);
    }

     /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|\think\Model $data
     * @return $this
     */
    public function parseUrl()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function parseMapClass()
    {
        return $this;
    }
}
