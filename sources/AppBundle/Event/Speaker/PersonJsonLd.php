<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

/**
 * Données structurées schema.org Person de la page d'un speaker.
 *
 * @see https://schema.org/Person
 */
final readonly class PersonJsonLd
{
    /**
     * Options d'encodage à utiliser pour insérer le JSON dans un <script> :
     * JSON_HEX_TAG encode < et >, si bien qu'un texte contenant « </script> » ne peut pas fermer la balise.
     */
    public const int JSON_FLAGS = JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

    /**
     * @param string $url l'URL absolue de la page du speaker
     * @param string|null $imageUrl l'URL absolue de sa photo
     *
     * @return array<string, string|list<string>>
     */
    public function getData(SpeakerProfile $profile, string $url, ?string $imageUrl): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile->identity->getName(),
            'url' => $url,
        ];

        if (null !== $imageUrl) {
            $data['image'] = $imageUrl;
        }

        if ($profile->socialLinks !== []) {
            $data['sameAs'] = array_values($profile->socialLinks);
        }

        return $data;
    }
}
