<?php

namespace tpext\builder\common;

use think\Model;
use think\Collection;
use think\helper\Arr;
use tpext\think\View;
use tpext\common\ExtLoader;
use tpext\builder\form\FRow;
use tpext\builder\form\Step;
use tpext\builder\form\When;
use tpext\builder\common\Module;
use tpext\builder\form\Fillable;
use tpext\builder\form\FWrapper;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\displayer\Field;
use tpext\builder\displayer\Items;
use tpext\builder\displayer\Button;
use tpext\builder\displayer\Fields;
use tpext\builder\form\ItemsContent;
use tpext\builder\inface\Renderable;
use tpext\builder\form\FieldsContent;
use tpext\builder\displayer\MultipleFile;

/**
 * Form class
 */
class Form extends FWrapper implements Renderable
{
    use HasDom;

    protected $view = '';
    protected $action = '';
    protected $id = 'theForm';
    protected $method = 'post';
    /**
     * Undocumented variable
     *
     * @var FRow[]|Fillable[]
     */
    protected $rows = [];
    protected $data = [];
    protected $botttomButtonsCalled = false;
    protected $bottomOffsetCalled = false;
    protected $ajax = true;
    protected $defaultDisplayerSize = null;
    protected $defaultDisplayerColSize = 12;
    protected $validator = [];
    protected $butonsSizeClass = 'default';
    protected $readonly = false;
    protected $partial = false;
    protected $formData = [];
    protected $convertScripts = [];
    protected $validateScripts = [];
    /**
     * Undocumented variable
     *
     * @var Tab
     */
    protected $tab = null;
    /**
     * Undocumented variable
     *
     * @var Step
     */
    protected $step = null;
    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__tabs__ = null;
    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $__fields__bag__ = [];
    /**
     * Undocumented variable
     *
     * @var ItemsContent
     */
    protected $__items__ = null;
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
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param FRow|Fillable $row
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
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        foreach ($this->rows as $row) {

            if ($row instanceof Tab || $row instanceof Step) {
                $row->readonly($val);
                continue;
            }

            if (!($row instanceof FRow)) {
                continue;
            }

            $row->getDisplayer()->readonly($val);
        }

