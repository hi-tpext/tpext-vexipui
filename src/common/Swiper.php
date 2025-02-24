<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\think\View;

class Swiper extends Widget implements Renderable
{
    /**
     * Undocumented variable
     *
     * @var View
     */
    protected $content;

    protected $partial = false;

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
     * @param array $arr [['title'=>title1,'image'=>image1],['title'=>title2,'image'=>image2],...] or [image1, image2,...]
     * @return $this
     */
    public function images($arr)
    {
        $list = [];
        foreach ($arr as $k => $v) {
            if (isset($v['image'])) {
                $list[] = ['title' => $v['title'] ?? $k, 'image' => $v['image']];
            } else {
                $list[] = ['title' => $k, 'image' => $v];
            }
        }
        $tpl = '<vxp-carousel :loop="true" arrow="inside" pointer="outside" :view-size="1" :autoplay="5000">
                {volist name="list" id="vo"}
                    <vxp-carousel-item class="carousel-item" :key="{$key}" title="{$vo.title}">
                        <img object-fit="fill" src="{$vo.image}" alt="{$vo.title}">
                    </vxp-carousel-item>
                {/volist}
                </vxp-carousel>';

        $this->content = new View($tpl);

        $this->content->assign(['list' => $list])->isContent(true);
        return $this;
    }

    public function beforRender()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        if ($this->partial) {
            return $this->content;
        }

        return $this->content->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function destroy()
    {
        $this->content = null;
    }
}
