<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\think\View;

class Split extends Column
{
    protected $id = '';

    protected $ratio = 0.1;

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = 'split' . mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->class = 'split-panel';
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param mixed $ratio 0.1~0.9
     * @return $this
     */
    public function ratio($ratio)
    {
        $this->ratio = $ratio;
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
        if (count($this->elms) == 2) {
            throw new \LengthException('Split column can only have two widgets');
        }
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
        if (count($this->elms) == 2) {
            throw new \LengthException('Split column can only have two widgets');
        }
        $this->elms[] = $rendable;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $splitId = $this->getId();

        $ratio = $this->ratio * 100;
        $script = <<<EOT

    const {$splitId}Open = ref(true);
    const {$splitId}OneWidth = computed({
        get() {
            return {$splitId}Open.value ? {$ratio} + '%' : '0';
        }
    });
    const {$splitId}TwoWidth = computed({
        get() {
            return {$splitId}Open.value ? (100 - {$ratio} -1) + '%' : '100%';
        }
    });
    const {$splitId}ActionTitle = computed({
        get() {
            return {$splitId}Open.value ? __blang.bilder_action_close_left_tree : __blang.bilder_action_open_left_tree;
        }
    });

EOT;
        Builder::getInstance()->addSetupScript($script);
        Builder::getInstance()->addVueToken([
            "{$splitId}Open",
            "{$splitId}OneWidth",
            "{$splitId}TwoWidth",
            "{$splitId}ActionTitle",
        ]);

        return parent::beforRender();
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'split.html';

        $viewshow = new View($template);

        $vars = [
            'id' => $this->getId(),
            'one' => $this->elms[0] ?? null,
            'tow' => $this->elms[1] ?? null,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        return $viewshow->assign($vars)->getContent();
    }
}
