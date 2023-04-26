<?php

namespace Elisa\ProductApi\Model\Service;

use Elisa\ProductApi\Api\Data\EventInterface as Event;
use Elisa\ProductApi\Api\Data\EventInterfaceFactory as EventFactory;
use Elisa\ProductApi\Api\Service\GetEventsInterface;
use Elisa\ProductApi\Api\Service\StoreLevelServiceInterface;
use Elisa\ProductApi\Exception\ServiceException;
use Elisa\ProductApi\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GetEvents implements GetEventsInterface, StoreLevelServiceInterface
{
    /** @var Config */
    protected $config;
    /** @var EventFactory */
    protected $eventFactory;
    /** @var CURL */
    protected $httpClient = [];
    /** @var CurlFactory */
    protected $httpClientFactory;
    /** @var Json */
    protected $jsonSerializer;
    /** @var LoggerInterface */
    protected $logger;
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var int|null */
    protected $storeId = null;

    /**
     * @param Config $config
     * @param CurlFactory $httpClientFactory
     * @param Json $jsonSerializer
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param EventFactory $eventFactory
     */
    public function __construct(
        Config $config,
        CurlFactory $httpClientFactory,
        Json $jsonSerializer,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        EventFactory $eventFactory
    ) {
        $this->config = $config;
        $this->eventFactory = $eventFactory;
        $this->httpClientFactory = $httpClientFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(): array
    {
        $events = [];
        $response = $this->get();
        $responseJson = $this->jsonSerializer->unserialize($response);
        $apiEvents = $responseJson['events'] ?? [];

        if (!$apiEvents) {
            return $events;
        }

        $pageUid = $this->config->getOnSitePageUid($this->getStoreId());

        foreach ($apiEvents as $apiEvent) {
            /** @var Event $event */
            $event = $this->eventFactory->create();
            $event->setEventId($apiEvent['eventId'])
                ->setCampaignId($apiEvent['campaignId'])
                ->setStartTimestamp((int)(((int)$apiEvent['eventStartTimestamp']) / 1000))
                ->setName($apiEvent['eventName'])
                ->setNameShort((string)$apiEvent['eventNameShort'])
                ->setPageUid($pageUid)
                ->setDescription(((string)$apiEvent['description']))
                ->setDeadlineTimestamp((int)(((int)$apiEvent['deadline']) / 1000))
                ->setLiveStartTimestamp((int)(((int)$apiEvent['liveStartTime']) / 1000))
                ->setStatus((string)$apiEvent['currentStatus'])
                ->setCoverPhotoRemoteUrl((string)$apiEvent['coverPhoto'])
                ->setTags($apiEvent['tags'] ?? [])
                ->setAnimationRemoteUrl($apiEvent['animation'] ?? '')
                ->setLiveCoverPhotoRemoteUrl($apiEvent['liveVideoCover'] ?? '');

            $events[] = $event;
        }

        return $events;
    }

    /**
     * @inheritDoc
     */
    public function getStoreId(): int
    {
        if ($this->storeId === null) {
            try {
                $this->storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $this->storeId = $this->storeManager->getDefaultStoreView()->getId();
            }
        }

        return $this->storeId;
    }

    /**
     * @inheritDoc
     */
    public function setStoreId(int $storeId): StoreLevelServiceInterface
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Returns client
     *
     * @return Curl
     */
    private function getHttpClient(): Curl
    {
        if (isset($this->httpClient[$this->getStoreId()])) {
            return $this->httpClient[$this->getStoreId()];
        }

        $httpClient = $this->httpClientFactory->create();
        $httpClient->setOption(CURLOPT_TIMEOUT, 300);
        $httpClient->addHeader("Content-Type", "application/json");
        $httpClient->addHeader(
            "Authorization",
            "Bearer " . $this->config->getOnSiteEventsApiToken($this->getStoreId())
        );

        $this->httpClient[$this->getStoreId()] = $httpClient;

        return $this->httpClient[$this->getStoreId()];
    }

    /**
     * Get API response body
     *
     * @return string
     * @throws ServiceException
     */
    private function get(): string
    {
        try {
            $client = $this->getHttpClient();
            $client->get(
                trim($this->config->getOnSiteEventsApiEndpoint($this->getStoreId()), ' /')
                . '/' . trim($this->config->getOnSitePageUid($this->getStoreId()))
            );

            $httpCode = $client->getStatus();

            if (!in_array($httpCode, [100, 200])) {
                throw new ServiceException(__("[HTTP %1] Failed request.", $httpCode));
            }

            return $client->getBody();
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw new ServiceException(null, $e);
        }
    }
}
