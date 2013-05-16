<?php

namespace Lazy\View\Helper;

abstract class AssetTag extends Tag
{
    protected $assetStamp = false;
    protected $tag;
    protected $assetAttribute;
    protected $extension;
    protected $defaultAttributes = [];

    public function assetStamp($assetStamp = null)
    {
        if (!func_num_args()) {
            return $this->assetStamp;
        }

        if (true === $assetStamp) {
            $assetStamp = time();
        }

        $this->assetStamp = $assetStamp;
        return $this;
    }

    protected function render($asset, array $attributes = [])
    {
        $attributes = array_merge($this->defaultAttributes, $attributes);
        pathinfo($asset, PATHINFO_EXTENSION) || $asset .= '.' . $this->extension;
        !$this->assetStamp || $asset .= '?' . $this->assetStamp;
        $attributes[$this->assetAttribute] = $asset;
        return $this->tag($this->tag, $attributes);
    }
}