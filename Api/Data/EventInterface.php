<?php

namespace Elisa\ProductApi\Api\Data;

/**
 * @api
 */
interface EventInterface
{
    /**
     * Get animation path
     *
     * @return string
     */
    public function getAnimationPath(): string;

    /**
     * Get animation remote url
     *
     * @return string
     */
    public function getAnimationRemoteUrl(): string;

    /**
     * Get Campaign ID
     *
     * @return string
     */
    public function getCampaignId(): string;

    /**
     * Get cover photo path.
     *
     * Will return null if not imported yet.
     *
     * @return string|null
     */
    public function getCoverPhotoPath(): ?string;

    /**
     * Get cover photo remote url
     *
     * @return string
     */
    public function getCoverPhotoRemoteUrl(): string;

    /**
     * Get deadline timestamp
     *
     * @return int
     */
    public function getDeadlineTimestamp(): int;

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get Event ID
     *
     * @return string
     */
    public function getEventId(): string;

    /**
     * Get live cover photo path.
     *
     * Will return null if not imported yet.
     *
     * @return string|null
     */
    public function getLiveCoverPhotoPath(): ?string;

    /**
     * Get live cover photo remote url
     *
     * @return string
     */
    public function getLiveCoverPhotoRemoteUrl(): string;

    /**
     * Get start timestamp of live video
     *
     * @return int
     */
    public function getLiveStartTimestamp(): int;

    /**
     * Get name of event
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get short name of event
     *
     * @return string
     */
    public function getNameShort(): string;

    /**
     * Get Page UID
     *
     * @return string
     */
    public function getPageUid(): string;

    /**
     * Get event start timestamp
     *
     * @return int
     */
    public function getStartTimestamp(): int;

    /**
     * Get event status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get event tags
     *
     * @return string[]
     */
    public function getTags(): array;

    /**
     * Set animation path
     *
     * @param string $value
     * @return EventInterface
     */
    public function setAnimationPath(string $value): EventInterface;

    /**
     * Set animation remote url
     *
     * @param string $value
     * @return EventInterface
     */
    public function setAnimationRemoteUrl(string $value): EventInterface;

    /**
     * Set Campaign ID
     *
     * @param string $value
     * @return EventInterface
     */
    public function setCampaignId(string $value): EventInterface;

    /**
     * Set cover photo path
     *
     * @param string $value
     * @return EventInterface
     */
    public function setCoverPhotoPath(string $value): EventInterface;

    /**
     * Set cover photo remote url
     *
     * @param string $value
     * @return EventInterface
     */
    public function setCoverPhotoRemoteUrl(string $value): EventInterface;

    /**
     * Set deadline timestamp
     *
     * @param int $value
     * @return EventInterface
     */
    public function setDeadlineTimestamp(int $value): EventInterface;

    /**
     * Set description
     *
     * @param string $value
     * @return EventInterface
     */
    public function setDescription(string $value): EventInterface;

    /**
     * Set Event ID
     *
     * @param string $value
     * @return EventInterface
     */
    public function setEventId(string $value): EventInterface;

    /**
     * Set live cover photo path
     *
     * @param string $value
     * @return EventInterface
     */
    public function setLiveCoverPhotoPath(string $value): EventInterface;

    /**
     * Set live cover photo remote url
     *
     * @param string $value
     * @return EventInterface
     */
    public function setLiveCoverPhotoRemoteUrl(string $value): EventInterface;

    /**
     * Set start timestamp of live video
     *
     * @param int $value
     * @return EventInterface
     */
    public function setLiveStartTimestamp(int $value): EventInterface;

    /**
     * Set name
     *
     * @param string $value
     * @return EventInterface
     */
    public function setName(string $value): EventInterface;

    /**
     * Set short name
     *
     * @param string $value
     * @return EventInterface
     */
    public function setNameShort(string $value): EventInterface;

    /**
     * Get Page UID
     *
     * @param string $value
     * @return EventInterface
     */
    public function setPageUid(string $value): EventInterface;

    /**
     * Set start timestamp
     *
     * @param int $value
     * @return EventInterface
     */
    public function setStartTimestamp(int $value): EventInterface;

    /**
     * Set status
     *
     * @param string $value
     * @return EventInterface
     */
    public function setStatus(string $value): EventInterface;

    /**
     * Set tags
     *
     * @param string[] $value
     * @return EventInterface
     */
    public function setTags(array $value): EventInterface;
}
