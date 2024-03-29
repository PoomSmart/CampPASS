<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException || $exception instanceof \CampPASSExceptionPermission)
            return redirect('/')->with('error', trans('app.NoPermissionError'));
        if ($exception instanceof \CampPASSException)
            return redirect('/')->with('error', $exception->getMessage());
        if ($exception instanceof \CampPASSExceptionRedirectBack)
            return redirect()->back()->with('error', $exception->getMessage());
        if ($exception instanceof \CampPASSExceptionNoFileUploaded)
            return redirect()->back()->with('error', trans('app.NoFileUploaded'));
        return parent::render($request, $exception);
    }
}
