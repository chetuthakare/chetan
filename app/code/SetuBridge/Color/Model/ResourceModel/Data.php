<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Data extends AbstractDb
{
    
    protected $date;

    public function __construct(
        Context $context,
        DateTime $date
    ) {
        $this->date = $date;
        parent::__construct($context);
    }

    protected function _construct()
    {
       
        $this->_init('personalization_color', 'color_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {

        $object->setUpdatedAt($this->date->gmtDate());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->gmtDate());
        }
        return parent::_beforeSave($object);
    }
}
