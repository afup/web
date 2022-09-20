<?php


namespace AppBundle\Mailchimp;

class Mailchimp
{
    private $client;

    public function __construct(\Mailchimp\Mailchimp $client)
    {
        $this->client = $client;
    }

    /**
     * Subscribe an address to a list
     *
     * @param string $list
     * @param string $email
     * @return \Illuminate\Support\Collection
     */
    public function subscribeAddress($list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            // Le status pending permet d'être en double opt-in, que Mailchimp envoie
            // un mail de confirmation pour valider l'inscription à la newsletter
            ['status' => 'pending', 'email_address' => $email, 'language' => 'fr']
        );
    }

    public function subscribeAddressWithoutConfirmation($list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            ['status' => 'subscribed', 'email_address' => $email, 'language' => 'fr']
        );
    }

    const MAX_MEMBERS_PER_PAGE = 50;

    /**
     * @param string $list
     *
     * @return array
     */
    public function getAllSubscribedMembersAddresses($list)
    {
        return $this->callMembersAddresses($list, 'subscribed');
    }

    /**
     * @param string $list
     *
     * @return array
     */
    public function getAllUnSubscribedMembersAddresses($list)
    {
        return $this->callMembersAddresses($list, 'unsubscribed');
    }

    /**
     * @param string $list
     *
     * @return array
     */
    public function getAllCleaneddMembersAddresses($list)
    {
        return $this->callMembersAddresses($list, 'cleaned');
    }

    /**
     * @param string $list
     * @param string $status
     * @return array
     */
    private function callMembersAddresses($list, $status)
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
     * @param string $list
     * @param string $email
     * @return \Illuminate\Support\Collection
     */
    public function unSubscribeAddress($list, $email)
    {
        return $this->client->put(
            'lists/' . $list . '/members/' . $this->getAddressId($email),
            ['status' => 'unsubscribed', 'email_address' => $email]
        );
    }

    /**
     * @param $list
     * @param $email
     * @return \Illuminate\Support\Collection
     */
    public function archiveAddress($list, $email)
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
     * @return string
     */
    private function getAddressId($email)
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
     * @param array $settings
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
