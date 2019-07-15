<?php

namespace GeekCms\Pages\Http\Controllers;

use App;
use Exception;
use GeekCms\Pages\Models\Assigns;
use GeekCms\Pages\Models\Block;
use GeekCms\Pages\Models\Page;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Theme;
use function count;

class AdminController extends Controller
{
    /**
     * List all pages.
     *
     * @return Factory|View
     */
    public function index()
    {
        $pages = Page::where('type', '!=', 'trans')
            ->with(['blocks'])
            ->get();

        try {
            $locales = getSupportedLocales();
        } catch (Exception $e) {
            $locales = [];
        }

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
     * @param Page $page
     *
     * @return Factory|View
     */
    public function form(Request $request, Page $page)
    {
        $page_has = object_get($page, 'id', null);
        $view = (!empty($page_has)) ? 'pages::page.edit' : 'pages::page.create';
        $tabs = (!empty($page_has)) ? Page::getPageTabs($page) : [];
        $categories = Page::getPagesList();
        $blocks = Block::formatBlocks();
        $assigned = Block::formatBlocksAssigned($page, true);
        try {
            $locales = getSupportedLocales();
        } catch (Exception $e) {
            $locales = [];
        }

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
     * @param Page $page
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws Exception
     *
     */
    public function save(Request $request, Page $page)
    {
        $page = (!empty($page)) ? $page : new Page();
        $data = $request->all();
        $data['lang'] = (!empty($data['lang'])) ? $data['lang'] : App::getLocale();
        $data['theme'] = (!empty($data['theme'])) ? $data['theme'] : Theme::current()->name;
        $data['slug'] = (!empty($data['slug'])) ? $data['slug'] : $data['lang'];
        $is_main = (bool)$data['main_page'];
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
     * @return RedirectResponse
     * @throws Exception
     *
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
     * @return RedirectResponse
     * @throws Exception
     *
     */
    public function deleteAll(Request $request)
    {
        $get_pages = $request->get('items', '');
        $get_pages = explode(',', $get_pages);

        if (count($get_pages)) {
            $find_page = Page::whereIn('id', $get_pages)->whereIn('parent_id', $get_pages);
            if ($find_page->count()) {
                $find_page->delete();
            }
        }

        return redirect()->route('admin.pages');
    }
}
