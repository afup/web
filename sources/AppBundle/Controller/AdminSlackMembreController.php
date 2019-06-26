<?php

namespace AppBundle\Controller;

use AppBundle\Slack\UsersChecker;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminSlackMembreController extends Controller
{
    public function checkMembersAction()
    {
        $result = $this->get(UsersChecker::class)->checkUsersValidity();
        $csv = '';
        if (count($result) > 0) {
            $filename = tempnam(sys_get_temp_dir(), 'AFUP');
            $file = new SplFileObject($filename, 'w');

            $file->fputcsv([
                'Nom d\'utilisateur Slack',
                'Nom affiché dans Slack',
                'Adresse courriel dans Slack',
                'Utilisateur présent dans la base AFUP',
                'Dernière souscription AFUP',
                'Status de l\'utilisateur AFUP'
            ]);
            foreach ($result as $user) {
                $user['user_found'] = $user['user_found'] ? 'oui' : 'non';
                if ($user['afup_last_subscription'] instanceof \DateTimeInterface) {
                    $user['afup_last_subscription'] = $user['afup_last_subscription']->format('d/m/Y H:i:s');
                }
                $file->fputcsv($user);
            }
            $file = null;
            $csv = file_get_contents($filename);
            @unlink($filename);
        }

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Utilisateurs à désactiver du ' . date('Y-m-d H_i_s') . '.csv"',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }
}
