<?php

declare(strict_types=1);


namespace AppBundle\Mailchimp;

use Illuminate\Support\Collection;

class Mailchimp
{
    private \Mailchimp\Mailchimp $client;

    public function __construct(\Mailchimp\Mailchimp $client)
    {
        $this->client = $client;
    }

    /**
     * Subscribe an address to a list
     *
     * @param string $email
     * @return Collection
     */
    public function subscribeAddress(string $list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            // Le status pending permet d'être en double opt-in, que Mailchimp envoie
            // un mail de confirmation pour valider l'inscription à la newsletter
            ['status' => 'pending', 'email_address' => $email, 'language' => 'fr']
        );
    }

    public function subscribeAddressWithoutConfirmation(string $list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            ['status' => 'subscribed', 'email_address' => $email, 'language' => 'fr']
        );
    }

    const MAX_MEMBERS_PER_PAGE = 50;

    public function getAllSubscribedMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'subscribed');
    }

    public function getAllUnSubscribedMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'unsubscribed');
    }

    public function getAllCleaneddMembersAddresses(string $list): array
    {
        return $this->callMembersAddresses($list, 'cleaned');
    }

    private function callMembersAddresses(string $list, string $status): array
    {
        $response = $this->client->get(
            'lists/' . $list . '/members',
            [
                'count' => 0,
                'status' => $status,
            ]
        );

        $totalItems = $response->get('total_items');

        $addresses = [];

        for ($i=0; $i<=ceil($totalItems / self::MAX_MEMBERS_PER_PAGE); $i++) {
            $response = $this->client->get(
                'lists/' . $list . '/members',
                [
                    'count' => self::MAX_MEMBERS_PER_PAGE,
                    'offset' => $i * self::MAX_MEMBERS_PER_PAGE,
                    'fields' => 'members.email_address',
                    'status' => $status,
                ]
            );

            foreach ($response->all() as $member) {
                $addresses[] = $member->email_address;
            }
        }

        return array_unique($addresses);
    }

    /**
     * Unsubscribe an address from a list
     *
     * @param string $email
     * @return Collection
     */
    public function unSubscribeAddress(string $list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            ['status' => 'unsubscribed', 'email_address' => $email]
        );
    }

    /**
     * @param $list
     * @param $email
     * @return Collection
     */
    public function archiveAddress(string $list, $email)
    {
        return $this->client->delete(
            'lists/' . $list . '/members/' . $this->getAddressId($email)
        );
    }

    /**
     * Mailchimp uses a predictable id to allow upsert operations on subscriptions.
     * It's based on a hash of the email.
     *
     * @param string $email
     */
    private function getAddressId($email): string
    {
        return md5(strtolower($email));
    }


    public function createTemplate($title, $html)
    {
        return $this->client->post(
            'templates',
            [
                'name' => $title,
                'html' => $html,
            ]
        );
    }

    /**
     * @param string $list
     */
    public function createCampaign($list, array $settings)
    {
        return $this->client->post(
            'campaigns',
            [
                'type' => 'regular',
                'recipients' => [
                    'list_id' => $list,
                ],
                'settings' => $settings
            ]
        );
    }

    /**
     * @param int $campaignId
     * @param \Datetime $datetime
     */
    public function scheduleCampaign($campaignId, $datetime)
    {
        return $this->client->post('campaigns/' . $campaignId . '/actions/schedule', [
            'schedule_time' => $datetime->format('c')
        ]);
    }
}
