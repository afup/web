<?php

namespace AppBundle\Security\ActionThrottling;

class ActionThrottling
{
    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param string|null $action
     * @param string|null $ip
     * @param int $objectId
     *
     * @return bool
     */
    public function isActionBlocked($action, $ip = null, $objectId = null)
    {
        $limitations = Log::LIMITATIONS;
        if (isset($limitations[$action]) === false) {
            throw new \RuntimeException(sprintf('Could not retrieve limitations for action "%s"', $action));
        }

        $delay = $limitations[$action]['delay'];
        $interval = new \DateInterval($delay);
        if (isset($interval) === false) { // Si le delay est invalide alors le dateinterval n'est pas créé et une warning générée mais pas d'exception
            throw new \RuntimeException(sprintf('Sorry, I could not understand the delay "%s" for the action "%s"', $delay, $action));
        }
        $logs = $this->logRepository->getApplicableLogs($action, $ip, $objectId, $interval);

        if ($logs['ip'] > $limitations[$action]['limit'] || $logs['object'] > $limitations[$action]['limit']) {
            return true;
        }

        return false;
    }

    public function clearLogsForIp($action, $ip)
    {
        $this->logRepository->removeLogs($action, $ip);
    }

    /**
     * @param string $action
     * @param string|null $ip
     * @param string|null $objectId
     */
    public function log($action, $ip = null, $objectId = null)
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
}
