<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\EventInterface;
use Elisa\ProductApi\Api\Data\EventInterfaceFactory as EventFactory;
use Elisa\ProductApi\Api\EventManagementInterface;
use Elisa\ProductApi\Api\Service\GetEventsInterface;
use Elisa\ProductApi\Api\Service\StoreLevelServiceInterface;
use Elisa\ProductApi\Exception\ElisaException;
use Elisa\ProductApi\Model\Event\ImageImporter;
use Elisa\ProductApi\Model\ResourceModel\Event as EventResource;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class EventManagement implements EventManagementInterface
{
    /** @var Config */
    protected $config;
    /** @var EventFactory */
    protected $eventFactory;
    /** @var EventResource */
    protected $eventResource;
    /** @var GetEventsInterface */
    protected $getEventsService;
    /** @var ImageImporter */
    protected $imageImporter;
    /** @var LoggerInterface */
    protected $logger;
    /** @var SerializerInterface */
    protected $serializer;
    /** @var ServiceOutputProcessor */
    protected $serviceOutputProcessor;

    /**
     * @param GetEventsInterface $getEventsService
     * @param ImageImporter $imageImporter
     * @param EventFactory $eventFactory
     * @param EventResource $eventResource
     * @param Config $config
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetEventsInterface $getEventsService,
        ImageImporter $imageImporter,
        EventFactory $eventFactory,
        EventResource $eventResource,
        Config $config,
        ServiceOutputProcessor $serviceOutputProcessor,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->eventFactory = $eventFactory;
        $this->eventResource = $eventResource;
        $this->getEventsService = $getEventsService;
        $this->imageImporter = $imageImporter;
        $this->logger = $logger;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function refreshEvents(?int $storeId = null)
    {
        if (!$this->config->isOnSiteEventsActive($storeId)) {
            return;
        }

        if ($storeId && $this->getEventsService instanceof StoreLevelServiceInterface) {
            $this->getEventsService->setStoreId($storeId);
        }

        $events = $this->getEventsService->execute();
        $eventIds = [];
        $validImages = [];

        foreach ($events as $event) {
            $eventIds[] = $event->getEventId();
            $coverPhotoUrl = $event->getCoverPhotoRemoteUrl();

            $event->setCoverPhotoPath('');

            try {
                $validImages[] = $path = $this->imageImporter->importImage($coverPhotoUrl, $event->getEventId());
                $event->setCoverPhotoPath($path);
            } catch (ElisaException|FileSystemException $e) {
                $this->logger->error($e);
            }

            $event->setAnimationPath('');

            if ($animationUrl = $event->getAnimationRemoteUrl()) {
                try {
                    $validImages[] = $path = $this->imageImporter->importImage($animationUrl, $event->getEventId());
                    $event->setAnimationPath($path);
                } catch (ElisaException|FileSystemException $e) {
                    $this->logger->error($e);
                }
            }

            $event->setLiveCoverPhotoPath('');

            if ($liveCoverPhotoUrl = $event->getLiveCoverPhotoRemoteUrl()) {
                try {
                    $validImages[] = $path = $this->imageImporter->importImage($liveCoverPhotoUrl, $event->getEventId());
                    $event->setLiveCoverPhotoPath($path);
                } catch (ElisaException|FileSystemException $e) {
                    $this->logger->error($e);
                }
            }

            $existingEvent = $this->eventFactory->create();
            $this->eventResource->load($existingEvent, $event->getEventId());

            $updated = $this->isUpdatedEvent($event, $existingEvent);

            if ($updated) {
                $this->eventResource->save($event);
            }
        }

        $deleteEventIds = $this->eventResource->getStaleEventIds($eventIds);

        foreach ($deleteEventIds as $eventId) {
            $staleEvent = $this->eventFactory->create(['data' => ['event_id' => $eventId]]);

            try {
                $this->eventResource->delete($staleEvent);
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }

        try {
            $this->imageImporter->cleanup($validImages);
        } catch (LocalizedException $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Compare if event contents are same
     *
     * @param Event $event
     * @param EventInterface $existingEvent
     * @return bool
     */
    private function isUpdatedEvent(Event $event, EventInterface $existingEvent): bool
    {
        $eventData = $this->serviceOutputProcessor->convertValue($event, EventInterface::class);
        $existingEventData = $this->serviceOutputProcessor->convertValue($existingEvent, EventInterface::class);
        return $this->serializer->serialize($eventData) !== $this->serializer->serialize($existingEventData);
    }
}
