<?php

namespace TestMonitor\ActiveCampaign\Actions;

use TestMonitor\ActiveCampaign\Resources\Connection;

trait ManagesConnections
{
    use ImplementsActions;

    /**
     * Returns all connections.
     *
     * @return Connection[]
     */
    public function connections()
    {
        return $this->transformCollection(
            $this->get('connections'),
            Connection::class,
            'connections'
        );
    }

    /**
     * Returns connection by ID.
     *
     * @param string $id
     *
     * @return Connection|null
     */
    public function getConnection($id)
    {
        $connections = $this->transformCollection(
            $this->get('connections/' . $id),
            Connection::class
        );

        return array_shift($connections);
    }

    /**
     * Finds connection by external service name or external id associated with a connection.
     *
     * @param string $service    The name of the service.
     * @param string $externalid The id of the account in the external service.
     *
     * @return null|Connection
     */
    public function findConnection($service = null, $externalid = null)
    {
        $query = [];
        if ($service) $query['filters[service]'] = $service;
        if ($externalid) $query['filters[externalid]'] = $externalid;
        if ($query)
        {
            $connections = $this->transformCollection(
                $this->get('connections', ['query' => $query]),
                Connection::class,
                'connections'
            );
            return array_shift($connections);
        }

        return null;
    }

    /**
     * Creates a new connection.
     *
     * @param string $service The name of the service.
     * @param string $externalid The id of the account in the external service.
     * @param string $name The name associated with the account in the external service.
     * @param string $logoUrl The URL to a logo image for the external service.
     * @param string $linkUrl The URL to a page where the integration with the external
     *  service can be managed in the third-party's website.
     *
     * @return Connection
     */
    public function createConnection($service, $externalid, $name, $logoUrl, $linkUrl)
    {
        $connections = $this->transformCollection(
            $this->post('connections', ['json' => ['connection' => [
                'service'    => $service,
                'externalid' => $externalid,
                'name'       => $name,
                'logoUrl'    => $logoUrl,
                'linkUrl'    => $linkUrl,
            ]]]),
            Connection::class
        );

        return array_shift($connections);
    }

    /**
     * Removes connection.
     *
     * @param int $id ID of the connection to delete
     *
     * @throws \TestMonitor\ActiveCampaign\Exceptions\NotFoundException
     */
    public function deleteConnection($id)
    {
        $this->delete('connections/'.$id);
    }

    /**
     * Updates a connection.
     *
     * @param Connection|int $connection    id of connection or connection object
     * @param array          $values        list of connection parameters to update
     *
     * @return Connection|null
     */
    public function updateConnection($connection, array $values = [])
    {
        if (is_numeric($connection))
        {
            $connection = $this->getConnection($connection);
        }

        if ($connection instanceof Connection)
        {
            if (empty($values))
            {
                $values = [
                    'service'    => $connection->service,
                    'externalid' => $connection->externalid,
                    'name'       => $connection->name,
                    'logoUrl'    => $connection->logoUrl,
                    'linkUrl'    => $connection->linkUrl,
                    'status'     => $connection->status,
                    'syncStatus' => $connection->syncStatus,
                ];
            }

            $connections = $this->transformCollection(
                $this->put('connections/' . $connection->id, ['json' => ['connection' => $values]]),
                Connection::class);

            return array_shift($connections);
        }

        return null;
    }
}
