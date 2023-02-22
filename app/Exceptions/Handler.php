<?php

namespace App\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use App\Service\TelegramBotHandler;

class Handler extends ExceptionHandler
{
    public function __construct(TelegramBotHandler $telegramBotHandler,
                                Request $request)
    {
        $this->telegrambot = $telegramBotHandler;
        $this->request = $request;
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        $param = json_encode($this->request->all());
        $env = env('APP_ENV');
        $uri = $this->request->getRequestUri();
        $message = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),

        ];
        $this->telegrambot->sendErrorLog($env, $uri, json_encode($message), $param);
        parent::report($exception);
    }
}
