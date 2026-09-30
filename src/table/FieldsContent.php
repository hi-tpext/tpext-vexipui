<?php

namespace tpext\builder\table;

use think\Model;
use tpext\builder\common\Module;
use tpext\builder\common\Table;
use tpext\builder\displayer\Field;
use tpext\builder\inface\Renderable;
use tpext\think\View;
use tpext\builder\inface\ReleaseAble;
use tpext\builder\traits\HasDestroyOnce;

class FieldsContent extends TWrapper implements Renderable, ReleaseAble
{
    use HasDestroyOnce;

    protected $view = 'fieldscontent';

    protected $cols = [];

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Table
     */
    protected $table;

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->cols as $col) {

            if (!($col instanceof TColumn)) {
                $col->fill($this->data);
                $col->beforRender();
                continue;
            }

            $displayer = $col->getDisplayer();

            $displayer
                ->value('')
                ->fill($this->data)
                ->beforRender();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param TColumn|Field $col
     * @return $this
     */
    public function addCol($col)
    {
        $this->cols[] = $col;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return TColumn[]
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Undocumented function
     *
     * @param Table $val
     * @return $this
     */
    public function setTable($val)
    {
        $this->table = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
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
            $fields[0]($this->table);
        }

        $this->table->fieldsEnd();
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
     * @param string $val
     * @return $this
     */
    public function value($val)
    {
        if (is_array($val)) {
            $this->data = $val;
        } else {
            $this->data = [];
        }
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
        foreach ($this->cols as $col) {
            if (!($col instanceof TColumn)) {
                continue;
            }

            $col->getDisplayer()->extKey($val);
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
        foreach ($this->cols as $col) {
            if (!($col instanceof TColumn)) {
                continue;
            }

            $col->getDisplayer()->clearScript();
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
        $template = Module::getInstance()->getViewsPath() . 'table' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        $vars = [
            'cols' => $this->cols,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->table, $name], $arguments);
    }

    public function destroy()
    {
        // 已销毁直接返回：同一组件可被多归属路径重复触达（契约见 traits\HasDestroyOnce）
        if ($this->__destroyed__) {
            return;
        }
        foreach ($this->cols as $col) {
            if ($col instanceof ReleaseAble) {
                $col->destroy();
            }
        }

        // 数组属性复位为空数组（保持类型恒定，二次 destroy 自然幂等）；对象引用置 null
        $this->cols = [];
        $this->data = [];
        $this->table = null;
        $this->__destroyed__ = true;
    }
}
