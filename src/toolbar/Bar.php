<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;
use tpext\common\ExtLoader;

class Bar implements Renderable
{
    use HasDom;

    protected $name = '';

    protected $view = '';

    protected $extKey = '';

    protected $icon = '';

    protected $href = '';

    protected $__href__ = '';

    protected $label = '';

    protected $setupScript = [];

    protected $useLayer = true;

    protected $layerSize = '';

    protected $pullRight = false;

    protected $hidden = false;

    protected $onClick = '';

    /**
     * Undocumented variable
     *
     * @var \Closure
     */
    protected $rendering = null;

    protected $tableId = '';

    protected $type = 'default';

    public function __construct($name, $label = '')
    {
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * Undocumented function
     *
     * @param string $val primary | success | warning | error | info | default
     * @return $this
     */
    public function type($val = '')
    {
        $this->type = str_replace('btn-', '', $val);
        if ($this->type == 'danger') {
            $this->type  = 'error';
        }
        if (!in_array($this->type, ['primary', 'info', 'success', 'warning', 'error'])) {
            $this->addClass('vxp-button--' . $this->type);
            $this->type = 'default';
        }

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
        $this->tableId = $val;

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
        $barType = $barType ? $barType : get_called_class();

        $barType = lcfirst($barType);

        $defaultClass = BWrapper::hasDefaultBarClass($barType);

        if (!empty($defaultClass)) {
            $this->class = $defaultClass;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function onClick($script)
    {
        $this->onClick = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return 'bar' . preg_replace('/\W/', '_', ucfirst($this->extKey) . ucfirst($this->name));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function label($val)
    {
        $this->label = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function icon($val)
    {
        $this->icon = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function href($val)
    {
        if ($val == '#' || $val == ' ') {
            return $this;
        }

        $this->href = (string) $val;

        if (!Builder::checkUrl($this->href)) {
            $this->hidden = true;
        }

        return $this;
    }

    public function getHref()
    {
        return empty($this->__href__) ? $this->href : $this->__href__;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function useLayer($val, $size = [])
    {
        $this->useLayer = $val;

        if (!empty($size)) {
            $this->layerSize = is_array($size) ? implode(',', $size) : $size;
        }

        return $this;
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
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function name($val)
    {
        $this->name = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function pullRight($val = true)
    {
        $this->pullRight = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPullRight()
    {
        return $this->pullRight;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function getLayerSize()
    {
        return $this->layerSize;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getInon()
    {
        return $this->icon;
    }

    /**
     * Undocumented function
     * 
     * @return $this
     */
    public function beforRender()
    {
        if (!empty($this->setupScript)) {
            Builder::getInstance()->addSetupScript($this->setupScript);
        }

        ExtLoader::trigger('tpext_bar_befor_render', $this);

        if ($this->rendering instanceof \Closure) {
            $this->rendering->call($this, $this);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        return '';
    }

    protected function getViewInstance()
    {
        $template = Module::getInstance()->getViewsPath() . 'toolbar' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        return $viewshow;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    public function initLayer()
    {
        $this->useLayer = $this->useLayer && !empty($this->href) && !preg_match('/javascript:.*/i', $this->href) && !preg_match('/^#.*/i', $this->href);

        if (strpos($this->attr, 'target=') !== false) {
            $this->useLayer = false;
        }

        if ($this->useLayer && empty($this->layerSize)) {
            $this->layerSize = $this->getAttrByName('data-layer-size');
            if (empty($this->layerSize)) {
                $config = Module::getInstance()->getConfig();
                $this->layerSize = $config['layer_size'];
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        $vars = [
            'id' => $this->getId(),
            'label' => $this->icon && $this->label ? '&nbsp;' . $this->label : $this->label,
            'name' => $this->getName(),
            'class' => str_replace('btn-', 'vxp-button--', $this->getClass()),
            'href' => $this->getHref(),
            'icon' => $this->icon,
            'attr' => $this->getAttrWithStyle(),
            'useLayer' => $this->useLayer,
            'pullRight' => $this->pullRight,
            'tableId' => $this->tableId,
            'hidden' => $this->hidden,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $vars;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function rendering($callback)
    {
        $this->rendering = $callback;
        return $this;
    }
}
