<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\EventInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Event extends AbstractModel implements EventInterface, IdentityInterface
{
    public const CACHE_TAG = 'els_ei';
    public const KEY_CAMPAIGN_ID = 'campaign_id';
    public const KEY_COVER_PHOTO_PATH = 'cover_photo_path';
    public const KEY_COVER_PHOTO_REMOTE_URL = 'cover_photo_remote_url';
    public const KEY_DEADLINE_TIMESTAMP = 'deadline_timestamp';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_EVENT_ID = 'event_id';
    public const KEY_LIVE_START_TIMESTAMP = 'live_start_timestamp';
    public const KEY_NAME = 'name';
    public const KEY_NAME_SHORT = 'name_short';
    public const KEY_PAGE_UID = 'page_uid';
    public const KEY_START_TIMESTAMP = 'start_timestamp';
    public const KEY_STATUS = 'status';
    public const KEY_TAGS = 'tags';

    /**
     * @inheritDoc
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'els_event_info';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Event::class);
    }

    /**
     * @inheritDoc
     */
    public function getCacheTags(): array
    {
        $tags = parent::getCacheTags();

        if (!is_array($tags)) {
            $tags = [];
        }

        return array_unique(array_merge($tags, $this->getIdentities()));
    }

    /**
     * @inheritDoc
     */
    public function getCampaignId(): string
    {
        return (string)$this->getData(self::KEY_CAMPAIGN_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCoverPhotoPath(): ?string
    {
        return $this->getData(self::KEY_COVER_PHOTO_PATH);
    }

    /**
     * @inheritDoc
     */
    public function getCoverPhotoRemoteUrl(): string
    {
        return (string)$this->getData(self::KEY_COVER_PHOTO_REMOTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getDeadlineTimestamp(): int
    {
        return (int)$this->getData(self::KEY_DEADLINE_TIMESTAMP);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string)$this->getData(self::KEY_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function getEventId(): string
    {
        return (string)$this->getData(self::KEY_EVENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getLiveStartTimestamp(): int
    {
        return (int)$this->getData(self::KEY_LIVE_START_TIMESTAMP);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->getData(self::KEY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getNameShort(): string
    {
        return (string)$this->getData(self::KEY_NAME_SHORT);
    }

    /**
     * @inheritDoc
     */
    public function getPageUid(): string
    {
        return (string)$this->getData(self::KEY_PAGE_UID);
    }

    /**
     * @inheritDoc
     */
    public function getStartTimestamp(): int
    {
        return (int)$this->getData(self::KEY_START_TIMESTAMP);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::KEY_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        return (array)$this->getData(self::KEY_TAGS);
    }

    /**
     * @inheritDoc
     */
    public function setCampaignId(string $value): EventInterface
    {
        return $this->setData(self::KEY_CAMPAIGN_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCoverPhotoPath(string $value): EventInterface
    {
        return $this->setData(self::KEY_COVER_PHOTO_PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCoverPhotoRemoteUrl(string $value): EventInterface
    {
        return $this->setData(self::KEY_COVER_PHOTO_REMOTE_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDeadlineTimestamp(int $value): EventInterface
    {
        return $this->setData(self::KEY_DEADLINE_TIMESTAMP, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $value): EventInterface
    {
        return $this->setData(self::KEY_DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function setEventId(string $value): EventInterface
    {
        return $this->setData(self::KEY_EVENT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLiveStartTimestamp(int $value): EventInterface
    {
        return $this->setData(self::KEY_LIVE_START_TIMESTAMP, $value);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $value): EventInterface
    {
        return $this->setData(self::KEY_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNameShort(string $value): EventInterface
    {
        return $this->setData(self::KEY_NAME_SHORT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPageUid(string $value): EventInterface
    {
        return $this->setData(self::KEY_PAGE_UID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStartTimestamp(int $value): EventInterface
    {
        return $this->setData(self::KEY_START_TIMESTAMP, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $value): EventInterface
    {
        return $this->setData(self::KEY_STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $value): EventInterface
    {
        return $this->setData(self::KEY_TAGS, $value);
    }
}
