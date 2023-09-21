<?php

namespace App\Filter;

use App\DTO\PromotionEnquiryInterface;

class LowestPriceFilter implements PromotionsFilterInterface
{

    public function apply(PromotionEnquiryInterface $enquiry): PromotionEnquiryInterface
    {
        $enquiry->setDiscountPrice(50);
        $enquiry->setPrice(100);
        $enquiry->setProductId(3);
        $enquiry->setPromotionName('Black Friday half price sale');

        return $enquiry;
    }
}