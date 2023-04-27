<?php

namespace Elisa\ProductApi\Model\Data\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class EventStatuses implements OptionSourceInterface
{
    public const STATUS_CLIP = 'clip';
    public const STATUS_PLANNED = 'planned';
    public const STATUS_LIVE = 'live';
    public const STATUS_VOD = 'vod';

    /** @var array|null */
    protected $options = null;

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];

            $this->options[] = [
                'value' => self::STATUS_PLANNED,
                'label' => __("Planned Event")
            ];

            $this->options[] = [
                'value' => self::STATUS_LIVE,
                'label' => __("Currently Live")
            ];

            $this->options[] = [
                'value' => self::STATUS_VOD,
                'label' => __("Past Event")
            ];

            $this->options[] = [
                'value' => self::STATUS_CLIP,
                'label' => __("Clip")
            ];
        }

        return $this->options;
    }
}
