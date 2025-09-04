<?php

namespace tpext\builder\table;

use think\facade\Session;
use tpext\builder\common\Module;
use tpext\builder\common\Builder;
use tpext\builder\common\Toolbar;

class MultipleToolbar extends Toolbar
{
    protected $useSearch = false;

    protected $btnSearch = null;

    protected $useExport = true;

    protected $useChooseColumns = ['*'];

    protected $btnExport = null;

    protected $tableCols = [];

    protected $actions = [];

    protected $tableId = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function tableId($val)
    {
        $this->tableId = $val;
        $this->extKey('tool' . $val);

        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param array $cols
     * @return $this
     */
    public function setTableCols($cols)
    {
        $this->tableCols = $cols;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function useLayerAll($val, $size = [])
    {
        foreach ($this->elms as $elm) {
            $elm->useLayer($val, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $barType
     * @return $this
     */
    public function created($barType = '')
    {
        parent::created();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useSearch($val = true)
    {
        $this->useSearch = $val;

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
        $this->useExport = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean|array|string $val 默认显示的字段，false则禁用
     * @return $this
     */
    public function useChooseColumns($val = ['*'])
    {
        if ($val === true) {
            $val = ['*'];
        } else if (empty($val)) {
            $val = [];
        } else if (is_string($val)) {
            $val = explode(',', $val);
        }

        $this->useChooseColumns = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getChooseColumns()
    {
        return is_array($this->useChooseColumns) ? $this->useChooseColumns : [];
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (empty($this->elms)) {
            $this->buttons();
        }

        if ($this->useChooseColumns) {
            $items = [];

            foreach ($this->tableCols as $col) {
                $name = $col->getName();
                $checked = $this->useChooseColumns[0] == '*' || in_array($name, $this->useChooseColumns);
                $items[$name] = [
                    'key' => $name,
                    'label' => preg_replace('/<[bh]r\s*\/?>/i', '', $col->getLabel()),
                    'icon' => $checked ? 'mdi-checkbox-marked-outline' : 'mdi-checkbox-blank-outline',
                    'url' => '#',
                    'attr' => '',
                    'class' => $checked ? 'checked' : '',
                ];
            }

            $this->btnChooseColumns($items);
        }

        if ($this->useExport && !$this->btnExport) {
            $this->btnExports();
        }

        if ($this->useSearch && !$this->btnSearch) {
            $this->btnToggleSearch();
        }

        foreach ($this->elms as $elm) {
            $elm->tableId($this->tableId);
            $elm->initLayer();
        }

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function buttons()
    {
        $this->btnAdd();
        $this->btnDelete();
        $this->btnRefresh();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnAdd($url = '', $label = '添加', $type = 'success', $icon = 'mdi-plus', $attr = '')
    {
        if (empty($url)) {
            $url = (string) url('add');
        }
        if ($label == '添加') {
            $label = __blang('bilder_action_add');
        }
        $action = 'add';
        if (isset($this->actions[$action])) {
            $action .= mt_rand(100, 999);
        }
        $this->actions[$action] = $action;
        $this->linkBtn($action, $label)->href($url)->icon($icon)->type($type)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDelete($postUrl = '', $label = '删除', $type = 'error', $icon = 'mdi-delete', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = (string) url('delete');
        }
        if ($label == '删除') {
            $label = __blang('bilder_action_delete');
        }
        $this->actions['delete'] = 'delete';
        $this->linkBtn('delete', $label)->postChecked($postUrl, $confirm)->type($type)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDisable($postUrl = '', $label = '禁用', $type = 'warning', $icon = 'mdi-block-helper', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = (string) url('enable', ['state' => 0]);
        }
        if ($label == '禁用') {
            $label = __blang('bilder_action_disable');
        }
        $this->actions['disable'] = 'disable';
        $this->linkBtn('disable', $label)->postChecked($postUrl, $confirm)->type($type)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnEnable($postUrl = '', $label = '启用', $type = 'success', $icon = 'mdi-check', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = (string) url('enable', ['state' => 1]);
        }
        if ($label == '启用') {
            $label = __blang('bilder_action_enable');
        }
        $this->actions['enable'] = 'enable';
        $this->linkBtn('enable', $label)->postChecked($postUrl, $confirm)->type($type)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $enableTitle
     * @param string $disableTitle
     * @return $this
     */
    public function btnEnableAndDisable($enableTitle = '启用', $disableTitle = '禁用')
    {
        if ($enableTitle == '启用') {
            $enableTitle = __blang('bilder_action_enable');
        }
        if ($disableTitle == '禁用') {
            $disableTitle = __blang('bilder_action_disable');
        }
        $this->btnEnable()->getCurrent()->attr('title="' . $enableTitle . '"')->label($enableTitle);
        $this->btnDisable()->getCurrent()->attr('title="' . $disableTitle . '"')->label($disableTitle);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnRefresh($label = '', $type = 'info', $icon = 'mdi-refresh', $attr = 'title="刷新"')
    {
        if ($attr == 'title="刷新"') {
            $attr = 'title="' . __blang('bilder_action_refresh') . '"';
        }
        $this->actions['refresh'] = 'refresh';
        $this->linkBtn('refresh', $label)->type($type)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnToggleSearch($label = '', $type = '', $icon = 'mdi-magnify', $attr = 'title="搜索"')
    {
        if ($attr == 'title="搜索"') {
            $attr = 'title="' . __blang('bilder_action_search') . '"';
        }
        $this->actions['toggle_search'] = 'toggle_search';
        $this->linkBtn('toggle_search', $label)->type($type)->icon($icon)->addAttr($attr);

        $this->btnSearch = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $afterSuccessUrl
     * @param string|array acceptedExts
     * @param array layerSize
     * @param int fileSize MB
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param string $driver
     * @return $this
     */
    public function btnImport($afterSuccessUrl = '', $acceptedExts = "rar,zip,doc,docx,xls,xlsx,ppt,pptx,pdf", $layerSize = ['800px', '550px'], $fileSize = '20', $label = '导入', $type = 'info', $icon = 'mdi-cloud-upload', $attr = 'title="上传文件"', $driver = '\\tpext\\builder\\logic\\LocalStorage')
    {
        if (empty($afterSuccessUrl)) {
            $afterSuccessUrl = (string) url('/admin/import/afterSuccess');
        }

        if (is_array($acceptedExts)) {
            $acceptedExts = implode(',', $acceptedExts);
        }

        $afterSuccessUrl = urlencode($afterSuccessUrl);

        $afterSuccessUrl = preg_replace('/(.+?)(\.html)?$/', '$1', $afterSuccessUrl);

        $importpagetoken = Session::has('importpagetoken') ? Session::get('importpagetoken') : md5('importpagetoken' . time() . uniqid());

        Session::set('importpagetoken', $importpagetoken);

        $driver = str_replace('\\', '-', $driver);

        $pagetoken = md5($importpagetoken . $acceptedExts . $fileSize);

        $url = (string) url(Module::getInstance()->getImportUrl()) . '?successUrl=' . $afterSuccessUrl . '&acceptedExts=' . $acceptedExts . '&fileSize=' . $fileSize . '&pageToken=' . $pagetoken . '&driver=' . $driver;

        if ($label == '导入') {
            $label = __blang('bilder_action_import');
        }
        if ($attr == 'title="上传文件"') {
            $attr = 'title="' . __blang('bilder_action_upload_file') . '"';
        }
        $this->actions['import'] = 'import';
        $this->linkBtn('import', $label)->useLayer(true, $layerSize)->href($url)->icon($icon)->type($type)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnExport($postUrl = '', $label = '导出', $type = '', $icon = 'mdi-export', $attr = 'title="导出"')
    {
        if (empty($postUrl)) {
            $postUrl = (string) url('export');
        }

        if (!Builder::checkUrl($postUrl)) {
            return $this;
        }
        if ($label == '导出') {
            $label = __blang('bilder_action_export');
        }
        if ($attr == 'title="导出"') {
            $attr = 'title="' . __blang('bilder_action_export') . '"';
        }
        $action = 'export';
        if (isset($this->actions[$action])) {
            $action .= mt_rand(100, 999);
        }
        $this->actions[$action] = $action;
        $this->linkBtn($action, $label)->type($type)->icon($icon)->href($postUrl);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items ['csv' => '导出csv', 'xls' => '导出xls', 'xlsx' => '导出xlsx']
     * @param string $postUrl
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnExports($items = [], $postUrl = '', $label = '导出', $type = '', $icon = 'mdi-export', $attr = 'title="导出"')
    {
        if (empty($postUrl)) {
            $postUrl = (string) url('export');
        }

        if (empty($items)) {
            $items = ['csv' => __blang('bilder_action_export_csv')];

            if (class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet') || class_exists('\\Vtiful\\Kernel\\Excel') || class_exists('\\PHPExcel')) {
                $items = array_merge($items, [
                    'xlsx' => __blang('bilder_action_export_xlsx'),
                ]);
            }
        }

        $this->btnExport = true;

        if (!Builder::checkUrl($postUrl)) {
            return $this;
        }
        if ($label == '导出') {
            $label = __blang('bilder_action_export');
        }
        if ($attr == 'title="导出"') {
            $attr = 'title="' . __blang('bilder_action_export') . '"';
        }
        $this->actions['exports'] = 'exports';
        $this->dropdownBtns('exports', $label)->items($items)->type($type)->icon($icon)->href($postUrl)->pullRight();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnChooseColumns($items, $label = '显示列', $class = 'btn-secondary', $icon = 'mdi-grid', $attr = 'title="选择要显示的列"')
    {
        if ($label == '显示列') {
            $label = __blang('bilder_action_columns');
        }
        if ($attr == 'title="选择要显示的列"') {
            $attr = 'title="' . __blang('bilder_action_choose_columns') . '"';
        }
        $this->dropdownBtns('choose_columns', $label)->items($items)->addClass($class)
            ->icon($icon)->addAttr($attr)->checkbox()->pullRight();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnLink($url, $label = '', $type = '', $icon = 'mdi-checkbox-marked-outline', $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', 'ac_$1', $url, -1, $count);

        if (!$count) {
            $action = 'ac_' . preg_replace('/\W/', '_', $url);
        }

        if (isset($this->actions[$action])) {
            $action .= mt_rand(100, 999);
        }

        $this->actions[$action] = $action;

        $this->linkBtn($action, $label)->href($url)->icon($icon)->type($type)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     *
     */
    public function btnPostChecked($url, $label = '', $type = '', $icon = 'mdi-checkbox-marked-outline', $attr = '', $confirm = true)
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', 'ac_$1', $url, -1, $count);

        if (!$count) {
            $action = 'ac_' . preg_replace('/\W/', '_', $url);
        }

        if (isset($this->actions[$action])) {
            $action .= mt_rand(100, 999);
        }

        $this->actions[$action] = $action;

        $this->linkBtn($action, $label)->postChecked($url, $confirm)->type($type)->icon($icon)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $type
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     *
     */
    public function btnOpenChecked($url, $label = '', $type = '', $icon = 'mdi-checkbox-marked-outline', $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', 'ac_$1', $url, -1, $count);

        if (!$count) {
            $action = 'ac_' . preg_replace('/\W/', '_', $url);
        }

        if (isset($this->actions[$action])) {
            $action .= mt_rand(100, 999);
        }

        $this->actions[$action] = $action;

        $this->linkBtn($action, $label)->openChecked($url)->type($type)->icon($icon)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function html($val)
    {
        parent::html($val);
        return $this;
    }

    /**
     * Undocumented function
     * 换行
     * @return $this
     */
    public function br()
    {
        parent::html('<br />');
        return $this;
    }
}
