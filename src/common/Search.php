<?php

namespace tpext\builder\common;

use think\helper\Arr;
use tpext\think\View;
use tpext\builder\form\FRow;
use tpext\builder\form\When;
use tpext\builder\search\SRow;
use tpext\builder\common\Module;
use tpext\builder\form\Fillable;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\displayer\Text;
use tpext\builder\search\TabLink;
use tpext\builder\displayer\Field;
use tpext\builder\search\SWrapper;
use tpext\builder\displayer\Button;
use tpext\builder\inface\Renderable;
use tpext\builder\form\FieldsContent;

/**
 * Search class
 */
class Search extends SWrapper implements Renderable
{
    use HasDom;

    protected $id = 'theSearch';

    /**
     * Undocumented variable
     *
     * @var FRow[] 
     */
    protected $rows = [];
    protected $searchButtonsCalled = false;
    protected $defaultDisplayerSize = [4, 8];
    protected $defaultDisplayerColSize = 2;
    protected $butonsSizeClass = 'small';
    protected $open = true;
    protected $tableId = '';
    protected $formData = [];
    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $addTop;
    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $addBottom;
    /**
     * Undocumented variable
     *
     * @var TabLink
     */
    protected $tablink = null;
    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;
    /**
     * Undocumented variable
     *
     * @var When
     */
    protected $__when__ = null;
    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->open = Module::config('search_open') == 1;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param SRow|Fillable $row
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
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function empty()
    {
        return empty($this->rows);
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function createFields()
    {
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setForm($this);
        return $this->__fields__;
    }

    /**
     * Undocumented function
     * @param Field $watchFor
     * @param string|int|array $cases
     * @return When
     */
    public function createWhen($watchFor, $cases)
    {
        $this->__when__ = new When();
        $this->__when__->watch($watchFor, $cases);
        $this->__when__->setForm($this);
        return $this->__when__;
    }


    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsEnd()
    {
        $this->__fields__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function whenEnd()
    {
        $this->__when__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $tableId
     * @return $this
     */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
        $this->id = preg_replace('/\W/', '_', 'search' . ucfirst($this->tableId));
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function open($val = true)
    {
        $this->open = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $key trigger feild
     * @return TabLink
     */
    public function tabLink($key)
    {
        if (empty($this->tablink)) {
            $this->tablink = new TabLink();
            $this->tablink->key($key);
        }

        return $this->tablink;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function addTop()
    {
        if (empty($this->addTop)) {
            $this->addTop = Row::make();
            $this->addTop->class('search-top');
        }

        return $this->addTop;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function addBottom()
    {
        if (empty($this->addBottom)) {
            $this->addBottom = Row::make();
            $this->addBottom->class('search-bottom');
        }

        return $this->addBottom;
    }

    /**
     * Undocumented function
     * btn-lg btn-sm btn-xs
     * @param string $val
     * @return $this
     */
    public function butonsSizeClass($val)
    {
        $this->butonsSizeClass = str_replace('btn-', '', $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $label
     * @param integer $element
     * @return $this
     */
    public function defaultDisplayerSize($label = 4, $element = 8)
    {
        $this->defaultDisplayerSize = [$label, $element];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return $this
     */
    public function defaultDisplayerColSize($size = 2)
    {
        $this->defaultDisplayerColSize = $size;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function searchButtons($create = true)
    {
        if ($create) {
            $this->fieldsEnd();
            $this->fields('search_buttons', ' ', '3 col-xl-3 col-lg-3 col-sm-12 col-xs-12 search-buttons')
                ->size('3 col-lg-4 col-sm-2 col-xs-12', '9 col-xl-8 col-lg-8 col-sm-8 col-xs-12')
                ->with(
                    $this->button('submit', __blang('bilder_button_filter'), '6 col-xl-6 col-lg-6 col-sm-6 col-xs-6')
                        ->type('primary')
                        ->buttonSize($this->butonsSizeClass)
                        ->onClick($this->id . 'Submit();'),
                    $this->button('button', __blang('bilder_button_reset'), '6 col-xl-6 col-lg-6 col-sm-6 col-xs-6')
                        ->type('default')
                        ->buttonSize($this->butonsSizeClass)
                        ->onClick($this->id . 'Reset();')
                );
        }

        $this->searchButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $type
     * @return $this
     */
    public function btnSubmit($label = '筛&nbsp;&nbsp;选', $size = '2 col-xl-2 col-lg-2 col-sm-6 col-xs-12', $type = 'primary')
    {
        if ($label == '筛&nbsp;&nbsp;选') {
            $label = __blang('bilder_button_filter');
        }
        $this->fieldsEnd();
        $this->button('button', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->onClick($this->id . 'Submit();');
        $this->searchButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $type
     * @return $this
     */
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = '2 col-xl-2 col-lg-2 col-sm-6 col-xs-12', $type = 'default')
    {
        if ($label == '重&nbsp;&nbsp;置') {
            $label = __blang('bilder_button_reset');
        }
        $this->button('button', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->onClick($this->id . 'Reset();');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string|int|array|mixed $value
     * @return $this
     */
    public function addFormData($name, $value)
    {
        if (strstr($name, '[')) {
            $name = str_replace(['[', ']'], ['.', ''], $name);
        }
        Arr::set($this->formData, $name, $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $empty = empty($this->rows);

        if (!$empty) {
            if (!$this->searchButtonsCalled) {
                $this->searchButtons();
            }
        } else {
            $this->addClass('form-empty');
            $this->open = false;
            $this->button('submit', 'submit', '1')->getWrapper()->addClass('hidden');
        }

        $this->hidden('__search__')->value($this->getFormId());
        $this->hidden('__table__')->value($this->tableId);

        $this->button('refresh', 'refresh', '1')
            ->addClass('search-refresh')
            ->getWrapper()
            ->addClass('hidden');

        foreach ($this->rows as $row) {
            $row->beforRender();
        }

        if ($this->tablink) {
            $this->tablink->searchId($this->id);
            $key = $this->tablink->getKey();
            $this->formData[$key] = $this->tablink->getActive();
            $this->tablink->beforRender();
        }

        if ($this->addTop) {
            $this->addTop->beforRender();
        }

        if ($this->addBottom) {
            $this->addBottom->beforRender();
        }
        $this->eventScript();
        $this->searchScript();

        return $this;
    }

    protected function eventScript()
    {
        $form = $this->id;

        $script = <<<EOT

        $(document).bind('keyup', function(event) {
            if (event.keyCode === 13) {
                if($('#{$form} form').hasClass('form-empty'))
                {
                    return false;
                }
                if($('form').length > 1)
                {
                    return false;
                }
                {$form}Submit();
                return false;
            }
            if (event.keyCode === 0x1B) {
                if($('#{$form} form').hasClass('form-empty'))
                {
                    return true;
                }
                var index = layer.msg(__blang.bilder_reset_filter_criteria, {
                    time: 2000,
                    btn: [__blang.bilder_button_ok, __blang.bilder_button_cancel],
                    yes: function (params) {
                        layer.close(index);
                        {$form}Reset();
                    }
                });
                return false; //阻止系统默认esc事件
            }
        });
EOT;
        Builder::getInstance()->addOnMountedScript($script);

        //兼容 jquery 方式刷新列表页面 parent.$('.row-refresh').trigger('click');
        $refreshScript = <<<EOT

        $('.search-refresh').click(function() {
            window.{$form}Submit();
        });
EOT;
        Builder::getInstance()->addOnMountedScript($refreshScript);
    }

    protected function searchScript()
    {
        $form = $this->id;
        $table = $this->tableId;
        $open = $this->open ? 'true' : 'false';
        $formData = json_encode($this->formData, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$form}Ref = ref(null);
    const {$form}Data = reactive($formData);
    const {$form}Visible = ref({$open});

    const {$form}Submit = () => {
        {$table}Refresh(true);
    };

    const {$form}Reset = () => {
        {$form}Ref.value.reset();
        {$table}Refresh(true);
    };

    const {$table}ToggleSearch = () => {
        {$form}Visible.value = !{$form}Visible.value;
    };

    const {$form}Op = ref({
        'hide-label': true,
        'size' : 'small',
        'gap' : [0, 0],
    });

    window.{$form}Submit = {$form}Submit;
    window.{$form}Reset = {$form}Reset;
    window.refreshTable = () => {
        {$table}Refresh();
    };

EOT;

        Builder::getInstance()->addVueToken(["{$form}Ref", "{$form}Op", "{$form}Data", "{$form}Visible"]);
        Builder::getInstance()->addSetupScript($script);

        return $script;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getViewTemplate()
    {
        $template = Module::getInstance()->getViewsPath() . 'table' . DIRECTORY_SEPARATOR . 'search.html';

        return $template;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        $viewshow = new View($this->getViewTemplate());

        $vars = [
            'rows' => $this->rows,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'id' => $this->id,
            'searchFor' => $this->tableId,
            'tablink' => $this->tablink,
            'addTop' => $this->addTop,
            'addBottom' => $this->addBottom,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = SRow::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : $this->defaultDisplayerColSize, $count > 3 ? $arguments[3] : '');

            if ($this->__fields__) {
                $this->__fields__->addRow($row);
            } else {
                $this->rows[] = $row;
            }

            $row->setForm($this);

            $displayer = $row->$name($arguments[0], $count > 1 ? $arguments[1] : '');

            $row->setLabel($displayer->getLabel());

            if ($this->__when__) {
                $this->__when__->toggle($displayer);
            }

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            $displayer->extKey($this->tableId);

            if ($displayer instanceof Text) {
                $displayer->befor('');
                $displayer->after('');
            } else if ($displayer instanceof Button) {
                $displayer->size(0, 12);
            }

            $displayer->setFormMode('search');

            return $displayer;
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }

    /**
     * 创建自身
     *
     * @param mixed $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return Widget::makeWidget('Search', $arguments);
    }

    public function destroy()
    {
        $this->__fields__ = null;
        $this->__when__ = null;
        if ($this->addTop) {
            $this->addTop->destroy();
            $this->addTop = null;
        }
        if ($this->addBottom) {
            $this->addBottom->destroy();
            $this->addBottom = null;
        }
        $this->tablink = null;
        foreach ($this->rows as $row) {
            $row->destroy();
        }
        $this->rows = null;
    }
}
