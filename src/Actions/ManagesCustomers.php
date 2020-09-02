<?php

namespace TestMonitor\ActiveCampaign\Actions;

use TestMonitor\ActiveCampaign\Resources\Customer;

trait ManagesCustomers
{
    use ImplementsActions;

    /**
     * Returns all customers.
     *
     * @return Customer[]
     */
    public function customers()
    {
        return $this->transformCollection(
            $this->get('ecomCustomers'),
            Customer::class,
            'ecomCustomers'
        );
    }

    /**
     * Returns customer by ID.
     *
     * @param string $id
     *
     * @return Customer|null
     */
    public function getCustomer($id)
    {
        $customers = $this->transformCollection(
            $this->get('ecomCustomers/'.$id),
            Customer::class
        );

        return array_shift($customers);
    }

    /**
     * Finds customer by email, id of the customer in the external service and connection id.
     *
     * @param string $email        email
     * @param string $externalid   id of the customer in the external service
     * @param int    $connectionid
     *
     * @return null|Customer
     */
    public function findCustomer($email = null, $externalid = null, $connectionid = null)
    {
        $query = [];
        if ($email) {
            $query['filters[email]'] = $email;
        }
        if ($externalid) {
            $query['filters[externalid]'] = $externalid;
        }
        if ($connectionid) {
            $query['filters[connectionid]'] = $connectionid;
        }

        if ($query) {
            $customers = $this->transformCollection(
                $this->get('ecomCustomers', ['query' => $query]),
                Customer::class,
                'ecomCustomers'
            );

            return array_shift($customers);
        }

        return null;
    }

    /**
     * Creates a new customer.
     *
     * @param int $connectionid The id of the connection object for the service where the customer originates.
     * @param string $externalid The id of the account in the external service.
     * @param string $email The email address of the customer.
     * @param string $acceptsMarketing Indication of whether customer has opt-ed in to marketing communications. 0 = not opted-in, 1 = opted-in.
     *
     * @return Customer
     */
    public function createCustomer($connectionid, $externalid, $email, $acceptsMarketing = 1)
    {
        $customers = $this->transformCollection(
            $this->post('ecomCustomers', ['json' => ['ecomCustomer' => [
                'connectionid'     => $connectionid,
                'externalid'       => $externalid,
                'email'            => $email,
                'acceptsMarketing' => $acceptsMarketing,
            ]]]),
            Customer::class
        );

        return $customers['ecomCustomer'] ?? null;
    }

    /**
     * Removes customer.
     *
     * @param int $id ID of the customer to delete
     *
     * @throws \TestMonitor\ActiveCampaign\Exceptions\NotFoundException
     */
    public function deleteCustomer($id)
    {
        $this->delete('ecomCustomers/'.$id);
    }

    /**
     * Updates a customer.
     *
     * @param Customer|int $customer id of customer or customer object
     * @param array        $values   list of customer parameters to update
     *
     * @return Customer|null
     */
    public function updateCustomer($customer, array $values = [])
    {
        if (is_numeric($customer)) {
            $customer = $this->getCustomer($customer);
        }

        if ($customer instanceof Customer) {
            if (empty($values)) {
                $values = [
                    'externalid'       => $customer->externalid,
                    'connectionid'     => $customer->connectionid,
                    'email'            => $customer->email,
                    'acceptsMarketing' => $customer->acceptsMarketing,
                ];
            }

            $customers = $this->transformCollection(
                $this->put('ecomCustomers/'.$customer->id, ['json' => ['ecomCustomer' => $values]]),
                Customer::class);

            return array_shift($customers);
        }

        return null;
    }
}
