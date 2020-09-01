<?php

namespace TestMonitor\ActiveCampaign\Resources;

class Connection extends Resource
{
    /**
     * The id of the contact.
     *
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $externalid;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $logoUrl;

    /**
     * @var string
     */
    public $linkUrl;

    /**
     * The status of the connection (0 = error; 1 = connected)
     *
     * Available only on update operation.
     *
     * @var int
     */
    public $status;

    /**
     * The status of a sync triggered on the connection (0 = sync stopped; 1 = sync running).
     *
     * Available only on update operation.
     *
     * @var int
     */
    public $syncStatus;
}
