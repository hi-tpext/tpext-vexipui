<?php

namespace tpext\builder\traits\actions;

/**
 * 删除
 */

trait HasDelete
{
    public function delete()
    {
        $this->checkToken();

        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error(__blang('bilder_parameter_error'));
        }
        $res = 0;
        foreach ($ids as $id) {
            if (!$this->canDel($id)) {
                continue;
            }
            if ($this->dataModel->destroy($id)) {
                $res += 1;
                $this->afterDel($id);
            }
        }

        if ($res) {
            $this->success(__blang('bilder_delete_{:num}_records_succeeded', ['num' => $res]));
        } else {
            $this->error(__blang('bilder_delete_failed'));
        }
    }

    /**
     * 判断是否可以删除 ，可使用模型的befroDelete事件替代
     *
     * @param mixed $id
     * @return boolean
     */
    protected function canDel($id)
    {
        if (!empty($this->delNotAllowed) && in_array($id, $this->delNotAllowed)) {
            return false;
        }
        // 其他逻辑
        return true;
    }

    /**
     * 删除以后，可使用模型的afterDelete事件替代
     *
     * @param mixed $id
     * @return boolean
     */
    protected function afterDel($id)
    {
        return true;
    }
}