        $this->readonly = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function ajax($val)
    {
        $this->ajax = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function formId($val)
    {
        $this->id = preg_replace('/\W/', '_', $val);
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
     * @param string $val
     * @return $this
     */
    public function action($val)
    {
        $this->action = (string)$val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param string $val
     * @return $this
     */
    public function method($val)
    {
        $this->method = $val;
        return $this;
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
     * @return string
     */
    public function getButonsSizeClass()
    {
        return $this->butonsSizeClass;
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
     * @return Tab
     */
    public function getTab()
    {
        if (empty($this->tab)) {
            $this->tab = new Tab();
            $this->rows[] = $this->tab;
        }
        return $this->tab;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $active
     * @param string $name
     * @return FieldsContent
     */
    public function tab($label, $active = false, $name = '')
    {
        $this->__fields__ = null;
        $this->__items__ = null;

        if (empty($this->tab)) {
            $this->tab = new Tab();
            $this->rows[] = $this->tab;
        }

        $this->__tabs__ = $this->tab->addFieldsContent($label, $active, $name);
        $this->__tabs__->setForm($this);
        return $this->__tabs__;
    }

    /**
     * Undocumented function
     *
     * @return Step
     */
    public function getStep()
    {
        if (empty($this->step)) {
            $this->step = new Step();
            $this->rows[] = $this->step;
        }
        return $this->step;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $description
     * @param boolean $active
     * @param string $name
     * @return FieldsContent
     */
    public function step($label, $description = '', $active = false, $name = '')
    {
        $this->__fields__ = null;
        $this->__items__ = null;

        if ($this->readonly) {
            $this->divider($label);
            return new FieldsContent;
        }

        if (empty($this->step)) {
            $this->step = new Step();
            $this->rows[] = $this->step;
        }

        $this->__tabs__ = $this->step->addFieldsContent($label, $description, $active, $name);
        $this->__tabs__->setForm($this);
        return $this->__tabs__;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function createFields()
    {
        if ($this->__fields__) {
            $this->__fields__bag__[] = $this->__fields__;
        }
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setForm($this);
        return $this->__fields__;
    }

    /**
     * Undocumented function
     *
     * @return ItemsContent
     */
    public function createItems()
    {
        $this->__items__ = new ItemsContent();
        $this->__items__->setForm($this);
        return $this->__items__;
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
        $this->__fields__ = array_pop($this->__fields__bag__);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function itemsEnd()
    {
        $this->__items__ = null;
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
     * @return $this
     */
    public function tabEnd()
    {
        $this->__tabs__ = null;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function stepEnd()
    {
        $this->__tabs__ = null;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function allContentsEnd()
    {
        $this->__fields__ = null;
        $this->__tabs__ = null;
        $this->__items__ = null;
        $this->__when__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function getTabsContent()
    {
        return $this->__tabs__;
    }

    /**
     * Undocumented function
     *
     * @param integer $label
     * @param integer $element
     * @return $this
     */
    public function defaultDisplayerSize($label = 2, $element = 8)
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
    public function defaultDisplayerColSize($size = 12)
    {
        $this->defaultDisplayerColSize = $size;
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
     * @return array|Model
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @param boolean $create
     * @return $this
     */
    public function bottomButtons($create = true)
    {
        if ($create) {
            if ($this->readonly) {
                $this->btnLayerClose();
            } else {
                $this->btnSubmit();
                $this->btnReset();
                $this->formError('__form_error__', '', 12);
            }
        }

        $this->fieldsEnd();

        $this->botttomButtonsCalled = true;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function bottomOffset()
    {
        if ($this->bottomOffsetCalled) {
            return $this;
        }
        $this->allContentsEnd();
        $this->html('', '', '12 col-lg-12 col-sm-12 col-xs-12')->getWrapper()->style('height:12px'); //这里是一个空行
        //此处开启了一个fields装载后面的操作按钮，不会调用fieldsEnd了，正常情况下，底部按钮后面不会有其他元素了。如果有，需要调用fieldsEnd结束按钮区域
        //col-lg 比例:      左(4) | 中部按钮组(4) | 右(4)
        //clo-md 比例:      左(4) | 中部按钮组(4) | 右(4)
        //col-sm 比例:      左(3) | 中部按钮组(6) | 右(3)
        //col-xs 比例:      左(2) | 中部按钮组(8) | 右(2)
        $this->html('', '', '4 col-xl-4 col-lg-4 col-sm-3 col-xs-2')->showLabel(false)->getWrapper()->style('height:40px;margin:0;padding:0;'); //左侧offset 4,4,3,2
        $this->fields('bottom_buttons', '', '4 col-xl-4 col-lg-4 col-sm-6 col-xs-8') //中间按钮组 4,4,6,8
            ->size(0, 12)->showLabel(false);
        $this->bottomOffsetCalled = true;
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
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = '6 col-xl-6 col-lg-6 col-sm-6 col-xs-6', $type = 'primary')
    {
        if ($label == '提&nbsp;&nbsp;交') {
            $label = __blang('bilder_button_submit');
        }
        $this->bottomOffset();
        $this->button('submit', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->onClick($this->id . "Submit();");
        $this->botttomButtonsCalled = true;
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
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = '6 col-xl-6 col-lg-6 col-sm-6 col-xs-6', $type = 'warning')
    {
        if ($label == '重&nbsp;&nbsp;置') {
            $label = __blang('bilder_button_reset');
        }
        $this->bottomOffset();
        $this->button('reset', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->onClick($this->id . "Ref.value.reset();");
        $this->botttomButtonsCalled = true;
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
    public function btnBack($label = '返&nbsp;&nbsp;回', $size = '6 col-xl-6 col-lg-6 col-sm-6 col-xs-6', $type = 'default')
    {
        if ($label == '返&nbsp;&nbsp;回') {
            $label = __blang('bilder_button_go_back');
        }
        $this->bottomOffset();
        $this->button('button', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->onClick('history.go(-1);');
        $this->botttomButtonsCalled = true;
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
    public function btnLayerClose($label = '返&nbsp;&nbsp;回', $size = 12, $type = 'default')
    {
        if ($label == '返&nbsp;&nbsp;回') {
            $label = __blang('bilder_button_go_back');
        }
        $this->bottomOffset();
        $this->button('button', $label, $size)
            ->type($type)
            ->buttonSize($this->butonsSizeClass)
            ->closeLayer();
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function left($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //清理，避免被包含到其他fields中。因为fields可以包含fields的
        $displayer =  $this->fields('left' . mt_rand(10, 99), '', $colSize)->size(0, 12)->showLabel(false)->gap([30, 0]);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \InvalidArgumentException('Argument fieldsCall must be  `Closure` or `null` , if set to `null`, call `->with(...$fields)` follow on .');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
            //如果传入了fields，这里结束掉。如果未传，后面可以再使用->with(...$fields)
            // $form->left(6, function() {...$fields}); 
            // 或者 
            // $form->left(6)->with(...$fields);
            // 或者 
            // $form->left(6)->with(function() {...$fields});
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function middle($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //同上
        $displayer =  $this->fields('middle' . mt_rand(100, 999), '', $colSize)->size(0, 12)->showLabel(false)->gap([30, 0]);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \InvalidArgumentException('Argument fieldsCall must be  `Closure` or `null` , if set to `null`, call `->with(...$fields)` follow on .');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function right($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //同上
        $displayer =  $this->fields('right' . mt_rand(100, 999), '', $colSize)->size(0, 12)->showLabel(false)->gap([30, 0]);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \InvalidArgumentException('fArgument fieldsCall must be  `Closure` or `null` , if set to `null`, call `->with(...$fields)` follow on .');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param array|Collection|\IteratorAggregate dataList
     * @param Closure|null $itemsCall
     * @param array size 大小 [12, 12] 为上下结构，[2, 10]为左右结构
     * @return Items
     */
    public function logs($label, $dataList, $itemsCall = null, $size = [12, 12])
    {
        $this->itemsEnd();
        $displayer =  $this->items('logs' . mt_rand(100, 999) . '__display__only', $label, 12)->size($size[0], $size[1])->readonly();

        if (is_array($dataList)) {
            $displayer->fill($dataList);
        } else if ($dataList instanceof Collection || $dataList instanceof \IteratorAggregate) {
            $displayer->dataWithId($dataList);
        }
        if ($itemsCall) {
            if (!($itemsCall instanceof \Closure)) {
                throw new \InvalidArgumentException('Argument itemsCall must be  `Closure` or `null` , if set to `null`, call `->with(...$fields) follow on .');
            }
            $itemsCall($this);
            $this->itemsEnd();
        }
        return $displayer;
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
     * @param string $name
     * @param array $rule ['required' => true, 'message' => '必填']
     * @return $this
     */
    public function addValidatorRule($name, $rule)
    {
        if (!empty($this->validator[$name])) {
            $this->validator[$name] = array_merge($this->validator[$name], $rule);
        } else {
            $this->validator[$name] = $rule;
        }

        return $this;
    }

    /**
     * Undocumented function
     * @deprecated 
     * 
     * @param string $name  
     * @param string $rule 
     * @param string|int|boolean|mixed $val
     * 
     * @example location id,required,true
     * 
     * @return $this
     */
    public function addJqValidatorRule($name, $rule, $val = true)
    {
        $this->addValidatorRule($name, [$rule => $val]);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        ExtLoader::trigger('tpext_form_befor_render', $this);

        if (!$this->botttomButtonsCalled && empty($this->step)) {
            $this->bottomButtons(true);
        }

        if (!in_array(strtolower($this->method), ['get', 'post'])) {
            $this->hidden('_method')->value($this->method);
        }

        if ($this->step) {
            $this->step->setFormId($this->id);
        }

        foreach ($this->rows as $row) {
            $row->fill($this->data);

            if (!($row instanceof FRow)) {
                $row->beforRender();
                continue;
            }

            $row->beforRender();
        }

        $this->formScript();
        $this->eventScript();

        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param string|array $script
     * @return void
     */
    public function addValidateScript($script)
    {
        if (!is_array($script)) {
            $script = [$script];
        }
        $this->validateScripts = array_merge($this->validateScripts, $script);
    }

    /**
     * Undocumented function
     * 
     * @param string|array $script
     * @return void
     */
    public function addConvertScript($script)
    {
        if (!is_array($script)) {
            $script = [$script];
        }
        $this->convertScripts = array_merge($this->convertScripts, $script);
    }

    /**
     * Undocumented function
     * 
     * @return void
     */
    protected function eventScript()
    {
        $form = $this->id;

        $script = <<<EOT

        window.focus();

        $(document).bind('keyup', '#{$form} form', function(event) {
            if (event.keyCode === 13) {
                if($('#{$form} form textarea').length) {
                    return event.target.tagName.toLowerCase() == "textarea";
                }
                return {$form}Submit();
            }
        });

        $(document).bind('keyup', function(event) {
            if (parent && parent.layer && event.keyCode === 0x1B) {
                var index1 = parent.layer.getFrameIndex(window.name);
                if(index1) {
                    var index2 = layer.msg(__blang.bilder_confirm_close_this_window, {
                        time: 2000,
                        btn: [__blang.bilder_button_ok, __blang.bilder_button_cancel],
                        yes: function (params) {
                            layer.close(index2);
                            
                            parent.layer.close(index1);
                        }
                    });
                }
                return false; //阻止系统默认esc事件
            }
        });

EOT;
        Builder::getInstance()->addOnMountedScript($script);
    }

    protected function formScript()
    {
        $form = $this->id;

        $rules = json_encode($this->validator, JSON_UNESCAPED_UNICODE);
        $formData = json_encode($this->formData, JSON_UNESCAPED_UNICODE);
        $ajax = $this->ajax ? 'true' : 'false';
        $readonly = $this->readonly ? 'true' : 'false';
        $validateScripts = '';
        $convertScripts = '';
        $this->validateScripts = array_filter($this->validateScripts, 'strlen');
        $this->convertScripts = array_filter($this->convertScripts, 'strlen');

        if (count($this->validateScripts)) {
            $validateScripts = implode("\n\t\t\t", $this->validateScripts);
        }

        if (count($this->convertScripts)) {
            $convertScripts = implode("\n\t\t\t", $this->convertScripts);
        }

        $script = <<<EOT

    const {$form}Ref = ref(null);
    const {$form}Loading = ref(false);
    const {$form}Data = reactive({$formData});
    const {$form}Ajax = {$ajax};
    const {$form}Rules = {$rules};

    const {$form}Submit = () => {
        {$form}Ref.value.validate().then((errors) => {
            if (errors.length) {
                let firstError = errors[0];
                VxpMessage.warning(__blang.bilder_validate_form_failed + firstError);
                {$form}Data.__form_error____display__only = firstError;
                setTimeout(() =>{
                    {$form}Data.__form_error____display__only = '';
                }, 10000);
                return false;
            }
            {$validateScripts}
            {$form}Post();
        });
    };

    const {$form}Convert = ({$form}Data) => {
        {$convertScripts}
        return {$form}Data;
    };

    const {$form}Post = () => {
        let params = Object.assign(
            {
                __token__ : window.__token__,
            },
            {$form}Data,
        );
        params = Object.keys(params)
            .filter(key => !/\w+__display__only$/.test(key) && !/\w+__tmp$/.test(key) && !/\w+__thumbs$/.test(key))
            .reduce((acc, key) => {
                acc[key] = params[key];
                return acc;
            }, {});

        params = {$form}Convert(params);

        {$form}Loading.value = true;
        if(!{$form}Ajax) {
            let form = document.createElement('form');
            form.method = 'post';
            form.action = '{$this->action}' || location.href;

            function addInput(name, value) {
                if (Array.isArray(value)) {
                    value.forEach(function (item) {
                        addInput(name + '[]', item);
                    });
                    return;
                }
                if (typeof value === 'object') {
                    for (let k in value) {
                        addInput(name + '[' + k + ']', value[k]);
                    }
                    return;
                }
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }

            for (let key in params) {
                addInput(key, params[key]);
            }
            document.body.appendChild(form);
            form.submit();
            return false;
        }

        axios({
            method: 'post',
            url: '{$this->action}' || location.href,
            responseType: 'json',
            data: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            timeout: 60000,
        }).then(res => {
            {$form}Loading.value = false;
            let data = res.data || {};
            let success = data.code || data.status || 0;
            let status = success ? 'success' : 'error';
            let message = data.msg || data.message || (success ? __blang.bilder_save_succeeded : __blang.bilder_save_failed);

            if (data.layer_close) {
                closeLayer(message, status);
            } else if (data.layer_close_refresh) {
                closeLayerRefresh(message, status);
            } else if (data.layer_close_go) {
                closeLayerGo(message, status);
            } else if (data.url) {
                VxpNotice.open({
                    type: status,
                    content: message,
                    placement: 'top-right',
                    duration: 2000,
                });
                setTimeout(function () {
                    location.replace(data.url);
                }, (data.wait || 2 ) * 1000 );
            } else {
                VxpNotice.open({
                    type: status,
                    content: message,
                    placement: 'top-right',
                    duration: 2000,
                });
            }
            if (data.script || (data.data && data.data.script)) {
                let script = data.script || data.data.script;
                if ($('#script-div').length) {
                    $('#script-div').html(script);
                } else {
                    $('body').append(
                        '<div class="hidden" id="script-div">' + script + '</div>');
                }
            }
        }).catch(e => {
            {$form}Loading.value = false;
            console.log(e);
            VxpMessage.error(__blang.bilder_network_error + (e.message || JSON.stringify(e)));
        });
    };

    const {$form}Op = ref({
        'hide-label': true,
        'size': 'default',
        // 'disabled': {$readonly},
        'rules': {$rules},
        'gap' : [30, 0],
    });

EOT;

        Builder::getInstance()->addVueToken([
            "{$form}Ref",
            "{$form}Loading",
            "{$form}Op",
            "{$form}Data",
        ]);
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
        $template = Module::getInstance()->getViewsPath() . 'form.html';

        return $template;
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
     * @return string|View
     */
    public function render()
    {
        $viewshow = new View($this->getViewTemplate());

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
            'method' => strtoupper($this->method),
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'id' => $this->id,
            'readonly' => $this->readonly,
        ];

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

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            if (strstr($arguments[0], '[')) {
                $arguments[0] = str_replace(['[', ']'], ['.', ''], $arguments[0]);
            }

            $row = FRow::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : ($name == 'button' ? 1 : $this->defaultDisplayerColSize));

            if ($this->__fields__) {
                $this->__fields__->addRow($row);
            } else if ($this->__items__) {
                $this->__items__->addCol($arguments[0], $row);
            } else if ($this->__tabs__) {
                $this->__tabs__->addRow($row);
            } else {
                $this->rows[] = $row;
            }

            $row->setForm($this);

            $displayer = $row->$name($arguments[0], $count > 1 ? $arguments[1] : '');

            $row->setLabel($displayer->getLabel());

            if ($this->__when__) {
                $this->__when__->toggle($displayer);
            }

            if ($this->defaultDisplayerSize && !($displayer instanceof Button)) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            if ($this->__items__) {
                if (!($displayer instanceof Items)) {
                    $displayer->inTable();
                    $displayer->extKey($this->__items__->getId());
                }
                if ($displayer instanceof MultipleFile) { //表格中默使用btn控制
                    $displayer->showInput(false)->disableButtons();
                } else if ($displayer instanceof Fields) { //items的Fields
                    $displayer->getContent()->hasWrapper(false);
                }
            }

            $displayer->setFormMode('form');

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
        return Widget::makeWidget('Form', $arguments);
    }

    public function destroy()
    {
        $this->allContentsEnd();
        foreach ($this->rows as $row) {
            if ($row instanceof FRow) {
                $row->destroy();
            }
        }
        $this->tab = null;
        $this->step = null;
        $this->formData = null;
        $this->rows = null;
        $this->data = null;
    }
}
