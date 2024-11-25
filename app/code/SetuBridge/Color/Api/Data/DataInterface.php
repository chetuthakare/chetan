<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Api\Data;

interface DataInterface
{
    const DATA_ID           = 'color_id';
    const COLOR_NAME        = 'color_name';
    const COLOR_CODE  = 'color_code';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';


    public function getId();

    public function setId($id);

    public function getColorName();

    public function setColorName($colorname);

    public function getColorCode();

    public function setColorCode($colorcode);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
