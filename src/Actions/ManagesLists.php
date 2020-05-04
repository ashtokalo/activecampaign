<?php

namespace TestMonitor\ActiveCampaign\Actions;

use TestMonitor\ActiveCampaign\Resources\Contact;
use TestMonitor\ActiveCampaign\Resources\ContactsList;

trait ManagesLists
{
    use ImplementsActions;

    /**
     * Returns all lists.
     *
     * @return ContactsList[]
     */
    public function lists()
    {
        return $this->transformCollection(
            $this->get('lists'),
            ContactsList::class,
            'lists'
        );
    }

    /**
     * Returns list by ID.
     *
     * @param string $id
     *
     * @return ContactsList|null
     */
    public function getList($id)
    {
        $lists = $this->transformCollection(
            $this->get('lists/' . $id),
            ContactsList::class
        );

        return array_shift($lists);
    }

    /**
     * Finds list by it's name or URL-safe name.
     *
     * @param string $name name of list to find
     *
     * @return null|ContactsList
     */
    public function findList($name)
    {
        $lists = $this->transformCollection(
            $this->get('lists', ['query' => ['filters[name]' => $name]]),
            ContactsList::class,
            'lists'
        );

        return array_shift($lists);
    }

    /**
     * Creates a new list.
     *
     * @param string $name Name of the list to create
     * @param string $senderUrl The website URL this list is for.
     * @param array  $params other options to create list
     *
     * @return ContactsList
     */
    public function createList($name, $senderUrl, $params = [])
    {
        $params['name'] = $name;
        if (! isset($params['stringid'])) {
            $params['stringid'] = strtolower(preg_replace('/[^A-Z0-9]+/i', '-', $name));
        }
        $params['sender_url'] = $senderUrl;
        $params['sender_reminder'] = 'You signed up for my mailing list.';

        $lists = $this->transformCollection(
            $this->post('lists', ['json' => ['list' => $params]]),
            ContactsList::class
        );

        return array_shift($lists);
    }

    /**
     * Removes list.
     *
     * @param int $id ID of the list to delete
     *
     * @throws \TestMonitor\ActiveCampaign\Exceptions\NotFoundException
     */
    public function deleteList($id)
    {
        $this->delete('lists/'.$id);
    }

    /**
     * Subscribe a contact to a list or unsubscribe a contact from a list.
     *
     * @param ContactsList|string|int $list List, name of list or ID of the list
     * @param Contact|string|int $contact Contact, email or ID of the contact
     * @param bool $subscribe TRUE to subscribe, FALSE otherwise
     */
    public function updateListStatus($list, $contact, $subscribe)
    {
        if (is_numeric($list))
        {
            $list = $this->getList($list);
        }
        else if (is_string($list))
        {
            $list = $this->findList($list);
        }

        if (is_numeric($contact))
        {
            $contact = $this->getContact($contact);
        }
        else if (is_string($contact))
        {
            $contact = $this->findContact($contact);
        }

        if ($list instanceof ContactsList && $contact instanceof Contact)
        {
            $this->post('contactLists', ['json' => [
                'contactList' => [
                    'list' => $list->id,
                    'contact' => $contact->id,
                    'status' => $subscribe ? 1 : 2,
                ], ]]);
        }
    }

    /**
     * Get all contacts related to the list.
     *
     * @return Contact[]
     */
    public function contactsByList($listId)
    {
        return $this->transformCollection(
            $this->get('contacts', ['query' => ['listid' => $listId]]),
            Contact::class,
            'contacts'
        );
    }
}
