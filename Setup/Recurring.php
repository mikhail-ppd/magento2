<?php

namespace Elisa\ProductApi\Setup;

use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    const ELISA_MVIEW_ID = 'elisa_product_tracker';

    /** @var ReaderInterface */
    protected $configReader;
    /** @var \Magento\Framework\Mview\ViewInterfaceFactory */
    protected $viewFactory;

    public function __construct(
        \Magento\Framework\Mview\ViewInterfaceFactory $viewFactory,
        \Magento\Framework\Config\ReaderInterface $configReader
    ) {
        $this->viewFactory = $viewFactory;
        $this->configReader = $configReader;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $view = $this->viewFactory->create();

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

        $view->unsubscribe()->subscribe();
    }
}
