<?php

namespace tpext\builder\traits\actions;

define('FORM_ADD', 0);
/**
 * 添加
 */

trait HasAdd
{
    public function add()
    {
        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->addText ?: __blang('bilder_page_add_text'), 'add');
            $form = $builder->form();
            $data = [];
            $this->form = $form;
            $this->isEdit = 0;
            $this->buildForm($this->isEdit, $data);
            $form->fill($data);
            $form->method('post');

            return $builder->render();
        }

        $this->checkToken();

        return $this->save();
    }
}
