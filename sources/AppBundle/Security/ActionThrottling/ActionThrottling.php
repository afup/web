<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling;

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

    public function clearLogsForIp($action, $ip): void
    {
        $this->logRepository->removeLogs($action, $ip);
    }

    public function log(string $action, ?string $ip = null, ?int $objectId = null): void
    {
        $log = new Log();
        $log
            ->setCreatedOn(new \DateTime())
            ->setAction($action)
            ->setIp($ip)
            ->setObjectId($objectId)
        ;
        $this->logRepository->save($log);
    }

    public function clearOldLogs(): void
    {
        $this->logRepository->clearOldLogs(new \DateInterval('P30D'));
    }
}
