<?php

namespace tpext\builder\displayer;

use think\Model;
use think\Collection;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\form\ItemsContent;

class Items extends Field
{
    protected $view = 'items';

    protected $input = false;

    protected $isFieldsGroup = true;

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Form|Search
     */
    protected $form;

    /**
     * Undocumented variable
     *
     * @var ItemsContent
     */
    protected $__items_content__;

    public function created($type = '')
    {
        parent::created($type);

        $this->form = $this->getWrapper()->getForm();
        $this->__items_content__ = $this->form->createItems();

        if (empty($this->name) || preg_match('/^\W+$/', $this->name)) {
            $this->name = '__items' . mt_rand(100, 999);
            $this->getWrapper()->setName($this->name);
        }

        $this->__items_content__->name($this->name);
    }

    /**
     * Undocumented function
     *
     * @param string $val a or a.b.c
     * @return $this
     */
    public function name($val)
    {
        parent::name($val);
        $this->__items_content__->name($val);
        return $this;
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

        $this->form->itemsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|Collection|\IteratorAggregate $data
     * @return $this
     */
    public function value($val)
    {
        return $this->fill($val);
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->__items_content__->extKey($val);
        return parent::extKey($val);
    }

    /**
     * Undocumented function
     *
     * @return ItemsContent
     */
    public function getContent()
    {
        return $this->__items_content__;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function actionRowText($val)
    {
        $this->__items_content__->actionRowText($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canDelete($val)
    {
        $this->__items_content__->canDelete($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canAdd($val)
    {
        $this->__items_content__->canAdd($val);
        return $this;
    }

     /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canRecover($val)
    {
        $this->__items_content__->canRecover($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function canNotAddOrDelete()
    {
        $this->__items_content__->canDelete(false);
        $this->__items_content__->canAdd(false);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess|Collection|\IteratorAggregate $data
     * @param boolean $overWrite
     * @return $this
     */
    public function fill($data = [], $overWrite = false)
    {
        if ($data instanceof Collection || $data instanceof \IteratorAggregate) {
            return $this->dataWithId($data, '', $overWrite);
        }

        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        if (!empty($this->name) && isset($data[$this->name])) {
            if (is_array($data[$this->name])) {
                $this->data = $data[$this->name];
            } else if ($data[$this->name] instanceof Collection || $data instanceof \IteratorAggregate) {
                return $this->dataWithId($data[$this->name], '', $overWrite);
            } else {
                //
            }
        } else if (is_array($data)) {
            $this->data = $data;
        }

        $this->__items_content__->fill($this->data);

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
        $this->canDelete(!$val);
        $this->canAdd(!$val);
        $this->__items_content__->readonly($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection|\IteratorAggregate $data
     * @param string $idField
     * @param boolean $overWrite
     * @return $this
     */
    public function dataWithId($data, $idField = 'id', $overWrite = false)
    {
        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        $list = [];
        foreach ($data as $k => $d) {
            if (empty($idField)) {
                $idField = $this->getPk($d);
            }

            if ($idField != '_') {
                $list[$d[$idField]] = $d;
            } else {
                $list[$k] = $d;
            }
        }
        $this->data = $list;

        $this->__items_content__->fill($this->data);
        $this->__items_content__->pk($idField);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param mixed $d
     * @return string
     */
    protected function getPk($d)
    {
        $pk = is_object($d) && method_exists($d, 'getPk') ? $d->getPk() : '';
        $pk = !empty($pk) && is_string($pk) ? $pk : '_';

        return $pk;
    }

    /**
     * Undocumented function
     *
     * @return array|Collection|\IteratorAggregate
     */
    public function getData()
    {
        return $this->data;
    }

    public function beforRender()
    {
        $this->__items_content__->label($this->getLabel());
        $this->__items_content__->beforRender();
        return parent::beforRender();
    }

    public function customVars()
    {
        return [
            'itemsContent' => $this->__items_content__,
        ];
    }

    /**
     * Undocumented function
     * 
     * @return boolean
     */
    public function isInTable()
    {
        return false;
    }

    /**
     * Undocumented function
     *
     * @param boolean $is
     * @return $this
     */
    public function inTable($val = false)
    {
        $this->inTable = false;
        return $this;
    }


    /**
     * @return array
     */
    public function renderValue()
    {
        return ['__items_content__'];
    }

    /**
     * 在每个模板字段上执行
     * 
     * @param \Closure $callback
     * @return $this
     */
    public function templateFieldCall($callback)
    {
        $this->__items_content__->templateFieldCall($callback);
        return $this;
    }

    protected function fieldScript()
    {
        $table = $this->__items_content__->getId();
        $VModel = $this->getVModel();

        $script = <<<EOT

        if ({$VModel}) {
            {$VModel} = Object.keys({$VModel} ).reduce((acc, key) => {
                acc[key] = {$table}Convert({$VModel}[key]);
                return acc;
            }, {});
        }

EOT;
        $this->convertScript[] = $script;
    }
}
