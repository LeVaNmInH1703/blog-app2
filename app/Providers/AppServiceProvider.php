<?php

namespace App\Providers;

use App\Models\Emoji;
use App\Models\Notification;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Blog\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Comment\CommentRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BlogRepositoryInterface::class, function () {
            return new BlogRepository();
        });

        $this->app->singleton(CommentRepositoryInterface::class, function () {
            return new CommentRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // DB::table('sessions')->created(function ($session) {
        //     // Xử lý logic khi session mới được tạo
        //     dump($session);
        //     return $session;
        // });
        //cho phép sử dụng bootstrap 5 cho paginator
        Paginator::useBootstrapFive();
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(600),
                Limit::perMinute(6)->by($request->ip()),
                Limit::perMinute(6)->by($request->input('email')),
            ];
        });
        RateLimiter::for('countGroupHasNewMessage', function (Request $request) {
            return [
                Limit::perMinute(1000),
                Limit::perMinute(10)->by($request->ip()),
                Limit::perMinute(10)->by($request->input('email')),
            ];
        });
        // đăng ký blade
        Blade::directive('diffForHumans', function ($time) {
            return "<?php echo strtr((new \Carbon\Carbon($time))->diffForHumans(now()), ['before' => 'ago']) ?>";
        });

        //view composer
        Facades\View::composer(['pages.home', 'pages.profile', 'pages.blogDetail'], function (View $view) {
            // Gửi dữ liệu tới view 'app.blade.php'
            $view->with('emojis', Emoji::all());
        });
        Facades\View::composer('layout.header', function ($view) {
            $view->with('notifications', Auth::user()->notifications->sortByDesc('created_at')->take(10));
            $view->with('countNewNotifications', Notification::where([['user_id_receive', Auth::id()], ['isSaw', false]])->count());
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
        // return ['redis', 'redis.connection'];
    }
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // ServerProvider::class => DigitalOceanServerProvider::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        // DowntimeNotifier::class => PingdomDowntimeNotifier::class,
    ];
}
