<?php

namespace Elisa\ProductApi\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    /** @var \Magento\Framework\Mview\ViewInterfaceFactory  */
    protected $viewFactory;

    public function __construct(
        \Magento\Framework\Mview\ViewInterfaceFactory $viewFactory
    ) {
        $this->viewFactory = $viewFactory;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $view = $this->viewFactory->create();
        $view->load('elisa_product_tracker');
        $view->unsubscribe()->subscribe();
    }
}
