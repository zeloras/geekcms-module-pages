<?php

namespace Modules\Pages\Listeners;

use App\Listeners\MainListener;
use GeekCms\PackagesManager\Events\ModulesEvent;

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
        \Route::group(['prefix' => \LaravelLocalization::setLocale()], function () {
            \Route::get('/{page?}', 'Modules\Pages\Http\Controllers\PageController@open')
                ->where(['page' => '[a-zA-Z\\/0-9]+'])
                ->name('page.open')
            ;
        });
    }
}
