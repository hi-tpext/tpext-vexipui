<?php

namespace tpext\builder\traits\actions;

/**
 * 字段编辑
 */

trait HasAutopost
{
    public function autopost()
    {
        return $this->_autopost();
    }

    protected function _autopost()
    {
        $this->checkToken();

        $id = input('post.id/d', '');
        $name = input('post.name', '');
        $value = input('post.value', '');

        if (empty($id) || empty($name)) {
            $this->error(__blang('bilder_parameter_error'));
        }

        if (!empty($this->postAllowFields) && !in_array($name, $this->postAllowFields)) {
            $this->error(__blang('bilder_field_not_allowed'));
        }

        //单独修改一个字段，好多字段是未设置的，处理模型事件容易出错。不触发模型事件，不触发[update_time]修改
        $res = $this->dataModel->where($this->getPk(), $id)->update([$name => $value]);

        if ($res) {
            $this->success(__blang('bilder_update_succeeded'));
        } else {
            $this->error(__blang('bilder_update_failed_or_no_changes'));
        }
    }
}
