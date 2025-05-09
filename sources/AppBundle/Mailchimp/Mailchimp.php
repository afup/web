<?php

declare(strict_types=1);


namespace AppBundle\Mailchimp;

use DrewM\MailChimp\MailChimp as DrewMailChimp;

class Mailchimp
{
    private const MAX_MEMBERS_PER_PAGE = 50;

    public function __construct(private readonly DrewMailChimp $client)
    {
    }

    /**
     * Subscribe an address to a list
     */
    public function subscribeAddress(string $list, string $email)
    {
        // Le status pending permet d'être en double opt-in, que Mailchimp envoie
        // un mail de confirmation pour valider l'inscription à la newsletter
        return $this->client->put('lists/' . $list . '/members/' . $this->getAddressId($email), [
            'status' => 'pending',
            'email_address' => $email,
            'language' => 'fr',
        ]);
    }

    public function subscribeAddressWithoutConfirmation(string $list, string $email)
    {
        return $this->client->put('lists/' . $list . '/members/' . $this->getAddressId($email), [
            'status' => 'subscribed',
            'email_address' => $email,
            'language' => 'fr',
        ]);
    }

    public function getAllSubscribedMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'subscribed');
    }

    public function getAllUnSubscribedMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'unsubscribed');
    }

    public function getAllCleanedMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'cleaned');
    }

    /**
     * @return string[]
     */
    private function callMembersAddresses(string $list, string $status): array
    {
        $response = $this->client->get('lists/' . $list . '/members', [
            'count' => 0,
            'status' => $status,
        ]);

        $addresses = [];

        $max = ceil($response['total_items'] / self::MAX_MEMBERS_PER_PAGE);
        for ($i = 0; $i <= $max; $i++) {
            $response = $this->client->get('lists/' . $list . '/members', [
                'count' => self::MAX_MEMBERS_PER_PAGE,
                'offset' => $i * self::MAX_MEMBERS_PER_PAGE,
                'fields' => 'members.email_address',
                'status' => $status,
            ]);

            foreach ($response['members'] as $member) {
                $addresses[] = $member->email_address;
            }
        }

        return array_unique($addresses);
    }

    /**
     * Unsubscribe an address from a list
     */
    public function unSubscribeAddress(string $list, string $email)
    {
        return $this->client->put('lists/' . $list . '/members/' . $this->getAddressId($email), [
            'status' => 'unsubscribed',
            'email_address' => $email,
        ]);
    }

    public function archiveAddress(string $list, string $email)
    {
        return $this->client->delete('lists/' . $list . '/members/' . $this->getAddressId($email));
    }

    /**
     * Mailchimp uses a predictable id to allow upsert operations on subscriptions.
     * It's based on a hash of the email.
     */
    private function getAddressId(string $email): string
    {
        return DrewMailChimp::subscriberHash($email);
    }


    public function createTemplate(string $title, string $html)
    {
        return $this->client->post('templates', [
            'name' => $title,
            'html' => $html,
        ]);
    }

    public function createCampaign(string $list, array $settings): array
    {
        return $this->client->post('campaigns', [
            'type' => 'regular',
            'recipients' => [
                'list_id' => $list,
            ],
            'settings' => $settings,
        ]);
    }

    public function scheduleCampaign(string $campaignId, \DateTime $datetime)
    {
        return $this->client->post('campaigns/' . $campaignId . '/actions/schedule', [
            'schedule_time' => $datetime->format('c'),
        ]);
    }
}
