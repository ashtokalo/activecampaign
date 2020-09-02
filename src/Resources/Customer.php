<?php

namespace TestMonitor\ActiveCampaign\Resources;

class Customer extends Resource
{
    /**
     * @var int
     */
    public $id;

    /**
     * The id of the connection object for the service where the customer originates.
     *
     * @var int
     */
    public $connectionid;

    /**
     * The id of the customer in the external service.
     *
     * @var string
     */
    public $externalid;

    /**
     * The email address of the customer.
     *
     * @var string
     */
    public $email;

    /**
     * @var int
     */
    public $totalRevenue;

    /**
     * @var int
     */
    public $totalOrders;

    /**
     * @var int
     */
    public $totalProducts;

    /**
     * @var int
     */
    public $avgRevenuePerOrder;

    /**
     * @var string
     */
    public $avgProductCategory;

    /**
     * Indication of whether customer has opt-ed in to marketing communications.
     *
     * @var string 0 = not opted-in, 1 = opted-in.
     */
    public $acceptsMarketing;

    /**
     * Creates new Order object linked with customer and related connection.
     *
     * @param string     $externalid The id of the order in the external service.
     * @param int        $totalPrice The total price of the order in cents, including tax and shipping charges.
     * @param string     $currency   The currency of the order (3-digit ISO code, e.g., 'USD').
     * @param int|string $createdAt  The date the order was placed.
     * @param int        $quantity   (optional) The quantity ordered.
     *
     * @return Order
     */
    public function newOrder($externalid, $totalPrice, $currency, $createdAt, $quantity = 1)
    {
        $order = new Order([], $this->activeCampaign);
        $order->externalid = $externalid;
        $order->totalPrice = $totalPrice;
        $order->currency = $currency;
        $order->externalCreatedDate = $createdAt;
        $order->connectionid = $this->connectionid;
        $order->customerid = $this->id;
        $order->source = 1;
        $order->quantity = $quantity;
        $order->email = $this->email;

        return $order;
    }
}
