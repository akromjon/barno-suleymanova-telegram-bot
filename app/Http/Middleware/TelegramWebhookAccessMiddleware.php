<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TelegramWebhookAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|false
    {
        if ($request->header(key: 'x-telegram-bot-api-secret-token') !== config(key: 'telegram.bots.mybot.secret_token')) {

            Log::error(message: "Someone is requesting with the wrong x-telegram-bot-api-secret-token or no token");

            abort(code: 200, message: 'Someone is requesting with the wrong x-telegram-bot-api-secret-token');

            return false;
            // we send 200 because, telegram marks unsuccessful requests and keeps in the pending, this overcomes load
        }

        return $next($request);

    }
}
