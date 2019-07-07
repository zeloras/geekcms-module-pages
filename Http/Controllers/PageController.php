<?php

namespace Modules\Pages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pages\Models\Page;

class PageController extends Controller
{
    public function open(Request $request, $page = null)
    {
        $page = (empty($page) && \DIRECTORY_SEPARATOR === $request->getRequestUri()) ? Page::getDefaultSlug() : $page;
        // Get page language
        $page_language = $request->route()->getPrefix();
        $page_language = (empty($page_language)) ? config('app.locale') : $page_language;
        // Get user language
        $user_lang = \App::getLocale();

        $page = Page::where('slug', $page)
            ->where('type', 'page')
            ->with(['children', 'assigns'])
            ->firstOrFail()
        ;

        $localePage = $page->children()
            ->where('type', 'trans')
            ->get()
        ;

        // For redirect to user locale session
        if ($page_language !== $user_lang) {
            return redirect(getLocalizedRouteURL($user_lang, 'page.open', ['page' => $page->slug]));
        }

        if ($localePage && \count($localePage)) {
            $page_lang = null;
            foreach ($localePage as $pages) {
                if ($user_lang === $pages->lang) {
                    $page_lang = $pages;

                    break;
                }
            }

            if (!empty($page_lang)) {
                foreach (Page::$translateAttributes as $attribute) {
                    $page->{$attribute} = $page_lang->{$attribute};
                }
            }
        }

        return view('page', [
            'page' => $page,
        ]);
    }
}
