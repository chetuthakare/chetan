<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Controller\Adminhtml\Redirect;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;

class MassDelete extends MassActionAbstract
{
    /**
     * @param AbstractDb $collection
     */
    protected function doAction(AbstractDb $collection)
    {
        $notDeletedItems = 0;
        $collectionSize = $collection->getSize();
        foreach ($collection as $redirect) {
            try {
                $this->redirectRepository->delete($redirect);
            } catch (CouldNotDeleteException $e) {
                $notDeletedItems++;
                $this->logger->error($e->getMessage());
            }
        }
        if ($notDeletedItems) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have not been deleted.', $notDeletedItems)
            );
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', abs($collectionSize - $notDeletedItems))
        );
    }
}
