<?php

namespace tpext\builder\search;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasOptions;
use tpext\think\View;

class TabLink implements Renderable
{
    use HasDom;
    use HasOptions;

    protected $view = 'tab';

    protected $active = null;
    protected $id = '';
    protected $key = '';
    protected $searchId = '';

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = 'tab' . mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $id
     * @return $this
     */
    public function searchId($id)
    {
        $this->searchId = $id;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function key($val)
    {
        $this->key = $val;

        return $this;
    }

    /**
     * Undocumented function
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function active($val)
    {
        $this->active = $val;

        return $this;
    }

    public function getActive()
    {
        if (is_null($this->active) && count($this->options)) {
            $this->active = array_keys($this->options)[0] ?? '';
        }

        return $this->active;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (is_null($this->active) && count($this->options)) {
            $this->active = array_keys($this->options)[0];
        }

        $tabId = $this->getId();
        $serchId = $this->searchId;
        $active = $this->getActive();
        if ($active === '') {
            $active = '__empty_string__';
        }

        $script = <<<EOT

    const {$tabId}Op = ref({
        'active' : '{$active}',
        'placement' : 'top',
        'card' : true,
    });

    const {$tabId}Click = (val) => {
        if (val === '__empty_string__') {
            val = '';
        }
        {$serchId}Data['{$this->key}'] = val;
        {$serchId}Submit();
    };
    
EOT;
        Builder::getInstance()->addSetupScript($script);

        Builder::getInstance()->addVueToken([
            "{$tabId}Op",
            "{$tabId}Click",
        ]);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'table' . DIRECTORY_SEPARATOR . $this->view . '.html';
        $options = [];

        foreach ($this->options as $k => $v) {
            if ($k === '') {
                $k = '__empty_string__';
            }
            $options[$k] = $v;
        }

        $vars = [
            'options' => $options,
            'active' => $this->getActive(),
            'id' => $this->getId(),
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        $viewshow = new View($template);
        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }
}
