<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use QuickerFaster\UILibrary\Exceptions\RecordNotAccessibleException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        // Report custom exceptions with extra context
        $this->reportable(function (RecordNotAccessibleException $e) {
            \Log::warning('RecordNotAccessibleException: ' . $e->getUserMessage(), $e->getContext());
        });

        $this->reportable(function (ModelNotFoundException $e) {
            \Log::warning('ModelNotFoundException caught by safety net', [
                'message' => $e->getMessage(),
                'url'     => request()->fullUrl(),
                'user_id' => auth()->id() ?? 'guest',
            ]);
        });

        // Default reportable for all other exceptions
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Override to provide user-friendly handling for:
     *  - RecordNotAccessibleException (custom domain exception)
     *  - ModelNotFoundException (safety net for any missed findOrFail calls)
     *
     * @param  Request  $request
     * @param  Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // ── Handle our custom RecordNotAccessibleException ──────────────────
        if ($e instanceof RecordNotAccessibleException) {
            return $this->renderRecordNotAccessible($request, $e);
        }

        // ── Safety net: catch any remaining ModelNotFoundException ──────────
        //    (from findOrFail calls we may have missed, or from third-party packages)
        if ($e instanceof ModelNotFoundException) {
            return $this->renderModelNotFound($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Render a RecordNotAccessibleException.
     */
    protected function renderRecordNotAccessible(Request $request, RecordNotAccessibleException $e): Response
    {
        $status  = $e->getHttpStatusCode();
        $message = $e->getUserMessage();
        $route   = $e->getRedirectRoute();

        // ── JSON / API / AJAX requests ─────────────────────────────────────
        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status'  => $status,
            ], $status);
        }

        // ── Livewire requests: dispatch showAlert event ───────────────────
        //    Livewire 3 uses a X-Livewire header on its AJAX requests.
        //    We return a 200 with an event dispatch so the component can
        //    handle the error inline rather than showing a full error page.
        if ($request->hasHeader('X-Livewire')) {
            return response()->json([
                'effects' => [
                    'dispatches' => [
                        [
                            'name'  => 'showAlert',
                            'params' => [
                                'type'    => 'error',
                                'message' => $message,
                            ],
                        ],
                    ],
                ],
                'redirect' => $route ? route($route) : null,
            ], 200);
        }

        // ── Standard web request ──────────────────────────────────────────
        if ($route) {
            return redirect()
                ->route($route)
                ->with('error', $message);
        }

        // Fallback: return a clean error view if no redirect is configured
        return response()->view('errors.custom', [
            'title'   => $status === 403 ? 'Access Denied' : 'Record Not Found',
            'message' => $message,
        ], $status);
    }

    /**
     * Render a ModelNotFoundException (safety net).
     */
    protected function renderModelNotFound(Request $request, ModelNotFoundException $e): Response
    {
        $message = 'The requested record could not be found. It may have been deleted or the link is invalid.';

        // ── JSON / API / AJAX requests ─────────────────────────────────────
        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status'  => 404,
            ], 404);
        }

        // ── Livewire requests ─────────────────────────────────────────────
        if ($request->hasHeader('X-Livewire')) {
            return response()->json([
                'effects' => [
                    'dispatches' => [
                        [
                            'name'   => 'showAlert',
                            'params' => [
                                'type'    => 'error',
                                'message' => $message,
                            ],
                        ],
                    ],
                ],
            ], 200);
        }

        // ── Standard web request ──────────────────────────────────────────
        //    Fall back to Laravel's default 404 handling
        return parent::render($request, $e);
    }
}
