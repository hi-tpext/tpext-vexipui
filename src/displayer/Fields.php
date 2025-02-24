<?php

namespace tpext\builder\displayer;

use think\Model;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Table;
use tpext\builder\form\FieldsContent as FormFileds;
use tpext\builder\table\FieldsContent as TableFileds;
use tpext\builder\table\TColumn;

class Fields extends Field
{
    protected $view = 'fields';

    protected $input = false;

    protected $isFieldsGroup = true;

    protected $data = [];

    protected $gap = [8, 0];

    /**
     * Undocumented variable
     *
     * @var Form|Search|Table
     */
    protected $widget;

    /**
     * Undocumented variable
     *
     * @var FormFileds|TableFileds
     */
    protected $__fields_content__;

    public function created($type = '')
    {
        parent::created($type);

        if ($this->getWrapper() instanceof TColumn) {
            $this->widget = $this->getWrapper()->getTable();
        } else {
            $this->widget = $this->getWrapper()->getForm();
        }

        $this->__fields_content__ = $this->widget->createFields();

        if (empty($this->name) || preg_match('/^\W+$/', $this->name)) {
            $this->name = '__fields' . mt_rand(100, 999);
            $this->getWrapper()->setName($this->name);
        }
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
            $fields[0]($this->widget);
        }

        $this->widget->fieldsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FormFileds|TableFileds
     */
    public function getContent()
    {
        return $this->__fields_content__;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @return $this
     */
    public function value($val)
    {
        return $this->fill($val, true);
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->__fields_content__->extKey($val);
        return parent::extKey($val);
    }

    /**
     * 栅格间隔，可以传入 [horizontal，vertical] 的数组
     *
     * @param array $val [8, 0]
     * @return $this
     */
    public function gap($val)
    {
        $this->gap = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @param boolean $overWrite
     * @return $this
     */
    public function fill($data = [], $overWrite = false)
    {
        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        if (
            !empty($this->name) && isset($data[$this->name]) &&
            (is_array($data[$this->name]) || $data[$this->name] instanceof \ArrayAccess)
        ) {
            $fieldData = $data[$this->name];

            if (is_object($fieldData) && method_exists($fieldData, 'toArray')) {
                $fieldData = $fieldData->toArray();
            }

            if ($fieldData &&  (is_array($fieldData) || $fieldData instanceof \ArrayAccess)) {
                if (!$this->data) {
                    $this->data = [];
                }
                if (is_array($this->data) || $this->data instanceof \ArrayAccess) {
                    $this->data = array_merge($this->data, $fieldData);
                }
            }
        } else {
            $this->data = $data;
        }

        $this->__fields_content__->fill($this->data);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearScript()
    {
        $this->__fields_content__->clearScript();
        return parent::clearScript();
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        $this->__fields_content__->readonly($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function renderValue()
    {
        return ['__fields__'];
    }

    public function beforRender()
    {
        $this->getWrapper()->addClass('fields-wrapper');
        $this->__fields_content__->beforRender();
        parent::beforRender();
        return $this;
    }

    public function customVars()
    {
        return [
            'fieldsContent' => $this->__fields_content__,
            'gap' => $this->gap,
        ];
    }
}
