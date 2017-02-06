<?php

namespace AppBundle\Indexation\Talks;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

class Transformer
{
    /**
     * @param Planning $planning
     * @param Talk $talk
     * @param Event $event
     * @param Speaker[]|\Traversable $speakers
     *
     * @return array
     */
    public function transform(Planning $planning, Talk $talk, Event $event, \Traversable $speakers)
    {
        $item = [
            'planning_id' => $planning->getId(),
            'talk_id' => $talk->getId(),
            'url_key' => $talk->getId() . '-' . $talk->getSlug(),
            'title' => $talk->getTitle(),
            'event' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start_date' => $event->getDateStart() ? $event->getDateStart()->format('Y-m-d') : null,
            ],
            'type' => [
                'id' => $talk->getType(),
                'label' => $talk->getTypeLabel(),
            ],
            'has_video' => $talk->hasYoutubeId(),
            'has_slides' => $talk->hasSlidesUrl(),
            'has_joindin' => $talk->hasJoindinId(),
            'has_blog_post' => $talk->hasBlogPostUrl(),
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
