<?php

namespace TestMonitor\ActiveCampaign\Resources;

/**
 * @property Connection $connection
 * @property Customer $customer
 */
class Order extends Resource
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
     * The id of the order in the external service. ONLY REQUIRED IF EXTERNALCHECKOUTID NOT INCLUDED.
     *
     * @var string
     */
    public $externalid;

    /**
     * The id of the order in the external service. ONLY REQUIRED IF EXTERNALCHECKOUTID NOT INCLUDED.
     *
     * @var string
     */
    public $externalcheckoutid;

    /**
     * The order source code.
     *
     * 0 - will not trigger automations,
     * 1 - will trigger automations, orders will only show up on your Ecommerce Dashboard when source = 1
     *
     * @var int
     */
    public $source;

    /**
     * The email address of the customer who placed the order.
     *
     * @var string
     */
    public $email;

    /**
     * Combination of line items and products in an external e-commerce service.
     *
     * @var OrderProduct[]
     */
    public $orderProducts;

    /**
     * The total price of the order in cents, including tax and shipping charges. (i.e. $456.78 => 45678).
     * Must be greater than or equal to zero.
     *
     * @var int
     */
    public $totalPrice;

    /**
     * The total shipping amount in cents for the order.
     *
     * @var int
     */
    public $shippingAmount;

    /**
     * The total tax amount for the order in cents.
     *
     * @var int
     */
    public $taxAmount;

    /**
     * The total discount amount for the order in cents.
     *
     * @var int
     */
    public $discountAmount;

    /**
     * The currency of the order (3-digit ISO code, e.g., 'USD').
     *
     * @var string
     */
    public $currency;

    /**
     * The id of the customer associated with this order.
     *
     * @var int
     */
    public $customerid;

    /**
     * The URL for the order in the external service.
     *
     * @var string
     */
    public $orderUrl;

    /**
     * The date the order was placed.
     *
     * @var string
     */
    public $externalCreatedDate;

    /**
     * The date the order was updated.
     *
     * @var string
     */
    public $externalUpdatedDate;

    /**
     * The date the cart was abandoned. REQUIRED ONLY IF INCLUDING EXTERNALCHECKOUTID.
     *
     * @var string
     */
    public $abandonedDate;

    /**
     * The shipping method of the order.
     *
     * @var string
     */
    public $shippingMethod;

    /**
     * The order number. This can be different than the externalid.
     *
     * @var string
     */
    public $orderNumber;

    /**
     * List of discounts applied to the order.
     *
     * @var OrderDiscount[]
     */
    public $orderDiscounts;

    public function __get($name)
    {
        if ($name === 'customer') {
            return $this->activeCampaign->getCustomer($this->customerid);
        }
        if ($name === 'connection') {
            return $this->activeCampaign->getConnection($this->connectionid);
        }

        throw new \RuntimeException('Getting unknown property: '.get_class($this).'::'.$name);
    }

    /**
     * Adds new order product with mandatory property and returns object
     * to add more data.
     *
     * @param string $name
     * @param int    $price
     * @param int    $quantity
     * @param string $externalid
     *
     * @return OrderProduct
     */
    public function addProduct($name, $price, $quantity, $externalid)
    {
        $orderProduct = new OrderProduct([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'externalid' => $externalid,
        ], $this->activeCampaign);

        if (empty($this->orderProducts)) {
            $this->orderProducts = [];
        }
        $this->orderProducts[] = $orderProduct;

        return $orderProduct;
    }

    /**
     * Adds new order discount to the order.
     *
     * @param string $name           The discount code or name of the discount
     * @param string $type           The type of discount, either 'order' for discount on the order.
     * @param int    $discountAmount The amount of the discount in cents.
     *
     * @return OrderDiscount
     */
    public function addDiscount($name, $type, $discountAmount)
    {
        $orderDiscount = new OrderDiscount([
            'name' => $name,
            'type' => $type,
            'discountAmount' => $discountAmount,
        ]);

        $this->discountAmount[] = $orderDiscount;

        return $orderDiscount;
    }
}
