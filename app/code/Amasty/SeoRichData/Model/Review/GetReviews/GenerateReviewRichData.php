<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\Review\GetReviews;

use Amasty\SeoRichData\Model\Review\Review;

class GenerateReviewRichData
{
    /**
     * @param Review $review
     * @return array
     */
    public function execute(Review $review): array
    {
        return [
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $review->getNickname()
            ],
            'datePublished' => $review->getCreatedAt(),
            'name' => $review->getTitle(),
            'reviewBody' => $review->getDetail(),
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review->getRatingValue(),
                'bestRating' => $review->getBestRating()
            ]
        ];
    }
}
