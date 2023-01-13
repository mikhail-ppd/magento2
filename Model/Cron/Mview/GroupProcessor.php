<?php

namespace Elisa\ProductApi\Model\Cron\Mview;

use Magento\Framework\Mview\ProcessorInterface;

class GroupProcessor
{
    /** @var ProcessorInterface  */
    protected $mviewProcessor;

    /**
     * @param ProcessorInterface $mviewProcessor
     */
    public function __construct(
        ProcessorInterface $mviewProcessor
    ) {
        $this->mviewProcessor = $mviewProcessor;
    }

    /**
     * @return void
     */
    public function updateMviews()
    {
        $this->mviewProcessor->update('elisa_product_tracker');
    }

    /**
     * @return void
     */
    public function clearChangelogs()
    {
        $this->mviewProcessor->clearChangelog('elisa_product_tracker');
    }
}
