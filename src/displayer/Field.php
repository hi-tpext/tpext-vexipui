<?php

namespace tpext\builder\displayer;

use think\Model;
use tpext\think\View;
use tpext\common\ExtLoader;
use tpext\builder\form\FRow;
use tpext\builder\search\SRow;
use tpext\builder\common\Module;
use tpext\builder\form\Fillable;
use tpext\builder\table\TColumn;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\common\Wrapper;
use tpext\builder\common\SizeAdapter;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Table;

/**
 * Field class
 */
class Field implements Fillable
{
    use HasDom;

    protected $id = '';
    protected $formMode = 'form';
    protected $displayerType = 'Field';
    protected $extKey = '';
    protected $name = '';
    protected $label = '';
    protected $js = [];
    protected $customJs = [];
    protected $customCss = [];
    protected $css = [];
    protected $stylesheet = '';
    protected $onMountedScript = [];
    protected $setupScript = [];
    protected $convertScript = [];
    protected $view = 'field';
    protected $input = true; //是否为可输入元素
    protected $isFieldsGroup = false;

    /**
     * @var string|array
     */
    protected $value = '';
    protected $lockValue = false;
    protected $default = '';
    protected $icon = '';
    protected $autoPost = [];
    protected $showLabel = true;
    protected $labelClass = '';
    protected $labelAttr = '';
    protected $size = [2, 8];
    protected $help = '';
    protected $readonly = false;
    protected $disabled = false;
    protected $valueType = 'string';
    protected $renderValue = null;
    protected $inTable = false;
    /**
     * Undocumented variable
     *
     * @var FRow|SRow|TColumn
     */
    protected $wrapper = null;
    protected static $templPath;
    protected $mapClass = [];
    protected $required = false;
    protected $minify = false;
    protected $to = '';
    protected $data = [];
    protected $jsOptions = [];
    protected $exporting = false;
    protected $randomKey = '';
    protected $propBind = true; // form-item 绑定字段

    /**
     * Undocumented variable
     *
     * @var \Closure[]
     */
    protected $rendering = [];

