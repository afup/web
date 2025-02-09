<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Talks;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

class Transformer
{
    /**
     * @param Speaker[]|\Traversable $speakers
     *
     */
    public function transform(Planning $planning, Talk $talk, Event $event, \Traversable $speakers): array
    {
        $item = [
            'planning_id' => $planning->getId(),
            'talk_id' => $talk->getId(),
            'url_key' => $talk->getUrlKey(),
            'title' => $talk->getTitle(),
            'event' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start_date' => $event->getDateStart() instanceof \DateTime ? $event->getDateStart()->format('Y-m-d') : null,
            ],
            'type' => [
                'id' => $talk->getType(),
                'label' => $talk->getTypeLabel(),
            ],
            'has_video' => $talk->hasYoutubeId(),
            'has_slides' => $talk->hasSlidesUrl(),
            'has_joindin' => $talk->hasJoindinId(),
            'has_blog_post' => $talk->hasBlogPostUrl(),
            'video_has_fr_subtitles' => $talk->getVideoHasFrSubtitles(),
            'video_has_en_subtitles' => $talk->getVideoHasEnSubtitles(),
            'language' => [
                'code' => $talk->getLanguageCode(),
                'label' => $talk->getLanguageLabel(),
            ],
        ];

        $speakersLabels = [];
        foreach ($speakers as $speaker) {
            $speakersLabels[] = $speaker->getLabel();
            $item['speakers'][] = [
                'id' => $speaker->getId(),
                'first_name' => $speaker->getFirstname(),
                'last_name' => $speaker->getLastname(),
                'label' => $speaker->getLabel(),
            ];
        }

        $item['speakers_label'] = implode(' et ', $speakersLabels);

        if (null !== ($youtubeUrl = $talk->getYoutubeUrl())) {
            $item['video_url'] = $youtubeUrl;
            $item['video_id'] = $talk->getYoutubeId();
        }

        if (null !== ($slidesUrl = $talk->getSlidesUrl())) {
            $item['slides_url'] = $slidesUrl;
        }

        if (null !== ($joindinUrl = $talk->getJoindinUrl())) {
            $item['joindin_url'] = $joindinUrl;
        }

        if (null !== ($blogPostUrl = $talk->getBlogPostUrl())) {
            $item['blog_post_url'] = $blogPostUrl;
        }

        return $item;
    }
}
