<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress;

use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Wordpress\Dto\Category;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

final readonly class HttpWordpressClient implements WordpressClient
{
    public function __construct(
        #[Autowire('@http_client.wordpress')]
        private HttpClientInterface $wordpressClient,
        private MapperBuilder $mapperBuilder,
        private Environment $twig,
        #[Autowire('%env(WORDPRESS_INTERVIEW_SPEAKER_TAG_ID)%')]
        private int $interviewSpeakerTagId,
    ) {}

    public function listCategories(): array
    {
        $response = $this->wordpressClient->request('GET', '/wp-json/wp/v2/categories?orderby=id&order=desc&per_page=100&page=1');

        return $this->mapperBuilder
            ->allowSuperfluousKeys()
            ->mapper()
            ->map('array<' . Category::class . '>', Source::json($response->getContent()));
    }

    public function persistInterview(Interview $interview, Event $event, array $speakers, array $talks): ?int
    {
        $categoryId = $event->getInterviewsWordpressCategoryId();
        if ($categoryId === null) {
            throw new LogicException('Configuration de la catégorie WordPress manquante');
        }

        $category = $this->getCategory($categoryId);
        if ($category === null) {
            throw new LogicException('Catégorie WordPress ' . $categoryId . ' inexistante');
        }

        $slugger = new AsciiSlugger();

        $speakerNames = array_map(
            fn(Speaker $speaker) => trim($speaker->firstname . ' ' . $speaker->lastname),
            $speakers,
        );

        $content = $this->twig->render('blog/interview.html.twig', [
            'talkIds' => implode(',', array_unique(array_map(fn(Talk $talk) => $talk->getId(), $talks))),
            'questions' => $interview->questions,
            'event' => $event,
            'category' => $category,
        ]);

        $path = '/wp-json/wp/v2/posts';
        if ($interview->wordpressPostId) {
            $path .= '/' . $interview->wordpressPostId;
        }

        $response = $this->wordpressClient->request('POST', $path, [
            'json' => [
                'title' => 'La parole est aux speakers : ' . implode(', ', $speakerNames),
                'content' => $content,
                'categories' => [$event->getInterviewsWordpressCategoryId()],
                'tags' => [$this->interviewSpeakerTagId],
                'slug' => vsprintf('%s-interview-%s', [
                    $event->getPath(),
                    $slugger->slug(implode(' ', $speakerNames)),
                ]),

                // La publication est déléguée à WordPress. Si une date dans le passé est renseignée,
                // l'interview sera publiée directement. Sinon, WordPress la publiera à la date indiquée.
                'date' => $interview->datePublication->format('Y-m-d\TH:i:s'),
                'status' => 'publish',
            ],
        ]);

        $id = $response->toArray()['id'] ?? null;

        return is_int($id) ? $id : null;
    }

    private function getCategory(int $id): ?Category
    {
        $response = $this->wordpressClient->request('GET', '/wp-json/wp/v2/categories/' . $id);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return $this->mapperBuilder
            ->allowSuperfluousKeys()
            ->mapper()
            ->map(Category::class, Source::json($response->getContent()));
    }
}
