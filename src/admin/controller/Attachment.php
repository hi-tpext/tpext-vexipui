<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use think\facade\Session;
use tpext\builder\common\Module;
use tpext\builder\traits\actions\HasBase;
use tpext\builder\traits\actions\HasIndex;
use tpext\builder\traits\actions\HasAutopost;
use tpext\builder\common\model\Attachment as AttachmentModel;

/**
 * Undocumented class
 * @title 文件管理
 */
class Attachment extends Controller
{
    use HasBase;
    use HasIndex;
    use HasAutopost;

    /**
     * Undocumented variable
     *
     * @var AttachmentModel
     */
    protected $dataModel;

    protected function initialize()
    {
        $this->dataModel = new AttachmentModel;

        $this->pageTitle = __blang('builder_attachment_manage');
        $this->postAllowFields = ['name'];
        $this->pagesize = 8;
    }

    protected function filterWhere()
    {
        $searchData = request()->get();

        $where = [];

        $admin = Session::get('admin_user');

        if ($admin['role_id'] != 1) {
            $where[] = ['admin_id', '=', $admin['id']];
        }

        if (!empty($searchData['name'])) {
            $where[] = ['name', 'like', '%' . $searchData['name'] . '%'];
        }

        if (!empty($searchData['url'])) {
            $where[] = ['url', 'like', '%' . $searchData['url'] . '%'];
        }

        $ext = input('ext');

        if ($ext) {
            $where[] = ['suffix', 'in', explode(',', $ext)];
        }

        if (!empty($searchData['suffix'])) {
            $where[] = ['suffix', 'in', $searchData['suffix']];
        }

        return $where;
    }

    /**
     * 构建搜索
     *
     * @return void
     */
    protected function buildSearch()
    {
        $search = $this->search;

        $search->text('name', __blang('builder_attachment_name'), '6 col-xs-6')->maxlength(55);
        $search->text('url', __blang('builder_attachment_url'), '6 col-xs-6')->maxlength(200);

        $exts = [];
        $arr = [];

        $ext = input('ext');
        if ($ext) {
            $arr = explode(',', $ext);
        } else {
            $config = Module::getInstance()->getConfig();
            $arr = explode(',', $config['allow_suffix']);
        }

        foreach ($arr as $a) {
            $exts[$a] = $a;
        }

        $search->multipleSelect('suffix', __blang('builder_attachment_suffix'), '6 col-xs-6')->options($exts);
    }
    /**
     * 构建表格
     *
     * @return void
     */
    protected function buildTable(&$data = [], $isExporting = false)
    {
        $table = $this->table;

        $choose = input('choose', 0);

        $table->show('id', 'ID');
        $table->text('name', __blang('builder_attachment_name'))->autoPost();
        $table->file('file',  __blang('builder_attachment_file'))->thumbSize(50, 50);
        if (!$choose) {
            $table->show('mime', __blang('builder_attachment_mime'));
            $table->show('size', __blang('builder_attachment_size'))->to('{val}MB');
            $table->show('suffix', __blang('builder_attachment_suffix'))->getWrapper()->addStyle('width:80px');
            $table->show('storage', __blang('builder_attachment_storage'));
        }

        $table->raw('url', __blang('builder_attachment_url'))->to('<a href="{val}" target="_blank">{val}</a>');
        $table->show('create_time', __blang('builder_attachment_create_time'))->getWrapper()->addStyle('width:160px');

        $table->getToolbar()
            ->btnRefresh()
            ->btnToggleSearch();

        foreach ($data as &$d) {
            $d['file'] = $d['url'];
        }

        unset($d);

        if ($choose) {
            $limit = input('limit/d', 1);
            $tableId = $table->getTableId(); //当前表格id

            if ($limit > 1) {

                $choseMultiplescript = <<<EOT

        let chooseUrlLimit = {$limit};

        let selected = {$tableId}GetCheckedRows();//获取选中的行数据
        if(selected.length == 0) {
            VxpMessage.warning(__blang.builder_no_data_was_selected);
            return;
        }
        let urls = [];
        selected.forEach( item => {
            urls.push(item.file);
        });
        parent.onChooseFile(urls.join(','));

EOT;
                //也可把上面方法封装成一个方法 window.onChooseFile = function() {//...} 
                //然后在btn attr中绑定onclick ：btnLink('#', __blang('builder_choose_multiple_files_button'), 'success', 'mdi-note-plus-outline', 'onclick="onChooseFile"')
                $table->getToolbar()
                    ->btnLink('#', __blang('builder_choose_multiple_files_button'), 'success', 'mdi-note-plus-outline')->barOnClick($choseMultiplescript);
            } else {
                $table->useCheckbox(false);
            }

            $table->getActionbar()
                ->btnLink('choose', '#', __blang('builder_choose_file_button'), 'success', 'mdi-note-plus-outline')
                ->barOnClick('parent.onChooseFile(row.file)'); //row 为当前行的数据
        } else {
            $table->useCheckbox(false);
            $table->useActionbar(false);
        }
    }
}
