<?php

namespace Modules\Pages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pages\Models\Assigns;
use Modules\Pages\Models\Block;
use Modules\Pages\Models\Page;

class AdminController extends Controller
{
    /**
     * List all pages.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $pages = Page::where('type', '!=', 'trans')
            ->with(['blocks'])
            ->get()
        ;

        $locales = config('laravellocalization.supportedLocales', []);

        return view('pages::page.index', [
            'pages' => $pages,
            'locales' => $locales,
            'default_slug' => Page::getDefaultSlug(),
        ]);
    }

    /**
     * Show form for edit page.
     *
     * @param Request $request
     * @param Page    $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form(Request $request, Page $page)
    {
        $page_has = object_get($page, 'id', null);
        $view = (!empty($page_has)) ? 'pages::page.edit' : 'pages::page.create';
        $locales = config('laravellocalization.supportedLocales', []);
        $tabs = (!empty($page_has)) ? Page::getPageTabs($page) : [];
        $categories = Page::getPagesList();
        $blocks = Block::formatBlocks();
        $assigned = Block::formatBlocksAssigned($page, true);

        return view($view, [
            'page' => $page ?? null,
            'tabs' => $tabs,
            'categories' => $categories,
            'locales' => $locales,
            'blocks' => $blocks,
            'assigned' => $assigned,
            'default_slug' => Page::getDefaultSlug(),
        ]);
    }

    /**
     * Save or create new page.
     *
     * @param Page    $page
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Page $page)
    {
        $page = (!empty($page)) ? $page : new Page();
        $data = $request->all();
        $data['lang'] = (!empty($data['lang'])) ? $data['lang'] : \App::getLocale();
        $data['theme'] = (!empty($data['theme'])) ? $data['theme'] : \Theme::current()->name;
        $data['slug'] = (!empty($data['slug'])) ? $data['slug'] : $data['lang'];
        $is_main = (bool) $data['main_page'];
        $assign_blocks_model = new Assigns($data);

        if ($page->fill($data) && !$page->validate($data)->fails()) {
            if ($is_main) {
                Page::setDefaultSlug($data['slug']);
            }

            $page->save();

            if (!$assign_blocks_model->fill($data) || !$assign_blocks_model->saveAndValidate($page)) {
                return redirect()->back()->withInput($data)->withErrors($assign_blocks_model->errors);
            }
        } else {
            return redirect()->back()->withInput($data)->withErrors($page->errors);
        }

        return redirect()->route('admin.pages');
    }

    /**
     * Delete page.
     *
     * @param Page $page
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Page $page)
    {
        $page->children()->delete();
        $page->delete();

        return redirect()->route('admin.pages');
    }

    /**
     * Delete selected data.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll(Request $request)
    {
        $get_pages = $request->get('items', '');
        $get_pages = explode(',', $get_pages);

        if (\count($get_pages)) {
            $find_page = Page::whereIn('id', $get_pages)->whereIn('parent_id', $get_pages);
            if ($find_page->count()) {
                $find_page->delete();
            }
        }

        return redirect()->route('admin.pages');
    }
}
