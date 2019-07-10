<?php

namespace GeekCms\Pages\Listeners;

use App\Listeners\MainListener;
use GeekCms\PackagesManager\Events\ModulesEvent;
use LaravelLocalization;
use Route;

/**
 * Class ModulesListener.
 */
class ModulesListener extends MainListener
{
    /**
     * @param ModulesEvent $event
     */
    public function handle(ModulesEvent $event)
    {
        // localize
        Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
            Route::get('/{page?}', 'GeekCms\Pages\Http\Controllers\PageController@open')
                ->where(['page' => '[a-zA-Z\\/0-9]+'])
                ->name('page.open');
        });
    }
}
