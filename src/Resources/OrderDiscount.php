<?php

namespace TestMonitor\ActiveCampaign\Resources;

class OrderDiscount extends Resource
{
    /**
     * The discount code or name of the discount
     *
     * @var string
     */
    public $name;

    /**
     * The type of discount, either 'order' for discount on the order, or 'shipping' for free shipping.
     *
     * @var string
     */
    public $type;

    /**
     * The amount of the discount in cents.
     *
     * @var int
     */
    public $discountAmount;
}
