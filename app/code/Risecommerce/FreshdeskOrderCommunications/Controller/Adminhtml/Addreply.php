<?php

/**
 * * @copyright Copyright (c) 2017-2021 Risecommerce IT Solutions Pvt Ltd. All rights reserved.
 
 */

namespace Risecommerce\FreshdeskOrderCommunications\Controller\Adminhtml;

abstract class Addreply extends \Magento\Backend\App\Action
{
    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        // $this->_setActiveMenu(
            // 'Risecommerce_FreshdeskOrderCommunications::portfolio_manage'
        // )->_addBreadcrumb(
            // __('Portfolio'),
            // __('Portfolio')
        // );
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Risecommerce_FreshdeskOrderCommunications::ordercommunication');
    }
}
?>