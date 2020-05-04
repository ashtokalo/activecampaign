<?php

namespace TestMonitor\ActiveCampaign\Actions;

use TestMonitor\ActiveCampaign\Resources\Automation;
use TestMonitor\ActiveCampaign\Resources\Contact;
use TestMonitor\ActiveCampaign\Resources\ContactAutomation;
use TestMonitor\ActiveCampaign\Resources\ContactTag;
use TestMonitor\ActiveCampaign\Resources\Tag;

trait ManagesContacts
{
    use ImplementsActions;

    /**
     * Get all contacts.
     *
     * @return array
     */
    public function contacts()
    {
        return $this->transformCollection(
            $this->get('contacts'),
            Contact::class,
            'contacts'
        );
    }

    /**
     * Find contact by email.
     *
     * @param string $email
     *
     * @return Contact|null
     */
    public function findContact($email)
    {
        $contacts = $this->transformCollection(
            $this->get('contacts', ['query' => ['email' => $email]]),
            Contact::class,
            'contacts'
        );

        return array_shift($contacts);
    }

    /**
     * Returns contact by ID.
     *
     * @param $id
     *
     * @return Contact|null
     */
    public function getContact($id)
    {
        $contacts = $this->transformCollection(
            $this->get('contacts/' . $id),
            Contact::class,
            'contactAutomations'
        );

        return array_shift($contacts);
    }

    /**
     * Create new contact.
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param int|null $phone
     *
     * @return Contact|null
     */
    public function createContact($email, $firstName, $lastName, $phone = null)
    {
        $contacts = $this->transformCollection(
            $this->post('contacts', ['json' => ['contact' => compact('email', 'firstName', 'lastName', 'phone')]]),
            Contact::class
        );

        return array_shift($contacts);
    }

    /**
     * Find or create a contact.
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param int|null $phone
     *
     * @return Contact
     */
    public function findOrCreateContact($email, $firstName, $lastName, $phone = null)
    {
        $contact = $this->findContact($email);

        if ($contact instanceof Contact) {
            return $contact;
        }

        return $this->createContact($email, $firstName, $lastName, $phone);
    }

    /**
     * Updates a contact.
     *
     * Values to update could be `email`, `firstName`, `lastName` and `phone`.
     *
     * @param Contact|int|string $contact
     * @param array              $values list of contact parameters to update
     *
     * @return Contact|null
     */
    public function updateContact($contact, array $values = [])
    {
        if (is_numeric($contact))
        {
            $contact = $this->getContact($contact);
        }
        else if (is_string($contact))
        {
            $contact = $this->findContact($contact);
        }

        if ($contact instanceof Contact)
        {
            $contacts = $this->transformCollection(
                $this->put('contacts/' . $contact->id, ['json' => ['contact' => $values]]),
                Contact::class);

            return array_shift($contacts);
        }

        return null;
    }

    /**
     * Get all automations of a contact.
     *
     * @param \TestMonitor\ActiveCampaign\Resources\Contact $contact
     *
     * @return array
     */
    public function contactAutomations(Contact $contact)
    {
        return $this->transformCollection(
            $this->get("contacts/{$contact->id}/contactAutomations"),
            ContactAutomation::class,
            'contactAutomations'
        );
    }

    /**
     * Get all tags of a contact.
     *
     * @param \TestMonitor\ActiveCampaign\Resources\Contact $contact
     *
     * @return array
     */
    public function contactTags(Contact $contact)
    {
        return $this->transformCollection(
            $this->get("contacts/{$contact->id}/contactTags"),
            ContactTag::class,
            'contactTags'
        );
    }

    /**
     * Removing a automation from a contact.
     *
     * @param \TestMonitor\ActiveCampaign\Resources\Contact $contact
     * @param \TestMonitor\ActiveCampaign\Resources\Automation $automation
     */
    public function removeAutomationFromContact(Contact $contact, Automation $automation)
    {
        $contactAutomations = $this->contactAutomations($contact);

        $contactAutomation = current(array_filter($contactAutomations, function ($contactAutomation) use ($automation) {
            return $contactAutomation->automation == $automation->id;
        }));

        if (empty($contactAutomation)) {
            return;
        }

        $this->delete("contactAutomations/{$contactAutomation->id}");
    }

    /**
     * Removing all automations from a contact.
     *
     * @param \TestMonitor\ActiveCampaign\Resources\Contact $contact
     */
    public function removeAllAutomationsFromContact(Contact $contact)
    {
        $contactAutomations = $this->contactAutomations($contact);

        foreach ($contactAutomations as $contactAutomation) {
            $this->delete("contactAutomations/{$contactAutomation->id}");
        }
    }

    /**
     * Removing a tag from a contact.
     *
     * @param \TestMonitor\ActiveCampaign\Resources\Contact $contact
     * @param \TestMonitor\ActiveCampaign\Resources\Tag $tag
     */
    public function removeTagFromContact(Contact $contact, Tag $tag)
    {
        $contactTags = $this->contactTags($contact);

        $contactTag = current(array_filter($contactTags, function ($contactTag) use ($tag) {
            return $contactTag->tag == $tag->id;
        }));

        if (empty($contactTag)) {
            return;
        }

        $this->delete("contactTags/{$contactTag->id}");
    }
}
