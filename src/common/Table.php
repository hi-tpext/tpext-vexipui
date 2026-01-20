<?php

namespace tpext\builder\common;

use think\Collection;
use think\helper\Arr;
use tpext\think\View;
use tpext\common\ExtLoader;
use tpext\builder\table\TEmpty;
use tpext\builder\table\TColumn;
use tpext\builder\traits\HasDom;
use tpext\builder\table\TWrapper;
use tpext\builder\displayer\Field;
use tpext\builder\table\Actionbar;
use tpext\builder\displayer\Fields;
use tpext\builder\inface\Renderable;
use tpext\builder\table\FieldsContent;
use tpext\builder\table\MultipleToolbar;
use tpext\builder\displayer\MultipleFile;

/**
 * Table class
 */
class Table extends TWrapper implements Renderable
{
    use HasDom;

    protected $js = [];
    protected $css = [];
    protected $id = 'theTable';
    protected $headTextAlign = 'center';
    protected $textAlign = 'center';
    protected $tableColumns = [];
    protected $dataList = [];
    /**
     * Undocumented variable
     * @var Field[]
     */
    protected $list = [];
    /**
     * Undocumented variable
     * @var TColumn[] 
     */
    protected $cols = [];
    protected $data = [];
    protected $pk = 'id';
    protected $actionbars = [];
    protected $checked = [];
    protected $useCheckbox = true;
    protected $pageSize = 0;
    protected $dataTotal = 0;
    protected $emptyText = '';
    protected $autoPost = [];
    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;
    /**
     * Undocumented variable
     *
     * @var MultipleToolbar
     */
    protected $toolbar = null;
    protected $useToolbar = true;
    protected $lockForExporting = false;
    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;
    protected $useActionbar = true;
    protected $actionRowText = '';
    protected $isInitData = false;
    protected $sortable = ['id'];
    protected $sortOrder = '';
    protected $partial = false;
    protected $delay = true; //延迟读取数据，调用fill()填充数据后取消延迟
    protected $convertScripts = [];

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
     * @var Search
     */
    protected $searchForm = null;
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $pagesizeDropdown = [];
    protected $usePagesizeDropdown = true;
    /**
     * Undocumented variable
     *
     * @var TEmpty
     */
    protected $tEmpty = null;
    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->emptyText = Module::config('table_empty_text');
        $this->actionRowText = __blang('bilder_action_operation');

