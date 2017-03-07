<?php
namespace AppBundle\Routing;

class LegacyRouter{
    public function getAdminUrl($page, $options = [])
    {
        $options['page'] = $page;
        return '/pages/administration/index.php?' . http_build_query($options);
    }
}
