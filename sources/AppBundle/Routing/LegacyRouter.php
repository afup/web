<?php

declare(strict_types=1);

namespace AppBundle\Routing;

class LegacyRouter
{
    public function getAdminUrl($page, array $options = []): string
    {
        $options['page'] = $page;
        return '/pages/administration/index.php?' . http_build_query($options);
    }
}
