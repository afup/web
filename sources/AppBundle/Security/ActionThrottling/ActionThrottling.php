<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling;

use AppBundle\Security\ActionThrottling\Entity\Log;
use AppBundle\Security\ActionThrottling\Entity\Repository\LogRepository;

final readonly class ActionThrottling
{
    public function __construct(private LogRepository $logRepository) {}

    public function isActionBlocked(string $action, ?string $ip = null, ?int $objectId = null): bool
    {
        $limitations = Log::LIMITATIONS;
        if (isset($limitations[$action]) === false) {
            throw new \RuntimeException(sprintf('Could not retrieve limitations for action "%s"', $action));
        }

        $delay = $limitations[$action]['delay'];

        $logs = $this->logRepository->getApplicableLogs($ip, $objectId, new \DateInterval($delay));

        return $logs['ip'] > $limitations[$action]['limit'] || $logs['object'] > $limitations[$action]['limit'];
    }

    public function clearLogsForIp(string $action, string $ip): void
    {
        $this->logRepository->removeLogs($action, $ip);
    }

    public function log(string $action, ?string $ip = null, ?int $objectId = null): void
    {
        $log = new Log();
        $log->dateCreation = new \DateTime();
        $log->action = $action;
        $log->ip = $ip !== null ? ip2long($ip) : null;
        $log->idObjet = $objectId;
        $this->logRepository->save($log);
    }

    public function clearOldLogs(): void
    {
        $this->logRepository->clearOldLogs(new \DateInterval('P30D'));
    }
}
