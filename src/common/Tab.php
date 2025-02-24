<?php

namespace tpext\builder\common;

use think\Model;
use tpext\builder\form\FieldsContent;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;

class Tab extends Widget implements Renderable
{
    use HasDom;

    protected $view = 'tab';

    protected $rows = [];

    protected $active = '';

    protected $id = '';

    protected $partial = false;

    protected $vertical = false;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $__fields__ = [];

    protected $content;

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
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return Row
     */
    public function add($label, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = (count($this->rows) + 1);
        }
        $name = 'tab_' . $name;
        if (empty($this->active) && count($this->rows) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $row = Row::make();

        $this->rows[$name] = ['name' => $name, 'content' => $row, 'label' => $label, 'is_fields' => false];
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Form
     */
    public function form($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->form($size);
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Table
     */
    public function table($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->table($size);
    }

    /**
     * Undocumented function
     *
     * @return Content
     */
    public function content($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->content($size);
    }

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
     * @param boolean $val
     * @return $this
     */
    public function vertical($val = true)
    {
        $this->vertical = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return FieldsContent
     */
    public function addFieldsContent($label, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = (count($this->rows) + 1);
        }

        $name = 'tab_' . $name;

        if (empty($this->active) && count($this->rows) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $content = new FieldsContent();
        $this->__fields__[] = $content;

        $this->rows[$name] = ['name' => $name, 'content' => $content, 'label' => $label, 'is_fields' => true];

        return $content;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @return $this
     */
    public function fill($data = [])
    {
        foreach ($this->__fields__ as $content) {
            $content->fill($data);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        foreach ($this->__fields__ as $content) {
            $content->readonly($val);
        }
        return $this;
    }

    public function isFieldsGroup()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function active($val)
    {
        $names = array_keys($this->rows);

        if (in_array($val, $names)) {
            $this->active = $val;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row['content']->beforRender();
        }

        $this->tabScript();

        return $this;
    }

    protected function tabScript()
    {
        $tabId = $this->getId();
        $active = $this->active;
        $position = $this->vertical ? 'left' : 'top';

        $script = <<<EOT

    const {$tabId}Op = ref({
        'active' : '{$active}',
        'placement' : '{$position}',
        'card' : true,
    });

EOT;
        Builder::getInstance()->addSetupScript($script);

        Builder::getInstance()->addVueToken([
            "{$tabId}Op",
        ]);
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . $this->view . '.html';

        $vars = [
            'rows' => $this->rows,
            'id' => $this->getId(),
            'class' => $this->class . ($this->vertical ? ' tabs-vertical' : ' tabs-horizontal'),
            'attr' => $this->getAttrWithStyle(),
            'contentAttr' => $this->vertical ? 'style="min-height:' . (count($this->rows) * 40 + 1) . 'px"' : '',
        ];

        $viewshow = new View($template);

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function destroy()
    {
        $this->rows = null;
    }
}
