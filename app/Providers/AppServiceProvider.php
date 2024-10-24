<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Enums\TelegramUserType;
use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production', 'local')) {
            URL::forceScheme('https');
        }
        Model::unguard();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100);
        });


        $this->loadViews();

    }
    private function loadViews(): void
    {
        view()->composer(views: 'partials.chat.private', callback: function ($view) {

            $chats = Chat::with(relations: 'telegramUser')->where('type',TelegramUserType::PRIVATE)->orderBy(column: 'last_messaged_at',direction: 'desc')->get();

            $view->with('chats', $chats);
        });
    }
}
