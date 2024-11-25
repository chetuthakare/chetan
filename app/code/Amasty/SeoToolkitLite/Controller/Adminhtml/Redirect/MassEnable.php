<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Controller\Adminhtml\Redirect;

use Magento\Framework\Data\Collection\AbstractDb;

class MassEnable extends MassActionAbstract
{
    /**
     * @param AbstractDb $collection
     */
    public function doAction(AbstractDb $collection)
    {
        $collectionSize = $collection->getSize();
        foreach ($collection as $redirect) {
            $redirect->setStatus(1);
            $this->redirectResource->save($redirect);
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been activated.', $collectionSize));
    }
}
