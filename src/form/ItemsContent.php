<?php

namespace tpext\builder\form;

use think\Collection;
use think\helper\Arr;
use tpext\think\View;
use tpext\builder\form\FRow;
use tpext\builder\common\Form;
use tpext\builder\common\Module;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\displayer\Field;
use tpext\builder\table\Actionbar;
use tpext\builder\displayer\Fields;
use tpext\builder\displayer\MultipleFile;
use tpext\builder\displayer\DateTime;

class ItemsContent extends FWrapper
{
    use HasDom;

    protected $id = 'items';
    protected $view = 'itemscontent';
    protected $tableColumns = [];
    protected $dataList = [];
    protected $pk = 'id';

    protected $extKey = 'items';

    protected $readonly = false;

    /**
     * Undocumented variable
     * @var Field[]
     */
    protected $list = [];
    /**
     * Undocumented variable
     * @var FRow[] 
     */
    protected $cols = [];
    protected $data = [];
    protected $emptyText = '';
    protected $isInitData = false;
    protected $actionRowText = '';
    protected $canDelete = true;
    protected $canAdd = true;
    protected $canRecover = true;
    protected $name = '';
    protected $label = '';
    protected $template = [];
    protected $initScripts = [];
    protected $convertScripts = [];

    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    /**
     * Undocumented variable
     *
     * @var \Closure[]
     */
    protected $templateFieldCall = [];

