<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Model;

use Magento\Framework\Model\AbstractModel;
use SetuBridge\Color\Api\Data\DataInterface;

class Data extends AbstractModel implements DataInterface
{
    const CACHE_TAG = 'personalization_color';

    protected function _construct()
    {
        $this->_init('SetuBridge\Color\Model\ResourceModel\Data');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getColorName()
    {
        return $this->getData(DataInterface::COLOR_NAME);
    }

    public function setColorName($colorname)
    {
        return $this->setData(DataInterface::COLOR_NAME, $colorname);
    }

    public function getColorCode()
    {
        return $this->getData(DataInterface::COLOR_CODE);
    }

    public function setColorCode($colorcode)
    {
        return $this->setData(DataInterface::COLOR_CODE, $colorcode);
    }

    public function getCreatedAt()
    {
        return $this->getData(DataInterface::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(DataInterface::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(DataInterface::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(DataInterface::UPDATED_AT, $updatedAt);
    }
}
