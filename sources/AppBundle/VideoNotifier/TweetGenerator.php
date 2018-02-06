<?php

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Talk;
use Twitter_Validation;

class TweetGenerator
{
    const TWEET_MAX_LENGTH = 280;

    /**
     * @var Twitter_Validation
     */
    protected $twitterValidation;

    public function __construct()
    {
        $this->twitterValidation = new Twitter_Validation();
    }

    /**
     * @param Talk $talk
     * @param \AppBundle\Event\Model\Speaker[] $speakers
     *
     * @return string
     */
    public function generate(Talk $talk, array $speakers)
    {
        $twitters = [];
        foreach ($speakers as $speaker) {
            if (!$twitter = $speaker->getCleanedTwitter()) {
                $twitters[] = $speaker->getLabel();
            } else {
                $twitters[] = "@" . $twitter;
            }
        }

        if (!count($twitters)) {
            throw new \InvalidArgumentException("No speaker found");
        }

        $speakersText = implode(' et ', $twitters);

        $talkTitle = $talk->getTitle();
        $talkTitleEndsWithPunctuation = in_array(substr($talkTitle, -1), ["?", "!"]);

        $firstPart = $talk->getTitle() . ($talkTitleEndsWithPunctuation ? '' : '.') ;
        $watchAgain = " à revoir sur le site de l'AFUP : ";
        $address = 'https://afup.org/talks/' . $talk->getId() . '-' . $talk->getSlug();

        $status = $firstPart . ' La conférence de ' . $speakersText . $watchAgain . $address;

        if ($this->getTweetLength($status) > self::TWEET_MAX_LENGTH) {
            $status = $firstPart . ' Par ' . $speakersText . $watchAgain . $address;
        }

        if ($this->getTweetLength($status) > self::TWEET_MAX_LENGTH) {
            $status = $firstPart . ' Par ' . $speakersText . ' ' . $address;
        }

        if ($this->getTweetLength($status) > self::TWEET_MAX_LENGTH) {
            $status = $firstPart . ' Par ' . $speakersText . ' ' . $address;
        }

        $status = preg_replace('/\s+/', ' ', $status);

        if (($length = $this->getTweetLength($status)) > self::TWEET_MAX_LENGTH) {
            throw new \LengthException(sprintf("Taille du tweet (%s) incorrecte : %s ", $length, var_export($status, true)));
        }

        return $status;
    }

    /**
     * @param string $tweet
     *
     * @return int
     */
    protected function getTweetLength($tweet)
    {
        return $this->twitterValidation->getTweetLength($tweet);
    }
}
