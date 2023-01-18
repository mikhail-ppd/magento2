<?php

namespace Elisa\ProductApi\Block\Widget;

use Elisa\ProductApi\Api\Data\EventInterface;
use Elisa\ProductApi\Model\Config;
use Elisa\ProductApi\Model\Data\OptionSource\EventStatuses;
use Elisa\ProductApi\Model\Event as Model;
use Elisa\ProductApi\Model\ResourceModel\Event\Collection;
use Elisa\ProductApi\Model\ResourceModel\Event\CollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

class Event extends Template implements BlockInterface, IdentityInterface
{
    /** @var Config */
    protected $config;
    /** @var CollectionFactory */
    protected $collectionFactory;
    /** @var DateTimeFactory */
    protected $dateTimeFactory;
    /** @var Model[] */
    protected $events = null;

    /**
     * @param Config $config
     * @param CollectionFactory $collectionFactory
     * @param DateTimeFactory $dateTimeFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Config $config,
        CollectionFactory $collectionFactory,
        DateTimeFactory $dateTimeFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->dateTimeFactory = $dateTimeFactory;

        $this->addData([
            'cache_lifetime' => 86400
        ]);
    }

    /**
     * Get additional css classes
     *
     * @return string
     */
    public function getAdditionalClasses(): string
    {
        $classes = [];

        if ($mode = $this->getDefaultStylingMode()) {
            if ($mode === 1) {
                $classes[] = 'with-default-styles';
            } else {
                $classes[] = 'with-strict-default-styles';
            }
        }

        if (!$this->getPlayButtonLabel()) {
            $classes[] = 'no-button-label';
        }

        if ($this->getTitle()) {
            $classes[] = 'with-title';
        }

        if ($this->isDescriptionShown()) {
            $classes[] = 'with-description';
        }

        return $classes ? ' ' . implode(' ', $classes) : '';
    }

    /**
     * @inheritDoc
     */
    public function getCacheKeyInfo()
    {
        $key = parent::getCacheKeyInfo();

        $key['active'] = $this->config->isOnSiteEventsActive();
        $key['sort_order'] = (string)$this->getData('sort_order');
        $key['status'] = (string)$this->getData('status');
        $key['tags'] = (string)$this->getData('tags');
        $key['limit'] = (int)$this->getData('limit');
        $key['date_format'] = (int)($this->getData('date_format') ?? \IntlDateFormatter::MEDIUM);
        $key['time_format'] = (int)($this->getData('time_format') ?? \IntlDateFormatter::SHORT);
        $key['use_default_styles'] = $this->getDefaultStylingMode();
        $key['show_description'] = $this->isDescriptionShown();
        $key['play_button_label'] = $this->getPlayButtonLabel() ?? '';
        $key['title'] = $this->getTitle() ?? '';

        return $key;
    }

    /**
     * Get Cover Photo URL
     *
     * @param EventInterface $event
     * @return string
     */
    public function getCoverPhotoUrl(EventInterface $event): string
    {
        $path = $event->getCoverPhotoPath();
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . '/' . $path;
    }

    /**
     * Get event description
     *
     * @param EventInterface $event
     * @return string|null
     */
    public function getDescription(EventInterface $event): ?string
    {
        if (!$this->isDescriptionShown()) {
            return null;
        }

        return nl2br($this->_escaper->escapeHtml($event->getDescription()));
    }

    /**
     * Return events for widget
     *
     * @return Model[]
     */
    public function getEvents(): array
    {
        if ($this->events === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();

            $collection->addFieldToFilter(Model::KEY_PAGE_UID, $this->config->getOnSitePageUid());

            $sortDir = (string)$this->getData('sort_order');
            $statusCsv = (string)$this->getData('status');
            $statuses = array_filter(explode(',', $statusCsv));
            $tagCsv = (string)$this->getData('tags');
            $limit = (int)$this->getData('limit');

            if ($statuses) {
                $collection->addFieldToFilter(Model::KEY_STATUS, ['in' => $statuses]);
            }

            if ($tagCsv) {
                $tags = explode(',', $tagCsv);
                $select = $collection->getSelect();

                $tagWheres = [];

                foreach ($tags as $tag) {
                    $tagWheres[] = "FIND_IN_SET('$tag', `tags`)";
                }

                $tagWhere = implode(' OR ', $tagWheres);

                $select->where($tagWhere);
            }

            if ($limit) {
                $collection->setPageSize($limit);
            }

            if ($sortDir === 'desc') {
                $collection->setOrder(Model::KEY_START_TIMESTAMP, $collection::SORT_ORDER_DESC);
            } else {
                $collection->setOrder(Model::KEY_START_TIMESTAMP, $collection::SORT_ORDER_ASC);
            }

            $this->events = $collection->getItems();

            foreach ($this->events as $event) {
                $event->getResource()->unserializeFields($event);
            }
        }

        return $this->events;
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        $identityList = [
            [Model::CACHE_TAG]
        ];

        foreach ($this->getEvents() as $event) {
            $identityList[] = $event->getIdentities();
        }

        if (!$identityList) {
            return [];
        }

        return array_merge(...$identityList);
    }

    /**
     * Get play button label
     *
     * @return string|null
     */
    public function getPlayButtonLabel(): ?string
    {
        return $this->getData('play_button_label') ?: null;
    }

    /**
     * Get Start Time
     *
     * @param EventInterface $event
     * @return string
     */
    public function getStartTime(
        EventInterface $event
    ): string {
        $dateFormat = (int)($this->getData('date_format') ?? \IntlDateFormatter::MEDIUM);
        $timeFormat = (int)($this->getData('time_format') ?? \IntlDateFormatter::SHORT);
        $startTs = $event->getStartTimestamp();
        /** @var DateTime $dateTime */
        $dateTime = $this->dateTimeFactory->create();
        $gmtDateTime = $dateTime->gmtDate(null, $startTs);
        return trim($this->formatDate($gmtDateTime, $dateFormat) . ' ' . $this->formatTime($gmtDateTime, $timeFormat));
    }

    /**
     * Get widget title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData('title') ?: null;
    }

    /**
     * Is event playable
     *
     * @param EventInterface $event
     * @return bool
     */
    public function isPlayable(EventInterface $event): bool
    {
        return in_array(
            $event->getStatus(),
            [EventStatuses::STATUS_VOD, EventStatuses::STATUS_LIVE]
        );
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _toHtml()
    {
        if (!$this->config->isOnSiteEventsActive()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get default styling mode
     *
     * @return int
     */
    private function getDefaultStylingMode(): int
    {
        return (int)($this->getData('use_default_styles') ?? 0);
    }

    /**
     * Whether description is displayed
     *
     * @return bool
     */
    private function isDescriptionShown(): bool
    {
        return (bool)($this->getData('show_description') ?? false);
    }
}
