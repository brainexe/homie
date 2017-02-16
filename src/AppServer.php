<?php

namespace Homie;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @todo migrate to Commands in Core
 * @Service("AppServer", public=true)
 */
class AppServer
{

    use LoggerTrait;
    use TimeTrait;

    const MAX_AGE = 10;

    const REQUEST  = 'request';
    const RESPONSE = 'response:%s';

    /**
     * @var AppKernel
     */
    private $appKernel;

    /**
     * @var Predis
     */
    private $redis;

    /**
     * @var int in seconds
     */
    private $timeout;

    /**
     * @var Session
     */
    private $session;

    /**
     * @Inject({
     *     "timeout" = 600,
     *     "redis" = "@Redis",
     *     "session" = "@RedisSession"
     * })
     * @param AppKernel $appKernel
     * @param Predis $redis
     * @param Session $session
     * @param int $timeout
     */
    public function __construct(AppKernel $appKernel, Predis $redis, Session $session, int $timeout)
    {
        $this->appKernel = $appKernel;
        $this->redis     = $redis;
        $this->timeout   = $timeout;
        $this->session   = $session;
    }

    public function start()
    {
        ini_set('session.use_cookies', false);
        while ($raw = $this->redis->brpop(self::REQUEST, $this->timeout)) {
            $raw = json_decode($raw[1], true);

            $this->handle($raw);
        }
    }

    /**
     * @param array $raw
     */
    private function handle(array $raw)
    {
        if ($this->now() > $raw['server']['REQUEST_TIME_FLOAT'] + self::MAX_AGE) {
            // ignore old requests
            return;
        }

        $request = $this->initRequest($raw);

        $response = $this->appKernel->handle($request);

        $responseData = [
            'requestId' => $raw['requestId'],
            'sessionId' => $this->session->getId(),
            'status'    => $response->getStatusCode(),
            'body'      => $response->getContent(),
            'headers'   => $response->headers->all(),
        ];

        $this->redis->publish(
            sprintf(self::RESPONSE, $raw['requestId']),
            json_encode($responseData)
        );

        $this->session->save();
    }

    /**
     * @param array $raw
     * @return Request
     */
    private function initRequest(array $raw) : Request
    {
        $request = new Request(
            $raw['get'],
            $raw['post'],
            $raw['request'],
            $raw['cookies'],
            $raw['files'],
            $raw['server'],
            $raw['raw']
        );

        if (!empty($raw['sessionId'])) {
            $this->session->setId($raw['sessionId']);
        }

        $this->session->start();

        return $request;
    }
}
