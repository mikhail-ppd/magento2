<?php

namespace Elisa\ProductApi\Plugin\Framework\Mview\View;

use Magento\Framework\Mview\View\Collection;
use Magento\Framework\Mview\View\State\CollectionFactory;

class CollectionPlugin
{
    const ELISA_MVIEW_GROUP = 'elisa_product_tracker';
    const ELISA_MVIEW_ID = 'elisa_product_tracker';

    /**
     * @var CollectionFactory
     */
    protected $mviewStatesFactory;

    /**
     * @param CollectionFactory $mviewStatesFactory
     */
    public function __construct(\Magento\Framework\Mview\View\State\CollectionFactory $mviewStatesFactory)
    {
        $this->mviewStatesFactory = $mviewStatesFactory;
    }


    /**
     * This is due to a bug in \Magento\Framework\Mview\View\Collection
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

                $view = $view->load(self::ELISA_MVIEW_ID);

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