    public function __construct($name, $label = '')
    {
        $this->name = trim($name);

        if (empty($label) && !empty($this->name)) {
            $label = __blang(ucfirst($this->name));
        }

        if (strstr($this->name, '.')) {
            $this->extKey = '_' . explode('.', $this->name)[0];
        }

        $this->randomKey = mt_rand(10, 99);

        $this->label = $label;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return void
     */
    public function created($type = '')
    {
        $type = $type ? $type : get_called_class();

        $this->displayerType = class_basename($type);

        $defaultClass = Wrapper::hasDefaultFieldClass($type);

        if (!empty($defaultClass)) {
            $this->class = $defaultClass;
        }

        ExtLoader::trigger('tpext_displayer_created', $this);
    }

    /**
     * 判断是否为某个类型的
     *
     * @param string $type
     * @return boolean
     */
    public function isDisplayerType($type)
    {
        return strtolower($this->displayerType) === strtolower($type);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getDisplayerType()
    {
        return $this->displayerType;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        if ($this->id) {
            return $this->id;
        }
        $this->id = ($this->formMode == 'table' ? $this->getForm()->getTableId() : $this->getForm()->getFormId())
            . $this->displayerType . preg_replace('/\W/', '_', ucfirst($this->extKey) . ucfirst($this->name)) . $this->randomKey;

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getOriginName()
    {
        return $this->wrapper->getName();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->extKey = $val;
        $this->id = '';
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @return boolean
     */
    public function isInTable()
    {
        return $this->inTable;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $refresh
     * @return $this
     */
    public function autoPost($url = '', $refresh = false)
    {
        if (empty($url)) {
            $url = (string) url('autopost');
        }
        $this->autoPost = ['url' => $url, 'refresh' => $refresh, 'displayerType' => $this->displayerType];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isInput()
    {
        return $this->input;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isFieldsGroup()
    {
        return $this->isFieldsGroup;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isArrayValue()
    {
        return $this->valueType == 'array';
    }

    /**
     * 设置字段值
     *
     * @param string|array|mixed $val 值
     * @return $this
     */
    public function value($val)
    {
        if ($this->lockValue) {
            return $this;
        }

        if (is_array($val)) {
            $val = implode(',', $val);
        }
        $this->value = $val;
        $this->renderValue = null;
        return $this;
    }

    /**
     * 锁定$value，不会被后续value()/fill()方法覆盖值
     * 
     * $form->text('field_a', 'A')->value('hello')->lockValue();
     * $form->fill(['field_a' => 'world']);//field_a不会覆被盖
     *
     * @param boolean $val
     * @return $this
     */
    public function lockValue($val = true)
    {
        $this->lockValue = $val;

        return $this;
    }

    /**
     * 格式化输出，用于只显示的字段，应避免都对可录入的字段使用
     *
     * @param string|\Closure $val
     * @return $this
     */
    public function to($val)
    {
        $this->to = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int|mixed $val
     * @return $this
     */
    public function default($val = '')
    {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val a or a.b.c
     * @return $this
     */
    public function name($val)
    {
        if (strstr($val, '[')) {
            $val = str_replace(['[', ']'], ['.', ''], $val);
        }
        $this->name = $val;
        $this->id = '';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function label($val)
    {
        $this->label = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function labelClass($val)
    {
        $this->labelClass = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function labelAttr($val)
    {
        $this->labelAttr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $label
     * @param integer|string $element
     * @return $this
     */
    public function size($label = 2, $element = 8)
    {
        $this->size = [$label, $element];
        return $this;
    }

    /**
     * Undocumented function
     * @example 1 [int] 4 => class="col-md-4"
     * @example 2 [string] '4 xls-4' => class="col-md-4 xls-4"
     *
     * @param int|string $val
     * @return $this
     */
    public function cloSize($val)
    {
        $this->wrapper->cloSize($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function help($val)
    {
        $this->help = $val;
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
        $this->readonly = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function disabled($val = true)
    {
        $this->disabled = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function required($val = true)
    {
        $this->required = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function showLabel($val)
    {
        $this->showLabel = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $is
     * @return $this
     */
    public function inTable($val = true)
    {
        $this->inTable = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isShowLabel()
    {
        return $this->showLabel;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function canMinify()
    {
        return $this->minify;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->js = array_merge($this->js, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->js as $k => $j) {
            if (in_array($j, $val)) {
                unset($this->js[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->css as $k => $c) {
            if (in_array($c, $val)) {
                unset($this->css[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceJs($val, $newVal)
    {
        foreach ($this->js as $k => $j) {
            if ($val == $j) {
                $this->js[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceCss($val, $newVal)
    {
        foreach ($this->css as $k => $c) {
            if ($val == $c) {
                $this->css[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * 添加自定义js，不会被minify
     * vue 版本取消minify，和addJs一样效果
     *
     * @param array|string $val
     * @return $this
     */
    public function customJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customJs = array_merge($this->customCss, $val);
        return $this;
    }

    /**
     * 添加自定义css，不会被minify
     * vue 版本取消minify，和addCss一样效果
     * @param array|string $val
     * @return $this
     */
    public function customCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customCss = array_merge($this->customCss, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->css = array_merge($this->css, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param FRow|SRow|TColumn $wrapper
     * @return $this
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FRow|SRow|TColumn
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabelClass()
    {
        return $this->labelClass;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabelAttr()
    {
        return $this->labelAttr;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Undocumented function
     *
     * @return string|array
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * return onMountedScript
     *
     * @return array
     */
    public function getScript()
    {
        return $this->onMountedScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getOnMountedScript()
    {
        return $this->onMountedScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getSetupScript()
    {
        return $this->setupScript;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getConvertScript()
    {
        return $this->convertScript;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearScript()
    {
        $this->onMountedScript = [];
        $this->setupScript = [];
        $this->convertScript = [];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $labelMin
     * @return $this
     */
    public function fullSize($labelMin = 3)
    {
        if (empty($this->size) || (is_numeric($this->size[0]) && is_numeric($this->size[1]))) {

            $this->size = [$labelMin, 12 - $labelMin];
        }

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
        if ($this->lockValue) {
            $this->data = $data;
            return $this;
        }

        if (!empty($this->name)) {
            $hasVal = false;
            $value = '';
            if (strstr($this->name, '.')) {
                $arr = explode('.', $this->name);
                // $form->field('b.name')
                // $form->fill($data);

                if (isset($data[$arr[0]])) {
                    // $data = ['name' => 'str1', 'b' => ['name' => 'str2']];
                    // 输出：'str2'
                    if (isset($data[$arr[0]][$arr[1]])) {
                        $value = $data[$arr[0]][$arr[1]];
                        $hasVal = true;
                    }
                    // 
                    //$data = ['name' => 'str1', 'b' => []];
                    // 输出：'str1'
                    else if (isset($data[$arr[1]])) { //尝试读取上一层级的值
                        $value = $data[$arr[1]];
                        $hasVal = true;
                    }
                } else {
                    if (isset($data[$arr[1]])) { //尝试读取上一层级的值
                        $value = $data[$arr[1]];
                        $hasVal = true;
                    }
                    // $data = ['name' => 'str1'];
                    // 输出：'str1'
                }
            } else if (isset($data[$this->name])) {

                $value = $data[$this->name];
                $hasVal = true;
            }

            if (is_array($value)) {
                $value = implode(',', $value);
            }
            if ($hasVal) {
                $this->value($value);
            }
        }

        $this->data = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $event
     * @return $this
     */
    public function trigger($event)
    {
        $fieldId = $this->getId();

        $script = <<<EOT

        $('#{$fieldId}').trigger('{$event}');

EOT;
        $this->onMountedScript[] = $script;
        return $this;
    }

    /**
     * add script to onMountedScript
     *
     * @param string $script
     * @return $this
     */
    public function addScript($script)
    {
        $this->onMountedScript[] = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function addOnMountedScript($script)
    {
        $this->onMountedScript[] = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function addSetupScript($script)
    {
        $this->setupScript[] = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function addStyleSheet($stylesheet)
    {
        $this->stylesheet[] = $stylesheet;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string|int|\Closure $values
     * @param string $class
     * @param string $field default current field
     * @param string $logic in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr
     * @return $this
     */
    public function mapClass($values, $class, $field = '', $logic = 'in_array')
    {
        if (empty($field)) {
            $field = $this->name;
        }

        if (!($values instanceof \Closure) && !is_array($values)) {
            $values = [$values];
        }

        $this->mapClass[] = [$values, $class, $field, $logic];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $groupArr
     * @example location1 [[$values1, $class1, $field1, $logic1], [$values2, $class2, $field2, $logic2], ... ]
     * @example location2 ['class1' => [$values1, $field1, $logic1], 'class2'=> [$values2, $field2, $logic2], ... ]
     * @example location3 ['class1' => function closure1() {...}, 'class2'=> function closure2() {...}, ... ]
     * @return $this
     */
    public function mapClassGroup($groupArr)
    {
        foreach ($groupArr as $key => $g) {
            if (is_int($key)) { //  1
                $values = $g[0];
                $class = $g[1];
                $field = isset($g[2]) ? $g[2] : '';
                $logic = isset($g[3]) ? $g[3] : '';
                $this->mapClass($values, $class, $field, $logic);
            } else if (is_string($key)) { //  2 /  3
                if (is_array($g)) //2
                {
                    $values = $g[0];
                    $field = isset($g[1]) ? $g[1] : '';
                    $logic = isset($g[2]) ? $g[2] : '';
                    $this->mapClass($values, $key, $field, $logic);
                } else if ($g instanceof \Closure) {
                    $this->mapClass($g, $key);
                }
            }
        }

        return $this;
    }

    /**
     * Summary of isExporting
     * @param boolean $val
     * @return $this
     */
    public function exporting($val = true)
    {
        $this->exporting = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCsrfToken()
    {
        return Builder::getInstance()->getCsrfToken();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getJsOptions()
    {
        return $this->jsOptions;
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

    public function __toString()
    {
        return $this->render();
    }

    protected function getViewInstance()
    {
        $template = Module::getInstance()->getViewsPath() . 'displayer' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        return $viewshow;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function vBindOp()
    {
        if ($this->isDisplayerType('map')) {
            return $this;
        }
        $fieldId = $this->getId();

        if ($this->isDisabled()) {
            $this->jsOptions['disabled'] = true;
        } else {
            unset($this->jsOptions['disabled']);
        }
        if ($this->isReadonly()) {
            $this->jsOptions['readonly'] = true;
        } else {
            unset($this->jsOptions['readonly']);
        }

        $rules = [];
        if ($this->isRequired()) {
            $this->jsOptions['required'] = true;
            $rules = ['required' => true, 'message' => '[' . $this->label . ']' . __blang('bilder_validate_required')];
        }

        $configs = empty($this->jsOptions) ? '{}' : json_encode($this->jsOptions, JSON_UNESCAPED_UNICODE);
        $rules = json_encode($rules, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$fieldId}Op = ref({$configs});
    const {$fieldId}Rules = ref({$rules});

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken(["{$fieldId}Op"]);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function fieldScript()
    {
        //
    }

    /**
     * 在列表中时初始化脚本
     * @return string
     */
    public function getInitRowScript()
    {
        return '//console.log(row);';
    }

    /**
     * Undocumented function
     * @return $this
     */
    public function beforRender()
    {
        if (
            $this->formMode == 'form' && !$this->inTable && !$this->getForm()->isReadonly() && !$this->isDisplayerType('items')
            && (!$this->isInput() || $this->to)
        ) {
            $this->name .= '__display__only'; //只读时，name加上__display__only，避免提交
        }

        $this->fieldScript();
        $this->vBindOp();

        if ($this->minify) {
            Builder::getInstance()->addJs($this->js);
            Builder::getInstance()->addCss($this->css);
        } else {
            Builder::getInstance()->customJs($this->js);
            Builder::getInstance()->customCss($this->css);
        }

        Builder::getInstance()->customJs($this->customJs);
        Builder::getInstance()->customCss($this->customCss);

        if ($this->formMode == 'table' && $this->autoPost) {
            if (Builder::checkUrl($this->autoPost['url'])) {
                $this->getForm()->addAutoPost($this->name, $this->autoPost);
            } else {
                $this->readonly();
            }
        }

        if ($this->formMode != 'table' && ($this->readonly || $this->disabled) && $this->isInput() && !($this instanceof MultipleFile)) {
            $this->getWrapper()->addClass('form-field-readonly')->addAttr('title="' . __blang('bilder_readonly') . '"');
        }

        if (!empty($this->onMountedScript)) {
            Builder::getInstance()->addOnMountedScript($this->onMountedScript);
        }

        if (!empty($this->setupScript)) {
            Builder::getInstance()->addSetupScript($this->setupScript);
        }

        if (($this->formMode == 'form' || $this->formMode == 'search') && !empty($this->convertScript)) {
            if (!$this->inTable) { //不在items中
                $this->getForm()->addConvertScript($this->convertScript);
            }
        } else if ($this->formMode == 'table' && !empty($this->convertScript)) {
            $this->getForm()->addConvertScript($this->convertScript);
        }

        if (!empty($this->stylesheet)) {
            Builder::getInstance()->addStyleSheet($this->stylesheet);
        }

        if (count($this->rendering)) {
            foreach ($this->rendering as $rd) {
                if ($rd instanceof \Closure) {
                    $rd->call($this, $this);
                }
            }
        }

        $this->addToFormData();

        ExtLoader::trigger('tpext_displayer_befor_render', $this);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|array
     */
    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }
        if ($this->exporting) {
            $this->valueType = 'string';
        }
        $value = !($this->value === '' || $this->value === null || $this->value === []) ? $this->value : $this->default;

        if ($this->valueType == 'array') {
            if (!is_array($value)) {
                $value = explode(',', (string) $value);
            }
            array_walk($value, function (&$v) {
                $v = (string) $v;
            });
            $value = array_values(array_filter($value, 'strlen'));
        } else if ($this->valueType == 'boolean') {
            $value = (bool) $value;
        } else if ($this->valueType == 'integer') {
            $value = (int) $value;
        } else if ($this->valueType == 'float') {
            $value = (float) $value;
        } else {
            if (is_array($value)) {
                $value = implode(',', array_filter($value, 'strlen'));
            }
            $value = (string) $value;
        }

        if ($this->valueType != 'array' && !empty($this->to)) {
            $value = $this->parseToValue($value);
        }

        $this->renderValue = $value;

        return $value;
    }

    protected function parseToValue($value)
    {
        $data = $this->data;

        $to = $this->to;
        if ($to instanceof \Closure) {
            //data 为空时可能引起错误
            if (empty($this->value) && empty($this->default) && empty($this->data)) {
                return '';
            }
            return $to($value, $data);
        }
        preg_match_all('/\{([\w\.]+)\}/', $this->to, $matches);
        $keys = ['{val}', '{__val__}'];
        $replace = [$value, $value];
        $arr = null;
        foreach ($matches[1] as $match) {
            $arr = explode('.', $match);

            if (count($arr) == 1) {
                $keys[] = '{' . $arr[0] . '}';
                $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
            } else if (count($arr) == 2) {

                $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '-';
            } else {
                //最多支持两层 xx 或 xx.yy
            }
        }

        $val = str_replace($keys, $replace, $to);

        return $val;
    }

    public function parseMapClass()
    {
        $matchClass = [];
        $values = $class = $field = $logic = $val = $match = null;
        if (!empty($this->mapClass)) {

            foreach ($this->mapClass as $mp) {
                $values = $mp[0];
                $class = $mp[1];
                $field = $mp[2];
                $logic = $mp[3]; //in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr
                $val = '';
                if (strstr($field, '.')) {
                    $arr = explode('.', $field);
                    if (isset($this->data[$arr[0]]) && isset($this->data[$arr[0]][$arr[1]])) {

                        $val = $this->data[$arr[0]][$arr[1]];
                    } else {
                        continue;
                    }
                } else {
                    if (!isset($this->data[$field])) {
                        continue;
                    }
                    $val = $this->data[$field];
                }
                if ($values instanceof \Closure) {
                    $match = $values($val, $this->data);
                    if ($match) {
                        $matchClass[] = $class;
                    }
                    continue;
                }

                $match = false;
                if ($logic == 'not_in_array' || $logic == '!in_array') {
                    $match = !in_array($val, $values);
                } else if ($logic == 'eq' || $logic == '==') {
                    $match = $val == $values[0];
                } else if ($logic == 'gt' || $logic == '>') {
                    $match = is_numeric($values[0]) && $val > $values[0];
                } else if ($logic == 'lt' || $logic == '<') {
                    $match = is_numeric($values[0]) && $val < $values[0];
                } else if ($logic == 'egt' || $logic == '>=') {
                    $match = is_numeric($values[0]) && $val >= $values[0];
                } else if ($logic == 'elt' || $logic == '<=') {
                    $match = is_numeric($values[0]) && $val <= $values[0];
                } else if ($logic == 'strpos' || $logic == 'strstr') {
                    $match = strstr($val, $values[0]);
                } else if ($logic == 'not_strpos' || $logic == 'not_strstr' || $logic == '!strpos' || $logic == '!strstr') {
                    $match = !strstr($val, $values[0]);
                } else //default in_array
                {
                    $match = in_array($val, $values);
                }
                if ($match) {
                    $matchClass[] = $class;
                }
            }
        }
        if (count($matchClass)) {
            return ' ' . implode(' ', array_unique($matchClass));
        }
        return '';
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        if (empty(static::$templPath)) {
            static::$templPath = Module::getInstance()->getViewsPath() . 'displayer' . DIRECTORY_SEPARATOR;
        }

        $mapClass = $this->parseMapClass();

        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'requiredStyle' => $this->required ? '' : 'style="display: none;"',
            'extKey' => $this->extKey,
            'value' => $this->inTable ? '' : $this->renderValue(),
            'class' => ' row-' . preg_replace('/\W/', '_', $this->name) . $this->getClass() . $mapClass,
            'attr' => $this->getAttrWithStyle(),
            'size' => $this->adjustSize(),
            'labelClass' => is_numeric($this->size[0]) && $this->size[0] < 12 ? $this->labelClass . ' control-label' : $this->labelClass . ' full-label',
            'labelAttr' => empty($this->labelAttr) ? '' : ' ' . $this->labelAttr,
            'sizeAttr' => $this->getSizeAttr(),
            'help' => $this->help,
            'showLabel' => $this->inTable || (is_numeric($this->size[0]) && $this->size[0] == 0) ? false : $this->showLabel,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
            'formMode' => $this->formMode,
            'formId' => $this->formMode == 'table' ? $this->getForm()->getTableId() : $this->getForm()->getFormId(),
            'VModel' => $this->getVModel(),
            'hasWrapper' => !$this->inTable,
            'helptempl' => static::$templPath . 'helptempl.html',
            'labeltempl' => static::$templPath . 'labeltempl.html',
            'begintempl' => static::$templPath . 'begintempl.html',
            'endtempl' => static::$templPath . 'endtempl.html',
            'displayerType' => strtolower($this->displayerType),
            'inTable' => $this->inTable,
            'propBind' => $this->propBind,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $vars;
    }

    /**
     * Undocumented function
     * 
     * @return void
     */
    protected function addToFormData()
    {
        if ($this->inTable) {
            return;
        }

        $displayer = $this;

        $fieldName = $displayer->getName();
        $renderValue = $displayer->renderValue();
        $displayer->getForm()->addFormData($fieldName, $renderValue);

        if ($displayer instanceof MultipleFile) {
            $thumbs = $displayer->thumbs();
            $displayer->getForm()->addFormData($fieldName . '__thumbs', $thumbs);
        } else if ($displayer instanceof DateTime) {
            $displayer->getForm()->addFormData($fieldName . '__tmp', $renderValue);
        } else if ($displayer instanceof Tree) {
            $displayer->getForm()->addFormData($fieldName . '__tmp', is_array($renderValue) ? implode(',', $renderValue) : $renderValue);
        }

        if (
            (($displayer->isInput() || $this->isDisplayerType('items')))
            && $displayer->isRequired() && !$displayer->isReadonly() && !$displayer->isDisabled()
        ) {
            $displayer->getForm()->addValidatorRule($fieldName, ['required' => true, 'message' => '[' . $this->label . ']' . __blang('bilder_validate_required')]);
            if ($displayer instanceof DateTime) {
                $displayer->getForm()->addValidatorRule($fieldName . '__tmp', ['required' => true, 'message' => '[' . $this->label . ']' . __blang('bilder_validate_required')]);
            }
        }

        if ($displayer instanceof Fields) {
            $childrenFields = $displayer->getContent()->getRows();

            foreach ($childrenFields as $child) {
                $child->getDisplayer()->addToFormData();
            }

            return;
        } else if ($displayer instanceof Items) {
            $childrenFields = $displayer->getContent()->getCols();

            foreach ($childrenFields as $child) {
                $child->getDisplayer()->addToFormData();
            }

            return;
        }
    }

    /**
     * Undocumented function
     * 
     * @return array
     */
    public function fieldInfo()
    {
        $matchClass = explode(' ', $this->parseMapClass());

        if ($this->isRequired()) {
            $matchClass[] = 'is-required';
        }

        if ($this->isDisabled() || $this->hasClass('disabled')) {
            $matchClass[] = 'disabled';
            $matchClass[] = 'readonly';
        }
        if ($this->isReadonly() || $this->hasClass('readonly')) {
            $matchClass[] = 'readonly';
        }

        $info = [
            'matchClass' => array_values(array_filter($matchClass)),
        ];
        if (!$info['matchClass']) {
            unset($info['matchClass']);
        }

        return $info;
    }

    /**
     * 字段的 v-model= 名称
     *
     * @return string
     */
    public function getVModel()
    {
        $name = $this->getName();
        if (strstr($name, '.')) {
            //解决多层字段使用数字作为键名非法问题，如 .price.2 应转换为 .price['2']
            $arr = explode('.', $name);
            $name = $arr[0];
            array_shift($arr);
            $name .= "['" . implode("']['", $arr) . "']";
        }

        if ($this->inTable) {
            return 'row.' . $name;
        }
        if ($this->formMode == 'form' || $this->formMode == 'search') {
            return $this->getForm()->getFormId() . 'Data.' . $name;
        }
        return 'row.' . $name;
    }

    /**
     * 所属表单的 v-model= 名称
     *
     * @return string
     */
    public function getFormVModel()
    {
        if ($this->inTable) {
            return 'row';
        }
        if ($this->formMode == 'form' || $this->formMode == 'search') {
            return $this->getForm()->getFormId() . 'Data';
        }
        return 'row';
    }


    /**
     * Undocumented function
     *
     * @return Form|Search|Table
     */
    protected function getForm()
    {
        if ($this->formMode == 'form' || $this->formMode == 'search') {
            return $this->getWrapper()->getForm();
        }
        return $this->getWrapper()->getTable();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function adjustSize()
    {
        if ($this->inTable) {
            return [0, 12];
        }
        return SizeAdapter::make()->adjustDisplayerSize($this->size);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function rendering($callback)
    {
        $this->rendering[] = $callback;
        return $this;
    }

    /**
     * 设置table列可排序
     *
     * @param boolean $val
     * @return $this
     */
    public function colSortable($val = true)
    {
        $this->wrapper->sortable($val);
        return $this;
    }

    /**
     * 设置table列默认隐藏
     *
     * @param boolean $val
     * @return $this
     */
    public function colHidden($val = true)
    {
        $this->wrapper->hidden($val);
        return $this;
    }

    /**
     * 需要return的vue变量
     *
     * @param array|string $token
     * @return $this
     */
    public function addVueToken($token)
    {
        Builder::getInstance()->addVueToken($token);

        return $this;
    }

    /**
     * 需要引入的UI Component
     *
     * @param array|string $component
     * @return $this
     */
    public function addComponentImport($component)
    {
        Builder::getInstance()->addComponentImport($component);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val from | table | search
     * @return $this
     */
    public function setFormMode($val)
    {
        $this->formMode = $val ?: 'from';
        if ($this->formMode == 'table') {
            $this->jsOptions['size'] = 'small';
            if ($this->isInput()) {
                $this->autoPost(); //表格中修改后自动提交
            }
        }
        return $this;
    }

    public function getFormMode()
    {
        return $this->formMode;
    }

    public function destroy()
    {
        $this->data = null;
        $this->wrapper = null;
    }

    public function getSizeAttr()
    {
        $size = $this->adjustSize();

        return [SizeAdapter::make()->getColSizeAttrFromColClass($size[0]), SizeAdapter::make()->getColSizeAttrFromColClass($size[1])];
    }
}
