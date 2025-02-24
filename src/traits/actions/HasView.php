<?php

namespace tpext\builder\traits\actions;

use tpext\builder\common\Tab;
use tpext\builder\displayer;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\FRow;
use tpext\builder\form\Step;

define('FORM_VIEW', 2);
/**
 * 查看
 */

trait HasView
{
    public function view()
    {
        $id = input('id');

        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->viewText ?: __blang('bilder_page_view_text'), 'view');

            $data = $this->dataModel->field(true)->where($this->getPk(), $id)->find();
            if (!$data) {
                return $builder->layer()->close(0, __blang('bilder_data_not_found'));
            }

            $form = $builder->form();
            $this->form = $form;
            $this->isEdit = 2;
            $this->buildForm($this->isEdit, $data);
            $rows = $this->form->getRows();
            $form->fill($data);

            $this->turn($rows);
            $form->readonly();

            return $builder->render();
        }

        $this->error(__blang('bilder_not_allowed'));
    }

    protected function turn($rows)
    {
        $displayer = null;

        $fieldName = '';

        foreach ($rows as $row) {

            if ($row instanceof Tab || $row instanceof Step) {

                $rows_ = $row->getRows();
                foreach ($rows_ as $row_) {
                    if ($row_['content'] instanceof FieldsContent) {
                        $rows_ = $row_['content']->getRows();
                        $this->turn($rows_);
                    }
                }

                continue;
            }

            if (!($row instanceof FRow)) {
                continue;
            }

            $displayer = $row->getDisplayer();

            $fieldName = $displayer->getOriginName();
            $default = $displayer->getDefault();

            if (
                $displayer instanceof displayer\Button || $displayer instanceof displayer\Show || $displayer instanceof displayer\Raw
                || $displayer instanceof displayer\Matche || $displayer instanceof displayer\Matches || $displayer instanceof displayer\Load
                || $displayer instanceof displayer\Loads  || $displayer instanceof displayer\Map || $displayer->getDisplayerType() == 'Field'
            ) {
                continue;
            } else if ($displayer instanceof displayer\Items) {

                $content = $displayer->getContent();
                $this->turn($content->getCols());
            } else if ($displayer instanceof displayer\Fields) {

                $content = $displayer->getContent();
                $this->turn($content->getRows());
            } else if ($displayer instanceof displayer\Password) {

                $row->show($fieldName, $row->getLabel())->default('*********');
            } else if (
                $displayer instanceof displayer\Text || $displayer instanceof displayer\Tags
                || $displayer instanceof displayer\Number || $displayer instanceof displayer\Textarea
            ) {

                $row->show($fieldName, $row->getLabel())->default(__blang('bilder_value_is_empty'));
            } else if ($displayer instanceof displayer\Select && $displayer->isAjax()) { // multipleSelect(ajax) / select(ajax)

                $ajax = $displayer->getAjax();

                if ($displayer instanceof displayer\multipleSelect) {
                    $row->loads($fieldName, $row->getLabel())->dataUrl($ajax['url'], $ajax['text']);
                } else {
                    $row->load($fieldName, $row->getLabel())->dataUrl($ajax['url'], $ajax['text']);
                }
            } else if (
                $displayer instanceof displayer\Checkbox || $displayer instanceof displayer\MultipleSelect
                || $displayer instanceof displayer\Transfer
            ) { // checkbox / multipleSelect(非ajax) / Transfer

                $row->matches($fieldName, $row->getLabel())->options($displayer->getOptions())->default($default);
            } else if ($displayer instanceof displayer\Radio || $displayer instanceof displayer\Select) { // radio / select(非ajax)

                $row->match($fieldName, $row->getLabel())->options($displayer->getOptions())->default($default);
            } else if ($displayer instanceof displayer\Tree) {
                $row->matches($fieldName, $row->getLabel())->separator('<br/>')->optionsData($displayer->getOptions(), 'name')->default($default);
            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair['on'] => __blang('bilder_option_on'), $pair['off'] => __blang('bilder_option_off')];
                $row->match($fieldName, $row->getLabel())->options($options)->default($default);
            } else if (!($displayer instanceof displayer\MultipleFile
                || $displayer instanceof displayer\Divider || $displayer instanceof displayer\Html)) {

                $row->raw($fieldName, $row->getLabel())->default(__blang('bilder_value_is_empty'));
            }

            $size = $displayer->getSize();

            $row->getDisplayer()
                ->required(false)
                ->showLabel($displayer->isShowLabel())
                ->size($size[0], $size[1])
                ->help($displayer->getHelp());
        }
    }
}
