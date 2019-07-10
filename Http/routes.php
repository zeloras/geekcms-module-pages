<?php

Route::group(['middleware' => ['web', 'permission:' . Gcms::MAIN_ADMIN_PERMISSION], 'prefix' => getAdminPrefix('pages')], function () {
    Route::group(['middleware' => ['permission:modules_pages_admin_pages_list']], function () {
        Route::get(DIRECTORY_SEPARATOR, 'GeekCms\Pages\Http\Controllers\AdminController@index')
            ->name('admin.pages');
    });

    Route::group(['middleware' => ['permission:modules_pages_admin_pages_create']], function () {
        Route::get('/create', 'GeekCms\Pages\Http\Controllers\AdminController@form')
            ->name('admin.pages.create');
    });

    Route::group(['middleware' => ['permission:modules_pages_admin_pages_edit']], function () {
        Route::get('/edit/{page}', 'GeekCms\Pages\Http\Controllers\AdminController@form')
            ->name('admin.pages.edit');
    });

    Route::post('/save/{page?}', 'GeekCms\Pages\Http\Controllers\AdminController@save')
        ->name('admin.pages.save');

    Route::group(['middleware' => ['permission:modules_pages_admin_pages_delete']], function () {
        Route::post('/delete/all', 'GeekCms\Pages\Http\Controllers\AdminController@deleteAll')
            ->name('admin.pages.delete.all');

        Route::get('/delete/{page}', 'GeekCms\Pages\Http\Controllers\AdminController@delete')
            ->name('admin.pages.delete');
    });

    // blocks
    Route::group(['prefix' => 'blocks'], function () {
        Route::group(['middleware' => ['permission:modules_pages_admin_blocks_list']], function () {
            Route::get(DIRECTORY_SEPARATOR, 'GeekCms\Pages\Http\Controllers\AdminBlockController@index')
                ->name('admin.pages.blocks');
        });

        Route::group(['middleware' => ['permission:modules_pages_admin_blocks_create']], function () {
            Route::get('/create', 'GeekCms\Pages\Http\Controllers\AdminBlockController@create')
                ->name('admin.pages.blocks.create');
        });

        Route::group(['middleware' => ['permission:modules_pages_admin_blocks_edit']], function () {
            Route::get('/edit/{block}', 'GeekCms\Pages\Http\Controllers\AdminBlockController@edit')
                ->name('admin.pages.blocks.edit');
        });

        Route::post('/save/{block?}', 'GeekCms\Pages\Http\Controllers\AdminBlockController@save')
            ->name('admin.pages.blocks.save');

        Route::group(['middleware' => ['permission:modules_pages_admin_blocks_delete']], function () {
            Route::post('/delete/all', 'GeekCms\Pages\Http\Controllers\AdminBlockController@deleteAll')
                ->name('admin.pages.blocks.delete.all');

            Route::get('/delete/{block}', 'GeekCms\Pages\Http\Controllers\AdminBlockController@destroy')
                ->name('admin.pages.blocks.delete');

            Route::get('/delete/var/{var}/', 'GeekCms\Pages\Http\Controllers\AdminBlockController@varDestroy')
                ->name('admin.pages.blocks.var_delete');
        });
    });
});
