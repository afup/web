<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling;

class ActionThrottling
{
    public function __construct(private readonly LogRepository $logRepository)
    {
    }

    /**
     * @param string|null $action
     * @param string|null $ip
     * @param int $objectId
     */
    public function isActionBlocked($action, $ip = null, $objectId = null): bool
    {
        $limitations = Log::LIMITATIONS;
        if (isset($limitations[$action]) === false) {
            throw new \RuntimeException(sprintf('Could not retrieve limitations for action "%s"', $action));
        }

        $delay = $limitations[$action]['delay'];

        try {
            $interval = new \DateInterval($delay);
        } catch (\Exception $dateIntervalException) {
            throw new \RuntimeException(
                sprintf('Sorry, I could not understand the delay "%s" for the action "%s"', $delay, $action),
                0,
                $dateIntervalException
            );
        }

        $logs = $this->logRepository->getApplicableLogs($action, $ip, $objectId, $interval);
        return $logs['ip'] > $limitations[$action]['limit'] || $logs['object'] > $limitations[$action]['limit'];
    }

    public function clearLogsForIp($action, $ip): void
    {
        $this->logRepository->removeLogs($action, $ip);
    }

    /**
     * @param string $action
     * @param string|null $ip
     * @param string|null $objectId
     */
    public function log($action, $ip = null, $objectId = null): void
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
