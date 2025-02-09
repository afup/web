<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

class Purchase
{
    /**
     * @var bool
     */
    private $companyCitation = true;

    /**
     * @var bool
     */
    private $newsletterAfup = false;



    /**
     * @return bool
     */
    public function getCompanyCitation()
    {
        return $this->companyCitation;
    }

    /**
     * @param bool $companyCitation
     */
    public function setCompanyCitation($companyCitation): self
    {
        $this->companyCitation = $companyCitation;
        return $this;
    }

    /**
     * @return bool
     */
    public function getNewsletterAfup()
    {
        return $this->newsletterAfup;
    }

    /**
     * @param bool $newsletterAfup
     */
    public function setNewsletterAfup($newsletterAfup): self
    {
        $this->newsletterAfup = $newsletterAfup;
        return $this;
    }
}
