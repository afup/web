<?php

declare(strict_types=1);


namespace AppBundle\TechLetter;

class HtmlParser
{
    private readonly \DOMDocument $dom;

    /**
     * @var \DOMNodeList&iterable<\DOMElement>
     */
    private readonly \DOMNodeList $meta;

    private readonly \DOMXPath $xpath;

    const OPEN_GRAPH_PREFIX = 'og';
    const TWITTER_PREFIX = 'twitter';

    public function __construct($html)
    {
        $this->dom = new \DOMDocument();
        $this->dom->recover = true;
        $this->dom->strictErrorChecking = false;
        libxml_use_internal_errors(true);
        if ($this->dom->loadHTML($html) === false) {
            throw new \InvalidArgumentException('Malformed html');
        }

        $this->meta = $this->dom->getElementsByTagName('meta');
        $this->xpath = new \DOMXPath($this->dom);
    }

    /**
     * This methods parse the html to find a meta property,
     * from opengraph (first) or twitter (if no meta found from opengraph) and return its value.
     * If no meta has been found, null is returned.
     *
     * @param $meta string
     */
    private function getSocialMeta(string $meta): ?string
    {
        $value = $this->getOpenGraphMeta($meta);
        if ($value === null) {
            $value = $this->getTwitterMeta($meta);
        }
        return $value;
    }

    private function getOpenGraphMeta(string $property): ?string
    {
        foreach ($this->meta as $meta) {
            if ($meta->hasAttribute('property') && $meta->getAttribute('property') === self::OPEN_GRAPH_PREFIX . ':' . $property) {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function getTwitterMeta(string $name): ?string
    {
        foreach ($this->meta as $meta) {
            if ($meta->hasAttribute('name') && $meta->getAttribute('name') === self::TWITTER_PREFIX . ':' . $name) {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    /**
     * @param $name
     */
    private function getStandardMeta($name): ?string
    {
        foreach ($this->meta as $meta) {
            if ($meta->hasAttribute('name') && $meta->getAttribute('name') === $name) {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    public function getMeta($name): ?string
    {
        $value = $this->getSocialMeta($name);
        if ($value === null) {
            $value = $this->getStandardMeta($name);
        }
        return $value;
    }

    public function getTitle()
    {
        $item = $this->dom->getElementsByTagName('title')->item(0);
        if ($item === null) {
            return $item;
        }
        return $item->textContent;
    }

    /**
     * @return mixed[]
     */
    public function getRichSchema(): array
    {
        $jsonScripts = $this->xpath->query('//script[@type="application/ld+json"]');

        $data = [];
        foreach ($jsonScripts as $script) {
            $data[] = json_decode((string) $script->nodeValue, true);
        }
        return $data;
    }
}