    public function __construct()
    {
        $this->actionRowText = __blang('builder_action_operation');
        $this->emptyText = '<span>' . __blang('builder_no_relevant_data') . '</span>';
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public function name($val)
    {
        $this->name = $val;
        $this->id = 'items' . preg_replace('/\W/', '_', $val . $this->extKey);
    }

    /**
     * Undocumented function
     * 
     * @param string $val
     * @return void
     */
    public function label($val)
    {
        $this->label = strip_tags(str_replace(['<br>', '<br/>', '<br />', '<br >'], " | ", $val));
    }

    /**
     * Undocumented function
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
        $this->id = 'items' . preg_replace('/\W/', '_', $this->name . $this->extKey);
        return $this;
    }

    /**
     * Undocumented function
     * 主键, 默认 为 'id'
     * @param string $val
     * @return $this
     */
    public function pk($val)
    {
        $this->pk = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function actionRowText($val)
    {
        $this->actionRowText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param FRow|Field|Fillable $row
     * @return $this
     */
    public function addCol($name, $col)
    {
        $this->cols[$name] = $col;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FRow[]
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Undocumented function
     *
     * @param Form $val
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
     * @return Form
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
     * @param boolean $val
     * @return $this
     */
    public function canDelete($val)
    {
        $this->canDelete = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasAction()
    {
        return $this->canDelete || $this->canAdd;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canAdd($val)
    {
        $this->canAdd = $val;
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
        $this->canRecover = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
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
     * @return array|Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        foreach ($this->cols as $col) {
            $col->getDisplayer()->readonly($val);
        }
        $this->readonly = $val;
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
            if (!($col instanceof FRow)) {
                continue;
            }

            $col->getDisplayer()->clearScript();
        }
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function templateFieldCall($callback)
    {
        $this->templateFieldCall[] = $callback;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->initData();
        $this->tableScript();
        $this->initRowScript();
        $this->addValidateScript();
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @return void
     */
    protected function initData()
    {
        $this->dataList = [];
        $this->template = [
            '__pk__' => '__new__0',
            '__del__' => 0,
            '__can_delete__' => 1,
            '__field_info__' => [],
        ];
        $displayer = null;
        foreach ($this->cols as $col => $colunm) {
            if (!($colunm instanceof FRow)) {
                continue;
            }

            $displayer = $colunm->getDisplayer();
            $title = $displayer->getLabel();
            $params = array_merge($displayer->fieldInfo(), [
                'isInput' => $displayer->isInput(),
                'displayerType' => strtolower($displayer->getDisplayerType()),
                'required' => $displayer->isRequired(),
                'titleRaw' => $title, //用于header中显示html
                'wrapperStyle' => $colunm->getStyle(),
            ]);

            $width = $colunm->getStyleByName('width') ?: ($displayer->getStyleByName('width') ?: '0');
            if (strstr($width, '%')) {
                //已支持百分比
            } else {
                $width = (int) preg_replace('/\D/', '', $width) ?: ($col == $this->pk ? 60 : null);
                if (!is_null($width) && $displayer->isInput()) {
                    $width += 16;
                }
            }

            $minWidth = $colunm->getStyleByName('min-width') ?: ($displayer->getStyleByName('min-width') ?: '0');
            if (strstr($minWidth, '%')) {
                $minWidth = 60; //暂不支持百分比
            } else {
                $minWidth = (int) preg_replace('/\D/', '', $minWidth) ?: 60;
                if ($displayer->isInput()) {
                    $minWidth += 16;
                }
            }

            $maxWidth = $colunm->getStyleByName('max-width') ?: ($displayer->getStyleByName('max-width') ?: '0');
            if (strstr($maxWidth, '%')) {
                $maxWidth = null; //暂不支持百分比
            } else {
                $maxWidth = (int) preg_replace('/\D/', '', $maxWidth) ?: null;
                if (!is_null($maxWidth) && $displayer->isInput()) {
                    $maxWidth += 16;
                }
            }

            $this->tableColumns[$col] = [
                'text-align' => $displayer->getStyleByName('text-align') ?: ($colunm->getStyleByName('text-align') ?: 'center'),
                // 'header-align' => 'center',
                // 'fixed' => $col == $this->pk ? 'left' : 'right',
                'class' => $colunm->getClass(),
                'id-key' => $col,
                'sorter' => !$this->hasAction(),
                'width' => $width,
                'min-width' => $col == $this->pk ? 30 : $minWidth,
                'max-width' => $maxWidth,
                'ellipsis' => !$displayer->isInput(),
                'meta' => $params, //在表格事件中可获取到该参数
            ];

            $this->list[$col] = $displayer;
        }

        if (count($this->data)) {
            $fieldCols = null;
            $sDisplayer = null;
            $this->initScripts = [];
            $item = [];
            $filled = [];
            $name = '';
            $value = '';
            foreach ($this->data as $key => $row) {
                $item = ['__pk__' => $key, '__del__' => 0, '__can_delete__' => isset($row['__can_delete__']) ? $row['__can_delete__'] : 1];
                foreach ($this->cols as $col => $colunm) {
                    if (!($colunm instanceof FRow)) {
                        continue;
                    }

                    $displayer = $colunm->getDisplayer();
                    $name = $col;

                    //暂存属性，防止在 rendering 被修改
                    $readonly = $displayer->isReadonly();
                    $disabled = $displayer->isDisabled();

                    $displayer->clearScript()
                        ->lockValue(false)
                        ->value('')
                        ->fill($row)
                        ->beforRender();

                    $value = $displayer->renderValue();
                    Arr::set($item, $name, $value);
                    $item['__field_info__'][$name] = $displayer->fieldInfo();
                    if ($displayer instanceof MultipleFile) {
                        Arr::set($item, $name . '__thumbs', $displayer->thumbs());
                    } else if ($displayer instanceof DateTime) {
                        Arr::set($item, $name . '__tmp', $value);
                    }

                    //还原属性
                    $displayer->readonly($readonly);
                    $displayer->disabled($disabled);

                    if (
                        !empty($data['__readonly__fields__'])
                        && (in_array($col, $row['__readonly__fields__']) || $row['__readonly__fields__'][0] == '*')
                    ) {
                        $displayer->readonly();
                    }

                    if (
                        !empty($data['__disabled__fields__'])
                        && (in_array($col, $row['__disabled__fields__']) || $row['__disabled__fields__'][0] == '*')
                    ) {
                        $displayer->disabled();
                    }

                    if (!isset($filled[$name])) {
                        $filled[$name] = true;
                        $this->initScripts[] = $displayer->getInitRowScript();
                        if ($this->canAdd) {
                            //填充模板默认值
                            Arr::set($this->template, $name, $displayer->lockValue(false)->value('')->renderValue());
                            if ($displayer instanceof MultipleFile) {
                                Arr::set($this->template, $name . '__thumbs', $displayer->thumbs());
                            } else if ($displayer instanceof DateTime) {
                                Arr::set($this->template, $name . '__tmp', $value);
                            }
                            $this->template['__field_info__'][$name] = $displayer->fieldInfo();
                        }
                    }

                    if ($displayer instanceof Fields) {
                        $fieldCols = $displayer->getContent()->getRows();
                        foreach ($fieldCols as $sCol) {
                            if (!($sCol instanceof FRow)) {
                                continue;
                            }
                            $sDisplayer = $sCol->getDisplayer();
                            $name = $sCol->getName();
                            $sDisplayer->clearScript()
                                ->lockValue(false)
                                ->value('')
                                ->fill($row);

                            $value = $sDisplayer->renderValue();
                            Arr::set($item, $name, $value);
                            if ($sDisplayer instanceof MultipleFile) {
                                Arr::set($item, $name . '__thumbs', $sDisplayer->thumbs());
                            } else if ($displayer instanceof DateTime) {
                                Arr::set($item, $name . '__tmp', $value);
                            }
                            $item['__field_info__'][$name] = $sDisplayer->fieldInfo();
                            if (!isset($filled[$name])) {
                                $filled[$name] = true;
                                $this->initScripts[] = $displayer->getInitRowScript();
                                if ($this->canAdd) {
                                    Arr::set($this->template, $name, $sDisplayer->lockValue(false)->value('')->renderValue());
                                    $this->template['__field_info__'] = $item['__field_info__'];
                                }
                            }
                        }
                    }
                    $this->convertScripts = array_merge($this->convertScripts, $displayer->getConvertScript());
                }
                $this->dataList[] = $item;
            }
        } else {
            //填充模板默认值
            if ($this->canAdd) {
                foreach ($this->cols as $col => $colunm) {
                    if (!($colunm instanceof FRow)) {
                        continue;
                    }
                    $displayer = $colunm->getDisplayer();
                    $value = $displayer->lockValue(false)->value('')->beforRender()->renderValue();
                    Arr::set($this->template, $col, $value);
                    if ($displayer instanceof DateTime) {
                        Arr::set($this->template, $col . '__tmp', $value);
                    } else if ($displayer instanceof MultipleFile) {
                        Arr::set($this->template, $col . '__thumbs', $displayer->thumbs());
                    }
                    $this->template['__field_info__'][$col] = $displayer->fieldInfo();
                    $this->convertScripts = array_merge($this->convertScripts, $displayer->getConvertScript());
                }
            }
        }

        if ($this->canAdd) {
            $this->dataList[] = [
                '__pk__' => '__add__',
            ];
        }

        $this->isInitData = true;
    }

    /**
     * 在列表中时初始化脚本
     * @return void
     */
    public function initRowScript()
    {
        $this->initScripts = array_filter($this->initScripts, 'strlen');
        if (count($this->initScripts)) {
            $table = $this->id;
            $initScrpts = implode("\n\t\t\t", $this->initScripts);
            $script = <<<EOT

    {$table}InitData.value.forEach(row => {
        if(row.__pk__ != '__add__') {
            {$table}Errors.value[row.__pk__] = {};
            {$initScrpts}
        }
    });
EOT;
            Builder::getInstance()->addOnMountedScript($script);
        }
    }

    /**
     * Undocumented function
     * @return void
     */
    public function addValidateScript()
    {
        if ($this->readonly) {
            return;
        }
        $table = $this->id;
        $form = $this->form->getFormId();
        $label = $this->label;

        $script = <<<EOT
        for(let key in {$table}InitData.value) {
            if(key =='__add__') {
                continue;
            }
            let row = {$table}InitData.value[key];
            for(let field in row) {
                if(field=='__pk__' || field=='__del__' || !row.hasOwnProperty('__field_info__') || !row.__field_info__.hasOwnProperty(field) || row.__del__ == 1) {
                    // console.log('{$label} - ' + field + ' pass validate',row);
                    continue;    
                }
                if(!{$table}Columns.value[field]){
                    continue;
                }
                if({$table}Columns.value[field].meta.required && !row[field]) {
                    let firstError = '[{$label} - ' + {$table}Columns.value[field].title + ']' + __blang.builder_validate_required;
                    VxpMessage.warning(__blang.builder_validate_form_failed + firstError);

                    {$form}Data.__form_error____display__only = firstError;
                    setTimeout(() =>{
                        {$form}Data.__form_error____display__only = '';
                    }, 10000);
                    return false;
                }
            }
        }
EOT;
        $this->form->addValidateScript($script);
    }

    /**
     * Undocumented function
     * 
     * @return void
     */
    protected function tableScript()
    {
        $table = $this->id;
        $VModel = $this->getVModel();
        $tableColumns = json_encode($this->tableColumns, JSON_UNESCAPED_UNICODE);
        $initData = json_encode(array_values($this->dataList), JSON_UNESCAPED_UNICODE);
        $template = json_encode($this->template, JSON_UNESCAPED_UNICODE);
        $canRecover = $this->canRecover ? 'true' : 'false';

        $this->convertScripts = array_filter($this->convertScripts, 'strlen');
        $convertScripts = '';
        if (count($this->convertScripts)) {
            $convertScripts = implode("\n\t\t\t", $this->convertScripts);
        }

        $script = <<<EOT

    const {$table}Ref = ref(null);
    const {$table}Columns = ref({$tableColumns});
    const {$table}InitData = ref({$initData});
    const {$table}Errors = ref({});
    const {$table}ToolbarId = ref('{$table}-toolbar-' + (window.location.origin + window.location.pathname).replace(/\W/g, '_'));

    let {$table}NewIndex = 0;
    let {$table}CanRecover = {$canRecover};

    const {$table}DelBtnOp = ref({
        'size': 'small',
        'simple': true,
        'type': 'error',
    });

    const {$table}AddBtnOp = ref({
        'size': 'small',
        'simple': true,
        'type': 'success',
    });

    const {$table}Delete = (row) => {
        if(/^__new__\d+$/.test(row.__pk__)) {
            {$table}InitData.value = {$table}InitData.value.filter(x => x.__pk__ != row.__pk__);
        } else {
            if(!{$table}CanRecover) {
                VxpConfirm.open({
                    title : __blang.builder_operation_tips,
                    content: __blang.builder_confirm_to_do_operation + ' [' + __blang.builder_remove + '] ' + __blang.builder_action_operation + ' ?',
                    confirmText : __blang.builder_button_ok,
                    cancelText : __blang.builder_button_cancel,
                    confirmType: 'warning',
                }).then((res) => {
                    if(res) {
                        row.__del__ = 1;
                    }
                });
            }
            else{
                row.__del__ = 1;
            }
        }
    };

    const {$table}Recover = (row) => {
        row.__del__ = 0;
    };

    const {$table}Add = () => {
        {$table}InitData.value = {$table}InitData.value.filter(x => x.__pk__ != '__add__');
        {$table}InitData.value.push({$table}GetTemplate());
        {$table}InitData.value.push({__pk__ : '__add__'});
    };

    const {$table}GetTemplate = () => {
        let template = {$template};
        template.__pk__ = '__new__' + {$table}NewIndex;
        {$table}Errors.value[template.__pk__] = {};
        {$table}NewIndex += 1;
        return template;
    };

    const {$table}ToData = () => {
        let items = {};
        {$table}InitData.value.filter(x => x.__pk__ != '__add__').forEach(row => {
            for(let field in row) {
                if(field=='__pk__' || field=='__del__' || !row.hasOwnProperty('__field_info__') || !row.__field_info__.hasOwnProperty(field)) {
                    continue;    
                }
                if(!{$table}Columns.value[field]){
                    continue;
                }
                if({$table}Columns.value[field].meta.required && !row[field] && row.__del__ != 1) {
                    {$table}Errors.value[row.__pk__][field] = 'vxp-input--error';
                } else {
                    {$table}Errors.value[row.__pk__][field] = '';
                }
            }
            let data =  Object.keys(row)
                .filter(key => key != '__pk__' && key != '__field_info__' && key != '_\$index_')
                .reduce((acc, key) => {
                    acc[key] = row[key];
                    return acc;
                }, {});
            items[row.__pk__] = data;
        });
        {$VModel} = Object.keys(items).length === 0 ? {} : items;
    }

    const {$table}Convert = (rowData) => {
        let row = Object.keys(rowData)
            .filter(key => !/\w+__display__only$/.test(key) && !/\w+__tmp$/.test(key) && !/\w+__thumbs$/.test(key)
            && key != '__can_delete__'
            )
            .reduce((acc, key) => {
                acc[key] = rowData[key];
                return acc;
            }, {});
        {$convertScripts}
        return row;
    };

    const {$table}Data = computed(()=>{
        return {$table}CanRecover ? {$table}InitData.value : {$table}InitData.value.filter(x => x.__del__ != 1);
    });

    const {$table}Op = ref({
        'border': true,
        'stripe' : true,
        'highlight' : true,
        'key-config' : {
            'id' : '__pk__',
        },
        'single-sorter' : true,//设置后将限制表格只能有一列开启排序,
        'data': {$table}Data,
        'disabled-tree' : true,
        'use-x-bar' : true,
        'col-resizable' : 'responsive',
    });

EOT;
        Builder::getInstance()->addVueToken([
            "{$table}Ref",
            "{$table}Columns",
            "{$table}Op",
            "{$table}DelBtnOp",
            "{$table}AddBtnOp",
            "{$table}Delete",
            "{$table}Add",
            "{$table}Recover",
            "{$table}ToolbarId",
            "{$table}Errors",
        ]);

        Builder::getInstance()->addSetupScript($script);

        $script = <<<EOT

    //监听表格数据变化，绑定到表单数据
    let {$table}Timer = null;
    watch(
        {$table}InitData,
        (newValue, oldValue) => {
            if({$table}Timer) {
                clearTimeout({$table}Timer);
                {$table}Timer = null;
            }
            {$table}Timer = setTimeout(() => {
                {$table}ToData();
            }, 800);
        },
        {deep: true, immediate: true}
    );
    {$table}Timer = setTimeout(() => {
        {$table}ToData();
    }, 100);

EOT;
        Builder::getInstance()->addOnMountedScript($script);
    }

    /**
     * 字段的 v-model= 名称
     *
     * @return string
     */
    public function getVModel()
    {
        return $this->form->getFormId() . 'Data.' . $this->name;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'form' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        $vars = [
            'id' => $this->id,
            'name' => $this->name,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'cols' => $this->cols,
            'list' => $this->list,
            'emptyText' => $this->emptyText,
            'canDelete' => $this->canDelete,
            'actionRowText' => $this->actionRowText,
            'canAdd' => $this->canAdd,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->form, $name], $arguments);
    }

    public function destroy()
    {
        foreach ($this->cols as $col) {
            $col->destroy();
        }
        $this->cols = null;
        $this->list = null;
    }
}
