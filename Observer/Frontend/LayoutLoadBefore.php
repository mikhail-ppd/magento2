<?php

namespace Elisa\ProductApi\Observer\Frontend;

use Elisa\ProductApi\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config as PageConfig;

class LayoutLoadBefore implements ObserverInterface
{
    /** @var Config */
    protected $config;
    /** @var PageConfig */
    protected $pageConfig;

    /**
     * @param Config $config
     * @param PageConfig $pageConfig
     */
    public function __construct(Config $config, PageConfig $pageConfig)
    {
        $this->config = $config;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isOnSiteActive()) {
            return;
        }

        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $observer->getData('layout');

        if ($this->config->isOnSiteEventsActive()) {
            $layout->getUpdate()->addHandle('elisa_event_styles');
        }

        $handles = $layout->getUpdate()->getHandles();

        if (!$this->isAllowedForLayout($handles)) {
            return;
        }

        $this->pageConfig->addBodyClass('with-elisa-on-site');

        $pageUid = $this->config->getOnSitePageUid();

        $this->pageConfig->addRemotePageAsset(
            "https://storage.googleapis.com/elisa-test-cdn.elisa.io/widget-$pageUid.js?ver=0.0.1",
            'js',
            [
                "src_type" => "url",
                'attributes' => [
                    'id' => 'elisa-js',
                    'async' => 'true',
                    'crossorigin' => 'anonymous'
                ]
            ],
            "elisa_on_site_init_script"
        );
    }

    /**
     * Checks whether onsite is allowed in the current layout
     *
     * @param string[] $handles
     * @return bool
     */
    private function isAllowedForLayout(array $handles): bool
    {
        $allowedMasks = $this->config->getOnSiteDisabledHandleMasks();

        if (!$allowedMasks) {
            return true;
        }

        $expandedHandles = array_reduce(
            $handles,
            function ($carry, $handle) {
                $carry[] = $handle;

                $pathParts = explode('_', $handle);

                while (count($pathParts) > 1) {
                    array_pop($pathParts);
                    $carry[] = implode('_', $pathParts) . '_*';
                }

                return $carry;
            },
            []
        );

        return !array_intersect($expandedHandles, $allowedMasks);
    }
}
