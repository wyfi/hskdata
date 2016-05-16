<?php

namespace API;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;

final class Error extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $code = $exception->getCode();
        $line = $exception->getLine();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $trace = $exception->getTraceAsString();
        $this->logger->critical("$file ($line): $message ($code)\n\n $trace");

        return parent::__invoke($request, $response, $exception);
    }
}

?>