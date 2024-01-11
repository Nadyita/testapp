<?php

use Amp\ByteStream;
use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

require __DIR__.'/vendor/autoload.php';

// Note any PSR-3 logger may be used, Monolog is only an example.
$logHandler = new StreamHandler(ByteStream\getStdout());
$logHandler->pushProcessor(new PsrLogMessageProcessor());
$logHandler->setFormatter(new ConsoleFormatter());

$logger = new Logger('server');
$logger->pushHandler($logHandler);

$requestHandler = new class() implements RequestHandler {
    public function handleRequest(Request $request) : Response
    {
        return new Response(
            status: HttpStatus::OK,
            headers: ['Content-Type' => 'text/plain'],
            body: 'Hello, world 6!',
        );
    }
};

$errorHandler = new DefaultErrorHandler();

$server = SocketHttpServer::createForDirectAccess($logger);
$server->expose('0.0.0.0:1337');
$server->start($requestHandler, $errorHandler);

// Serve requests until SIGINT or SIGTERM is received by the process.
Amp\trapSignal([SIGINT, SIGTERM]);

$server->stop();
