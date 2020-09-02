<?php

namespace TestMonitor\ActiveCampaign\Actions;

use TestMonitor\ActiveCampaign\Resources\Order;
use TestMonitor\ActiveCampaign\Resources\OrderDiscount;
use TestMonitor\ActiveCampaign\Resources\OrderProduct;

trait ManagesOrders
{
    use ImplementsActions;

    /**
     * Returns all orders.
     *
     * @return Order[]
     */
    public function orders($filters = [])
    {
        $query = [];
        foreach ($filters as $name => $value) {
            $query['filter['.$name.']'] = $value;
        }

        return $this->transformCollection(
            $this->get('ecomOrders', $query ? ['query' => $query] : []),
            Order::class,
            'ecomOrders'
        );
    }

    /**
     * Returns order by ID.
     *
     * @param string $id
     *
     * @return Order|null
     */
    public function getOrder($id)
    {
        $orders = $this->transformCollection(
            $this->get('ecomOrders/'.$id),
            Order::class
        );

        return $this->getOrderWithProducts(array_shift($orders));
    }

    /**
     * Creates a new order.
     *
     * @param Order $order The order object to create, any null value will be ignored
     *
     * @return Order
     */
    public function createOrder(Order $order)
    {
        $orders = $this->transformCollection(
            $this->post('ecomOrders', ['json' => ['ecomOrder' => $this->getOrderParams($order)]]),
            Order::class
        );

        return array_shift($orders);
    }

    /**
     * Removes order.
     *
     * @param int $id ID of the order to delete
     *
     * @throws \TestMonitor\ActiveCampaign\Exceptions\NotFoundException
     */
    public function deleteOrder($id)
    {
        $this->delete('ecomOrders/'.$id);
    }

    /**
     * Updates a order.
     *
     * @param Order|int $order   id of order or order object
     * @param array     $values  list of order parameters to update
     *
     * @return Order|null
     */
    public function updateOrder($order, array $values = [])
    {
        if (is_numeric($order)) {
            $order = $this->getOrder($order);
        }

        if ($order instanceof Order) {
            if (empty($values)) {
                $values = $this->getOrderParams($order);
            }

            $orders = $this->transformCollection(
                $this->put('ecomOrders/'.$order->id, ['json' => ['ecomOrder' => $values]]),
                Order::class);

            return array_shift($orders);
        }

        return null;
    }

    /**
     * Returns array to be used with create or update operations.
     *
     * @param Order $order
     *
     * @return array
     */
    private function getOrderParams(Order $order)
    {
        $params = get_object_vars($order);
        if (isset($params['id'])) {
            unset($params['id']);
        }

        $cleanParams = [];
        foreach ($params as $name => $value) {
            // skip undefined values
            if (is_null($value)) {
                continue;
            }

            // convert dates from timestamp or text format to required ISO 8601
            if (in_array($name, ['externalCreatedDate', 'externalUpdatedDate', 'abandonedDate'])) {
                $value = date('c', is_numeric($value)
                    ? $value
                    : strtotime($value));
            }
            // convert OrderProduct objects into arrays
            elseif ($name === 'orderProducts') {
                foreach ($value as $orderProduct) {
                    if ($orderProduct instanceof OrderProduct) {
                        $orderProduct = get_object_vars($orderProduct);
                    }
                    if (! is_array($orderProduct)) {
                        continue;
                    }
                }
            }
            // convert OrderDiscount objects into arrays
            elseif ($name === 'orderDiscounts') {
                foreach ($value as $orderDiscount) {
                    if ($orderDiscount instanceof OrderDiscount) {
                        $orderDiscount = get_object_vars($orderDiscount);
                    }
                    if (! is_array($orderDiscount)) {
                        continue;
                    }
                }
            }
            $cleanParams[$name] = $value;
        }

        return $cleanParams;
    }

    /**
     * Returns Order object with loaded orderProducts and orderDiscounts which requires extra requests.
     *
     * @param Order $order
     *
     * @return Order
     */
    private function getOrderWithProducts(Order $order)
    {
        if ($order) {
            if (empty($order->orderProducts)) {
                $order->orderProducts = $this->transformCollection(
                    $this->get('ecomOrders/'.$order->id.'/orderProducts'),
                    OrderProduct::class,
                    'ecomOrderProducts');
            }

            if (empty($order->orderDiscounts)) {
                $order->orderDiscounts = $this->transformCollection(
                    $this->get('ecomOrders/'.$order->id.'/orderDiscounts'),
                    OrderDiscount::class,
                    'ecomOrderDiscounts');
            }
        }

        return $order;
    }
}
