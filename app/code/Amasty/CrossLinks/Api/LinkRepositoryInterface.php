<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface LinkRepositoryInterface
{

    /**
     * Loads a specified abstract giftcard entity.
     *
     * @param int $id The abstract giftcard entity ID.
     * @return \Amasty\CrossLinks\Api\LinkInterface abstract giftcard entity interface.
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Performs persist operations for a specified abstract giftcard entity.
     *
     * @param \Amasty\CrossLinks\Api\LinkInterface $entity The abstract giftcard entity ID.
     * @return \Amasty\CrossLinks\Api\LinkInterface abstract giftcard entity interface.
     */
    public function save(LinkInterface $entity);

    /**
     * Performs persist operations for a specified abstract giftcard entity.
     *
     * @param \Amasty\CrossLinks\Api\LinkInterface $entity The abstract giftcard entity ID.
     * @return \Amasty\CrossLinks\Api\LinkInterface abstract giftcard entity interface.
     */
    public function delete(LinkInterface $entity);
}
