<?php

namespace TestMonitor\ActiveCampaign\Resources;

class OrderProduct extends Resource
{
    /**
     * The name of the product.
     *
     * @var string
     */
    public $name;

    /**
     * The price of the product, in cents. (i.e. $456.78 => 45678). Must be greater than or equal to zero.
     *
     * @var int
     */
    public $price;

    /**
     * The quantity ordered.
     *
     * @var int
     */
    public $quantity;

    /**
     * The id of the product in the external service.
     *
     * @var string
     */
    public $externalid;

    /**
     * The category of the product.
     *
     * @var string
     */
    public $category;

    /**
     * The SKU for the product.
     *
     * @var string
     */
    public $sku;

    /**
     * The description of the product.
     *
     * @var string
     */
    public $description;

    /**
     * An Image URL that displays an image of the product.
     *
     * @var string
     */
    public $imageUrl;

    /**
     * A URL linking to the product in your store.
     *
     * @var string
     */
    public $productUrl;
}