        $this->tEmpty = new TEmpty;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param \tpext\builder\table\TColumn $col
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
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
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
    public function tableId($val)
    {
        $this->id = preg_replace('/\W/', '_', $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getTableId()
    {
        return $this->id;
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
     * Undocumented function
     * 
     * @param string $val left | center | right
     * @return $this
     */
    public function textAlign($val)
    {
        $this->textAlign = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param string $val left | center | right
     * @return $this
     */
    public function headTextAlign($val)
    {
        $this->headTextAlign = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function sortable($val)
    {
        if (!is_array($val)) {
            $val = explode(',', $val);
        }

        $this->sortable = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function checked($val)
    {
        $this->checked = is_array($val) ? $val : explode(',', $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function emptyText($val)
    {
        $this->emptyText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection|\IteratorAggregate $data
     * @return $this
     */
    public function data($data = [])
    {
        $this->delay = false;
        $this->data = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param bool $val
     * @return $this
     */
    public function lockForExporting($val = true)
    {
        $this->lockForExporting = $val;

        if ($this->toolbar) {
            $this->toolbar->lockForExporting($val);
        }
        if ($this->actionbar) {
            $this->actionbar->lockForExporting($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection|\IteratorAggregate $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->delay = false;
        if (empty($data)) {
            return $this;
        }

        $this->data = $data;
        if (count($data) > 0 && empty($this->cols)) {
            $cols = [];
            $first = $data[0];
            if (is_object($first) && method_exists($first, 'toArray')) {
                $first = $first->toArray();
            }
            $cols = array_keys($first);
            foreach ($cols as $col) {
                $this->show($col, ucfirst($col));
            }
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function sortOrder($val)
    {
        $this->sortOrder = $val;
        return $this;
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

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getChooseColumns()
    {
        return $this->getToolbar()->getChooseColumns();
    }

    /**
     * Undocumented function
     *
     * @param int $dataTotal
     * @param int $pageSize
     * @return $this
     */
    public function paginator($dataTotal, $pageSize = 10)
    {
        if (!$pageSize) {
            $pageSize = 10;
        }

        $this->dataTotal = $dataTotal;
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * 获取一个toolbar
     *
     * @return MultipleToolbar
     */
    public function getToolbar()
    {
        if (empty($this->toolbar)) {
            $this->toolbar = Widget::makeWidget('MultipleToolbar');
            $this->toolbar->tableId($this->id);
        }

        return $this->toolbar;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useToolbar($val)
    {
        $this->useToolbar = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useActionbar($val)
    {
        $this->useActionbar = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @param boolean $val
     * @return $this
     */
    public function useCheckbox($val)
    {
        $this->useCheckbox = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useExport($val = true)
    {
        $this->getToolbar()->useExport($val);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean|array|string $val 默认显示的字段，false则禁用
     * @return $this
     */
    public function useChooseColumns($val = true)
    {
        $this->getToolbar()->useChooseColumns($val);

        return $this;
    }

    /**
     * 弃用，使用｀useExport｀代替
     * @deprecated 1.8.93
     * @param boolean $val
     * @return $this
     */
    public function hasExport($val = true)
    {
        $this->getToolbar()->useExport($val);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return bool
     */
    public function isLockForExporting()
    {
        return $this->lockForExporting;
    }

    /**
     * 获取一个actionbar
     *
     * @return Actionbar
     */
    public function getActionbar()
    {
        if (empty($this->actionbar)) {
            $this->actionbar = Widget::makeWidget('Actionbar');
            $this->actionbar->tableId($this->id);
        }

        return $this->actionbar;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    protected function actionRowText($val)
    {
        $this->actionRowText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|boolean $items
     * @return $this
     */
    public function pagesizeDropdown($items)
    {
        if ($items === false) {
            $this->usePagesizeDropdown = false;
            return $this;
        }

        $this->pagesizeDropdown = $items;

        return $this;
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
     * 获取一个搜索
     *
     * @return Search
     */
    public function getSearch()
    {
        if (empty($this->searchForm)) {
            $this->searchForm = Widget::makeWidget('Search');
            $this->searchForm->setTableId($this->id);
        }
        return $this->searchForm;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        ExtLoader::trigger('tpext_table_befor_render', $this);

        $this->initData();

        if ($this->partial) {
            //ajax return json data
        } else {
            //get render table html 
            Builder::getInstance()->addJs($this->js);
            Builder::getInstance()->addCss($this->css);

            $emptyForm = empty($this->searchForm);

            if ($emptyForm) {
                $this->getSearch();
            }

            $this->tableScript();

            if ($this->useToolbar) {
                $toolbar = $this->getToolbar();
                $toolbar->useSearch(!$emptyForm && !$this->searchForm->empty());
                $toolbar->setTableCols($this->cols);
                $toolbar->beforRender();
            }

            if ($this->useActionbar && $this->actionbar) {
                $this->actionbar->beforRender();
            }

            if ($emptyForm) {
                $this->searchForm->addClass('form-empty');
            }

            $this->searchForm->beforRender();

            if ($this->addTop) {
                $this->addTop->beforRender();
            }

            if ($this->addBottom) {
                $this->addBottom->beforRender();
            }
        }

        return $this;
    }

    protected function tableScript()
    {
        if ($this->usePagesizeDropdown && $this->pageSize && empty($this->pagesizeDropdown)) {
            $this->pagesizeDropdown = array_merge([$this->pageSize], [6, 10, 14, 20, 30, 40, 50, 60, 90, 120, 200, 350, 500, 800, 1000]);
        }

        $table = $this->id;
        $search = $this->searchForm->getFormId();
        $this->pagesizeDropdown = array_unique(array_values($this->pagesizeDropdown));
        sort($this->pagesizeDropdown);
        $tableColumns = json_encode($this->tableColumns, JSON_UNESCAPED_UNICODE);
        $pagesizeDropdown = json_encode($this->pagesizeDropdown, JSON_UNESCAPED_UNICODE);
        $initData = json_encode(array_values($this->dataList), JSON_UNESCAPED_UNICODE);
        $useChooseColumns = $this->getToolbar()->getChooseColumns() === false ? '[]'
            : json_encode($this->getToolbar()->getChooseColumns(), JSON_UNESCAPED_UNICODE);
        $useCheckbox = $this->useCheckbox && $this->useToolbar ? 'true' : 'false';

        $script = <<<EOT

    const {$table}Ref = ref(null);
    const {$table}Loading = ref(true);
    const {$table}SelectedSize = ref(0);
    const {$table}MultipleToolbarDisabled = ref(true);
    const {$table}UseChooseColumns = ref({$useChooseColumns});
    const {$table}Columns = ref({$tableColumns});
    const {$table}ActiveRow = ref({__pk__ : null});
    const {$table}ActiveRowChanged = {};
    const {$table}ActivePage = ref(1);
    const {$table}PageSize = ref({$this->pageSize});

    let {$table}ActiveRowTurn = false;
    let {$table}Data = ref({$initData});
    const {$table}useCheckbox = {$useCheckbox};

    const {$table}PagerConfig = ref({
        'size-options': {$pagesizeDropdown},
        'total': {$this->dataTotal},
        'size': 'small',
        'plugins': ['total', 'jump', 'size'],
    });

    let {$table}Sort = '';

    const {$table}Refresh = (resetPage) => {
        if(resetPage) {
            {$table}ActivePage.value = 1;
        }
        {$table}Ref.value.clearSelected();//清除已选
        {$table}Loading.value = true;
        {$table}GetData();
    };

    const {$table}Change = (resetPage) => {
        {$table}Refresh();
    };

    const {$table}PageSizeChange = (resetPage) => {
        {$table}Refresh(true);
    };

    const {$table}GetData = () => {
        let params = Object.assign(
            {   __fetch_data__ : 'y',
                __page__ : {$table}ActivePage.value,
                __pagesize__ : {$table}PageSize.value,
            },
            {$search}Data,
            {$table}Sort ? { __sort__ : {$table}Sort } : null,
        );

        params = Object.keys(params)
            .filter(key => !/^__button\d+$/.test(key) && key != 'search_buttons' && !/\w+__tmp$/.test(key))
            .reduce((acc, key) => {
                acc[key] = params[key];
                return acc;
            }, {});

        params = {$search}Convert(params);

        axios({
            method: 'get',
            url: location.href,
            responseType: 'json',
            params: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            timeout: 10000,
        }).then(res => {
            {$table}Loading.value = false;
            {$table}MultipleToolbarDisabled.value = true;//重置多选工具栏状态
            let data = res.data || {};
            {$table}ActiveRow.value = {__pk__ : null};
            {$table}Data.value = data.list;
            {$table}PagerConfig.value.total = data.total;
            // {$table}Ref.value.refresh();
        }).catch(e => {
            {$table}Loading.value = false;
            console.log(e);
            VxpMessage.error(__blang.bilder_network_error + (e.message || JSON.stringify(e)));
        });
    };

    const {$table}ExportData = (url, type) => {
        pageLoading.value = true;
        let {$table}Selected = {$table}Ref.value.getSelected();
        let ids = {$table}Selected.map(x => { return x.__pk__  }).join(',');
        let params = Object.assign(
            {   __file_type__ : type,
                __ids__ : ids,
                __columns__ : {$table}UseChooseColumns.value.join(','),
            },
            {$table}Sort ? { __sort__ : {$table}Sort } : null,
        );

        params = {$search}Convert(params);

        axios({
            method: 'get',
            url: url,
            responseType: 'json',
            params: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            timeout: 10000,
        }).then(res => {
            pageLoading.value = false;
            let data = res.data || {};
            if(data.code) {
                if(data.open_url) {
                    //数据太多，打开页面，分页处理
                    layer.open({
                        type: 2,
                        title: __blang.bilder_generating_data,
                        scrollbar: false,
                        area: ['400px','150px'],
                        content: data.open_url
                    });
                } else {
                    //一次性生成文件，并返回下载链接
                    var filename = data.data.replace(/.+?([^\/]+)$/, '$1');
                    layer.open({
                        type: 1,
                        title: __blang.bilder_download_file,
                        shadeClose: false,
                        area: ['400px','150px'],
                        content: '<div class="vxp-alert vxp-alert-vars vxp-alert--success" role="alert" style="widht:94%;margin:2%;flex-direction: column;align-items: start;"><p>' + __blang.bilder_file_has_been_generated + '</p><a onclick="layer.closeAll();" target="_blank" href="' + data.data + '">' + filename + '</a></div>',
                    });
                }
            } else {
                VxpConfirm.open({
                    content: __blang.bilder_operation_failed + data.msg,
                    cancelable: false
                });
            }
        }).catch(e => {
            pageLoading.value = false;
            console.log(e);
            VxpMessage.error(__blang.bilder_network_error + (e.message || JSON.stringify(e)));
        });
    };
    
    const {$table}SendData = (url, params, refresh) => {
        params = Object.assign(
            {
                __token__ : window.__token__,
                _method : /.+?\/(?:destroy|delete|remove|del)(?:\.\w+)?$/.test(url) ? "delete" : "patch"
            },
            params,
        );

        {$table}Loading.value = true;
        axios({
            method: 'post',
            url: url,
            responseType: 'json',
            data: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            timeout: 60000,
        }).then(res => {
            {$table}Loading.value = false;
            let data = res.data || {};
            if (data.__token__) {
                window.__token__ = data.__token__;
            }
            if (data.status || data.code) {
                VxpNotice.open({
                    type: 'success',
                    content: data.msg || data.message || __blang.bilder_operation_succeeded,
                    placement: 'top-right',
                    duration: 2000,
                });
                if (refresh) {
                    {$table}Refresh();
                }
            } else {
                VxpNotice.open({
                    type: 'error',
                    content: data.msg || data.message || __blang.bilder_operation_failed,
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
            {$table}Loading.value = false;
            console.log(e);
            VxpMessage.error(__blang.bilder_network_error + (e.message || JSON.stringify(e)));
        });
    };

    const {$table}CellClick = ({ row, rowIndex, column, columnIndex }) => {
        if (!{$table}useCheckbox || column.key == '__action__' || (column.meta && column.meta.isInput)) {
            return;
        }
        {$table}Ref.value.selectRow(row);
        {$table}SelectChange();
    };

    const {$table}CellDblClick = ({ row, rowIndex, column, columnIndex }) => {
        if (column.key == '__action__' || (column.meta && column.meta.isInput)) {
            return;
        }
        let dbl_click = null, edit_click = null, view_click = null, link_click = null;
        let btn = null;
        for(let k in row.__action__) {
            btn = row.__action__[k];
            if(btn.hidden || btn.disabled || !btn.href) {
                continue;
            }
            if(btn.dbl_click) {
                dbl_click = btn;
            }
            if(btn.name == 'edit') {
                edit_click = btn;
            }
            if(btn.name == 'view') {
                view_click = btn;
            }
            if(!link_click && btn.href) {
                link_click = btn;
            }
        }

        btn = null;
        if(dbl_click) {
            btn = dbl_click;
        } else if(edit_click) {
            btn = edit_click;
        } else if(view_click) {
            btn = view_click;
        } else if(link_click) {
            btn = link_click;
        }

        if(btn) {
            window.refreshTable = () => {
                {$table}Refresh();
            };
            layerOpenLink(btn.href, btn.layer_title, btn.layer_size);
        }
    };

    const {$table}GetCheckedRows = () => {
        return {$table}Ref.value.getSelected();
    };

    const {$table}SelectChange = () => {
        let data = {$table}Ref.value.getSelected();
        {$table}SelectedSize.value = data.length;
        {$table}MultipleToolbarDisabled.value = data.length == 0;
    };

    const {$table}SelectAll = (checked, partial) => {
        let data = {$table}Ref.value.getSelected();
        {$table}SelectedSize.value = data.length;
        {$table}MultipleToolbarDisabled.value = !checked;
    };

    const {$table}RowEnter = ({ row, index }) => {
        if(!{$table}ActiveRow.value || {$table}ActiveRow.value.__pk__ !== row.__pk__) {
            {$table}ActiveRowTurn = true;
            {$table}ActiveRow.value = row;
            setTimeout(() => {
                {$table}ActiveRowTurn = false;
            }, 20);
        }
    };

    const {$table}RowSort = (profiles, sortedRow) => {
        let sortBy = profiles[0];
        if(profiles.length) {
            {$table}Sort = sortBy.key + ' ' + sortBy.type;
        } else{
            {$table}Sort ='';
        }
        {$table}GetData();
    };

    const {$table}Op = ref({
        'border' : true,
        'stripe' : true,
        'highlight' : true,
        'key-config' : {
            'id' : '__pk__',
        },
        'width' : '100%',
        'min-height' : 100,
        'row-style' : 'min-height:41px',
        'single-sorter' : true, //设置后将限制表格只能有一列开启排序,
        'disabled-tree' : true,
        'use-x-bar' : true,
        'col-resizable' : 'responsive',
        'custom-sorter' : true, //设置是否为自定义排序，开启后仅派发事件而不会进行内部排序
    });

EOT;

        Builder::getInstance()->addVueToken([
            "{$table}Ref",
            "{$table}Columns",
            "{$table}Op",
            "{$table}UseChooseColumns",
            "{$table}Data",
            "{$table}MultipleToolbarDisabled",
            "{$table}PagerConfig",
            "{$table}ActivePage",
            "{$table}PageSize",
            "{$table}Loading",
            //events
            "{$table}Change",
            "{$table}PageSizeChange",
            "{$table}CellClick",
            "{$table}CellDblClick",
            "{$table}SelectChange",
            "{$table}SelectAll",
            "{$table}RowEnter",
            "{$table}RowSort"
        ]);

        Builder::getInstance()->addSetupScript($script);

        if (count($this->autoPost)) {

            $scripts = [];

            $this->convertScripts = array_filter($this->convertScripts, 'strlen');
            $convertScripts = '';
            if (count($this->convertScripts)) {
                $convertScripts = implode("\n\t\t\t", $this->convertScripts);
            }

            $scripts[] = <<<EOT

        const {$table}Convert = (row) => {
            {$convertScripts}
            return row;
        };
EOT;

            foreach ($this->autoPost as $fieldName => $val) {
                $eventKey = $table . preg_replace('/\W/', '_', $fieldName) . 'Change';
                $url = $val['url'];
                $refresh = $val['refresh'] ? 'true' : 'false';
                $isText = in_array($val['displayerType'], ['Text', 'Textarea', 'Password']) ? 'true' : 'false';
                $delay = in_array($val['displayerType'], ['Number']) ?
                    1000 : (in_array($val['displayerType'], ['RangeSlider', 'Checkbox', 'MultipleSelect']) ? 700 : 300); //多选防抖时间长一点

                $scripts[] = <<<EOT

        let {$eventKey}Timer = null;
        watch(
            () => {$table}ActiveRow.value.{$fieldName},
            (newValue, oldValue) => {
                if(!{$table}ActiveRowTurn) {
                    let id = {$table}ActiveRow.value.__pk__;
                    if(!{$table}ActiveRow.value || !{$table}ActiveRow.value.__pk__) {
                        return;
                    }
                    let rowData = {$table}Convert({ {$fieldName} : newValue });
                    let value = rowData.{$fieldName};
                    value = Array.isArray(value) ? value.join(',') : value;
                    {$table}ActiveRowChanged['{$fieldName}'] = value;
                    if($isText) {
                        return;
                    }
                    if({$eventKey}Timer) {
                        clearTimeout({$eventKey}Timer);
                        {$eventKey}Timer = null;
                    }
                    {$eventKey}Timer = setTimeout(() => {
                        let params = {
                            id: id,
                            name: '{$fieldName}',
                            value: value,
                        };
                        {$table}SendData('{$url}', params, $refresh);
                    }, {$delay});
                }
            }
        );

EOT;
            }

            Builder::getInstance()->addSetupScript(implode('', $scripts));
        }

        $delayLoad = $this->delay ? 'true' : 'false';

        $script = <<<EOT
        
        if({$delayLoad}) {
            {$table}GetData();
        } else {
            {$table}Loading.value = false;
        }
        
EOT;

        Builder::getInstance()->addOnMountedScript($script);
    }

    protected function initData()
    {
        ExtLoader::trigger('tpext_table_init_data', $this);

        if (!$this->dataTotal) {
            $this->dataTotal = count($this->data);
        }
        if (!$this->pageSize) {
            $this->pageSize = $this->dataTotal;
        }
        if ($this->dataTotal <= 6) {
            $this->usePagesizeDropdown = false;
        }

        if (!$this->pk) {
            $this->pk = '__pk__';
        }

        $this->dataList = [];
        $actionbar = $this->getActionbar();
        $actionbar->pk($this->pk);
        $displayer = null;

        if ($this->partial) {
            $colAttr = [];
            foreach ($this->cols as $col => $colunm) {
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $colAttr = $colunm->getColAttr();
                if ($colAttr['sortable']) {
                    $this->sortable[] = $col;
                }
            }
        } else {
            $colAttr = [];
            $this->tableColumns = [];
            $sortKey = '';
            if ($this->sortOrder) {
                $arr = explode(' ', $this->sortOrder);
                if (count($arr) == 2) {
                    $sortKey = $arr[0];
                }
            }

            foreach ($this->cols as $col => $colunm) {
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $colAttr = $colunm->getColAttr();

                if ($colAttr['sortable']) {
                    $this->sortable[] = $col;
                }

                $displayer = $colunm->getDisplayer();
                $title = $displayer->getLabel();
                $params = array_merge($displayer->fieldInfo(), [
                    'isInput' => $displayer->isInput(),
                    'displayerType' => strtolower($displayer->getDisplayerType()),
                    'titleRaw' => $title, //用于header中显示html
                    'wrapperStyle' => $colunm->getStyle(),
                ]);

                $width = $colAttr['width'] ?: ($colunm->getStyleByName('width') ?: ($displayer->getStyleByName('width') ?: '0'));
                if (strstr($width, '%')) {
                    //已支持百分比
                } else {
                    $width = (int) preg_replace('/\D/', '', $width) ?: ($col == $this->pk ? 60 : null);
                    if (!is_null($width) && $displayer->isInput()) {
                        $width += 16;
                    }
                }

                $minWidth = $colAttr['min-width'] ?: ($colunm->getStyleByName('min-width') ?: ($displayer->getStyleByName('min-width') ?: '0'));
                if (strstr($minWidth, '%')) {
                    $minWidth = 60; //暂不支持百分比
                } else {
                    $minWidth = (int) preg_replace('/\D/', '', $minWidth) ?: 60;
                    if ($displayer->isInput()) {
                        $minWidth += 16;
                    }
                }

                $maxWidth = $colAttr['max-width'] ?: ($colunm->getStyleByName('max-width') ?: ($displayer->getStyleByName('max-width') ?: '0'));
                if (strstr($maxWidth, '%')) {
                    $maxWidth = null; //暂不支持百分比
                } else {
                    $maxWidth = (int) preg_replace('/\D/', '', $maxWidth) ?: null;
                    if (!is_null($maxWidth) && $displayer->isInput()) {
                        $maxWidth += 16;
                    }
                }

                $this->tableColumns[$col] = [
                    'text-align' => $colAttr['align'] ?: ($displayer->getStyleByName('text-align') ?: ($colunm->getStyleByName('text-align') ?: $this->textAlign)),
                    // 'header-align' => $colAttr['header-align'] ?: $this->headTextAlign,
                    // 'fixed' => $col == $this->pk ? 'left' : 'right',
                    'class' => $colunm->getClass(),
                    'id-key' => $col,
                    'sorter' => $colAttr['sortable'] || $col == $sortKey || in_array($col, $this->sortable),
                    'width' => $width,
                    'min-width' => $col == $this->pk ? 30 : $minWidth,
                    'max-width' => $maxWidth,
                    // 'visible' => $colAttr['hidden'] ? false : ($useChooseColumns && ($useChooseColumns[0] == '*' || in_array($col, $useChooseColumns))),
                    'meta' => $params, //在表格事件中可获取到该参数
                ];

                $this->list[$col] = $displayer;
            }
        }

        if (count($this->data)) {
            $fieldCols = null;
            $sDisplayer = null;
            foreach ($this->data as $key => $row) {
                if (!isset($row[$this->pk])) {
                    $row[$this->pk] = 'row_' . $key;
                }
                $this->dataList[$key] = [];
                $this->dataList[$key]['__pk__'] = $row[$this->pk];
                foreach ($this->cols as $col => $colunm) {
                    if (!($colunm instanceof TColumn)) {
                        continue;
                    }
                    $displayer = $colunm->getDisplayer();
                    $displayer->clearScript()
                        ->lockValue(false)
                        ->value('')
                        ->fill($row)
                        ->beforRender();
                    Arr::set($this->dataList[$key], $col, $displayer->renderValue());
                    $this->dataList[$key]['__field_info__'][$col] = $displayer->fieldInfo();
                    if ($displayer instanceof MultipleFile) {
                        Arr::set($this->dataList[$key], $col . '__thumbs', $displayer->thumbs());
                    }

                    if ($displayer instanceof Fields) {
                        $fieldCols = $displayer->getContent()->getCols();
                        foreach ($fieldCols as $sCol) {
                            if (!($sCol instanceof TColumn)) {
                                continue;
                            }
                            $sDisplayer = $sCol->getDisplayer();
                            $sDisplayer->clearScript()
                                ->lockValue(false)
                                ->value('')
                                ->fill($row)
                                ->beforRender();
                            Arr::set($this->dataList[$key], $sDisplayer->getName(), $sDisplayer->renderValue());
                            $this->dataList[$key]['__field_info__'][$sDisplayer->getName()] = $sDisplayer->fieldInfo();
                            if ($sDisplayer instanceof MultipleFile) {
                                Arr::set($this->dataList[$key], $sDisplayer->getName() . '__thumbs', $sDisplayer->thumbs());
                            }

                            $sDisplayer->fill([]);
                        }
                    }

                    $displayer->fill([]);
                }

                if ($this->useActionbar) {
                    $config = $actionbar->rowData($row)->getActionConfig();
                    foreach ($config as $k => $c) {
                        foreach ($c as $e => $v) {
                            $this->dataList[$key]['__action__'][$k][$e] = $v;
                        }
                    }
                }
            }
        } else {
            foreach ($this->cols as $col => $colunm) {
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $colunm->getDisplayer()->value('')->beforRender();
            }
            if ($this->useActionbar) {
                $actionbar->rowData([])->getActionConfig();
            }
        }

        $this->isInitData = true;
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
            $this->addTop->class('table-top');
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
            $this->addBottom->class('table-bottom');
        }

        return $this->addBottom;
    }

    /**
     * Undocumented function
     * 
     * @param mixed $val
     * @return $this
     */
    public function addAutoPost($name, $val = [])
    {
        if (strstr($name, '[')) {
            $name = str_replace(['[', ']'], ['.', ''], $name);
        }
        $this->autoPost[$name] = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @param string $name
     * 
     * @return FieldsContent
     */
    public function createFields()
    {
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setTable($this);
        return $this->__fields__;
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
     * @return string
     */
    public function getViewTemplate()
    {
        $template = Module::getInstance()->getViewsPath() . 'table.html';

        return $template;
    }

    /**
     * Undocumented function
     *
     * @return string|View|mixed
     */
    public function render()
    {
        if ($this->lockForExporting) {
            return 'lockForExporting';
        }

        if (!$this->isInitData) {
            $this->initData();
        }

        if ($this->partial) {
            if (ob_get_level()) {
                ob_clean();
            }
            return json(['list' => array_values($this->dataList), 'total' => $this->dataTotal, 'pageSize' => $this->pageSize]);
        }

        $viewshow = new View($this->getViewTemplate());

        $vars = [
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'emptyText' => $this->emptyText,
            'useCheckbox' => $this->useCheckbox && $this->useToolbar,
            'name' => time() . mt_rand(1000, 9999),
            'id' => $this->id,
            'toolbar' => $this->useToolbar && !$this->partial ? $this->toolbar : null,
            'searchForm' => !$this->partial ? $this->searchForm : null,
            'actionRowText' => $this->actionRowText,
            'actionRowWidth' => $this->actionbar ? $this->actionbar->getActionWidth() : '',
            'useActionbar' => $this->useActionbar && $this->actionbar,
            'actionbar' => $this->actionbar,
            'addTop' => $this->addTop,
            'addBottom' => $this->addBottom,
            'list' => $this->list,
        ];
        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        if ($this->lockForExporting) {
            return $this->tEmpty;
        }

        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            if (strstr($arguments[0], '[')) {
                $arguments[0] = str_replace(['[', ']'], ['.', ''], $arguments[0]);
            }

            $col = TColumn::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0);

            $col->setTable($this);

            if ($this->__fields__) {
                $this->__fields__->addCol($col);
            } else {
                $this->cols[$arguments[0]] = $col;
            }

            $displayer = $col->$name($arguments[0], $count > 1 ? $arguments[1] : '');
            $col->setLabel($displayer->getLabel());

            if ($displayer instanceof MultipleFile) { //表格中默禁止上传，使用btn控制，如确实需要上传，调用canUpload(true)
                $displayer->canUpload(false)->showInput(false)->disableButtons();
            }

            $displayer->showLabel(false);
            $displayer->inTable();
            $displayer->setFormMode('table');

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
        return Widget::makeWidget('Table', $arguments);
    }

    public function destroy()
    {
        $this->__fields__ = null;
        $this->toolbar = null;
        $this->actionbar = null;
        $this->pagesizeDropdown = null;
        foreach ($this->cols as $col) {
            $col->destroy();
        }
        $this->cols = null;
        $this->data = null;
        $this->tEmpty = null;
        if ($this->searchForm) {
            $this->searchForm->destroy();
            $this->searchForm = null;
        }
        if ($this->addTop) {
            $this->addTop->destroy();
            $this->addTop = null;
        }
        if ($this->addBottom) {
            $this->addBottom->destroy();
            $this->addBottom = null;
        }
    }
}
