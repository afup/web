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
            ['status' => 'subscribed', 'email_address' => $email]
        );
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
     * Mailchimp uses a predictable id to allow upsert operations on subscriptions.
     * It's based on a hash of the email.
     *
     * @param string $email
     * @return string
     */
    private function getAddressId($email)
    {
        return md5($email);
    }
}
