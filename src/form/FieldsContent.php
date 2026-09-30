<?php

namespace tpext\builder\form;

use think\Model;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Table;
use tpext\builder\search\SRow;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\think\View;
use tpext\builder\inface\ReleaseAble;
use tpext\builder\traits\HasDestroyOnce;

class FieldsContent extends FWrapper implements Renderable, ReleaseAble
{
    use HasDestroyOnce;

    protected $view = 'fieldscontent';
    protected $rows = [];
    protected $data = [];
    protected $readonly = false;
    protected $hasWrapper = true;

    /**
     * Undocumented variable
     *
     * @var Form|Search|Table
     */
    protected $form;

    /**
     * Undocumented function
     * 
     * @param mixed $val
     * @return $this
     */
    public function hasWrapper($val = true)
    {
        $this->hasWrapper = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->fill($this->data);
            if (!($row instanceof FRow)) {
                $row->beforRender();
                continue;
            }

            $row->beforRender();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param FRow|SRow $row
     * @return $this
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FRow[]|SRow[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @param Form|Search $val
     * @return $this
     */
    public function setForm($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Form|Search
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$fields
     * @return $this
     */
    public function with(...$fields)
    {
        if (count($fields) && $fields[0] instanceof \Closure) {
            $fields[0]($this->form);
        }

        $this->form->fieldsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->data = $data;
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
        foreach ($this->rows as $row) {
            $row->getDisplayer()->readonly($val);
        }
        $this->readonly = $val;
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
        foreach ($this->rows as $row) {
            if (!($row instanceof FRow)) {
                continue;
            }

            $row->getDisplayer()->extKey($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearScript()
    {
        foreach ($this->rows as $row) {
            if (!($row instanceof FRow)) {
                continue;
            }

            $row->getDisplayer()->clearScript();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|Model
     */
    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'form' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        $vars = [
            'rows' => $this->rows,
            'hasWrapper' => $this->hasWrapper,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->form, $name], $arguments);
    }

    public function destroy()
    {
        // 已销毁直接返回：同一组件可被多归属路径重复触达（契约见 traits\HasDestroyOnce）
        if ($this->__destroyed__) {
            return;
        }
        foreach ($this->rows as $row) {
            if ($row instanceof ReleaseAble) {
                $row->destroy();
            }
        }
        // 数组属性复位为空数组（保持类型恒定，二次 destroy 自然幂等）
        $this->rows = [];
        $this->__destroyed__ = true;
    }
}
