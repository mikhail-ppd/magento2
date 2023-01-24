<?php

namespace Elisa\ProductApi\Plugin\Framework\Mview\View;

use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Mview\View\Collection;
use Magento\Framework\Mview\View\State\CollectionFactory;

class CollectionPlugin
{
    const ELISA_MVIEW_GROUP = 'elisa_product_tracker';
    const ELISA_MVIEW_ID = 'elisa_product_tracker';
    /** @var ReaderInterface  */
    protected $configReader;

    /**
     * @var CollectionFactory
     */
    protected $mviewStatesFactory;

    /**
     * @param CollectionFactory $mviewStatesFactory
     * @param ReaderInterface $configReader
     */
    public function __construct(
        \Magento\Framework\Mview\View\State\CollectionFactory $mviewStatesFactory,
        \Magento\Framework\Config\ReaderInterface $configReader
    ) {
        $this->mviewStatesFactory = $mviewStatesFactory;
        $this->configReader = $configReader;
    }

    /**
     * This is due to a bug in \Magento\Framework\Mview\View\Collection
     *
     * More precisely in \Magento\Framework\Mview\View\Collection::getOrderedViewIds,
     *
     *     $orderedViewIds += array_diff(array_keys($this->config->getViews()), $orderedViewIds);
     *
     * The array union is skipping some of legitimate non-Indexer mviews if the numeric keys collide
     *
     * This plugin can be removed when Magento has fixed this.
     *
     * It should be a simple fix:
     *     $orderedViewIds = array_merge(
     *          $orderedViewIds,
     *          array_diff(array_keys($this->config->getViews()), $orderedViewIds)
     *     );
     *
     * @param Collection $subject
     * @param callable $proceed
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @throws \Exception
     */
    public function aroundLoadData($subject, callable $proceed, $printQuery = false, $logQuery = false)
    {
        $wasLoaded = $subject->isLoaded();
        $result = $proceed($printQuery, $logQuery);

        if (!$wasLoaded) {
            $items = $subject->getItemsByColumnValue('group', self::ELISA_MVIEW_GROUP);

            if (!$items) {
                $view = $subject->getNewEmptyItem();

                try {
                    $view = $view->load(self::ELISA_MVIEW_ID);
                } catch (\InvalidArgumentException $exception) {
                    /**
                     * Because of https://github.com/magento/magento2/issues/33802#issuecomment-899059404
                     * During setup:upgrade we end up with a cached version of DeploymentConfig which is not the
                     * one that is updated when app/etc/config.php is updated.
                     * Unless and until M2 fixes this, working around using an inexpensive proxy to the config reader
                     * with updated composition objects
                     */
                    $configData = $this->configReader->read();

                    if ($configData[self::ELISA_MVIEW_ID]) {
                        $viewData = $configData[self::ELISA_MVIEW_ID];
                        $view->setId($viewData['view_id']);
                        $view->setData($viewData);
                    } else {
                        throw $exception;
                    }
                }

                $states = $this->mviewStatesFactory->create();

                foreach ($states->getItems() as $state) {
                    /** @var \Magento\Framework\Mview\View\StateInterface $state */
                    if ($state->getViewId() == self::ELISA_MVIEW_ID) {
                        $view->setState($state);
                        break;
                    }
                }

                $subject->addItem($view);
            }
        }

        return $result;
    }
}
