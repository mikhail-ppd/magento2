<?php

namespace Elisa\ProductApi\Model\Data\OptionSource;

use Elisa\ProductApi\Model\ResourceModel\Event as EventResource;
use Magento\Framework\Data\OptionSourceInterface;

class EventTags implements OptionSourceInterface
{
    /** @var EventResource */
    protected $resource;

    /** @var array|null */
    protected $options = null;

    /**
     * @param EventResource $resource
     */
    public function __construct(EventResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];

            $tags = $this->resource->getTags();

            foreach ($tags as $tag) {
                $this->options[] = [
                    'value' => $tag,
                    'label' => $tag
                ];
            }
        }

        return $this->options;
    }
}
