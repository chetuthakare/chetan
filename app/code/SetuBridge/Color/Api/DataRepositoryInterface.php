<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SetuBridge\Color\Api\Data\DataInterface;

interface DataRepositoryInterface
{

    public function save(DataInterface $data);

    public function getById($dataId);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function delete(DataInterface $data);

    public function deleteById($dataId);
}
