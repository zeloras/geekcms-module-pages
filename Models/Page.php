<?php

namespace GeekCms\Pages\Models;

use Illuminate\Support\Facades\Blade;
use GeekCms\Pages\Models\db\Page as DBModel;
use Ponich\Eloquent\Traits\VirtualAttribute;

class Page extends DBModel
{
    use VirtualAttribute;

    public static $translateAttributes = [
        'name',
        'content',
    ];

    public $virtalAttributes = [
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_tags',
    ];

    public static $block_list_main = [
        'id' => 0,
        'name' => 'Current content',
        'content' => '',
        'position' => 0,
        'old_position' => 0,
        'enabled' => true,
        'page_id' => 0,
        'block_id' => 0,
        'main' => true,
    ];

    /**
     * Get available translates.
     *
     * @param null|array $pluck
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function getAvailableTranslates($pluck = null)
    {
        $root = ($this->parent_id) ? false : true;
        $start_lang = (!empty($this->lang)) ? $this->lang : \App::getLocale();

        $model = ($root) ? $this : $this->parent;
        $items = $model->children->where('type', '=', 'trans');
        // @var \Illuminate\Support\Collection $items
        $items->push($model);
        foreach ($items as $item_key => $item_val) {
            if (empty($item_val->lang)) {
                $items[$item_key]->lang = $start_lang;
            }
        }

        $return = $items;

        if (!empty($pluck)) {
            $return = $items->pluck($pluck)->toArray();
        }

        return $return;
    }

    /**
     * Set langue attribute with slug.
     *
     * @param null $lang
     */
    public function setLangAttribute($lang = null)
    {
        $lang = $lang ?? config('app.locale');

        if ('trans' === $this->type && empty($this->attributes['slug'])) {
            $this->attributes['slug'] = $lang;
        }

        $this->attributes['lang'] = $lang;
    }

    /**
     * For compile attribute content use blade.
     *
     * @return string
     */
    public function getContentCompileAttribute()
    {
        $blocks = [];
        $assigns_list = $this->assigns()->with(['block'])->get();
        if (!empty($assigns_list) && \count($assigns_list)) {
            foreach ($assigns_list as $assign) {
                $blocks[$assign->position] = ($assign->enabled) ? $assign->block->content_compile : '';
            }
        }

        $content = Blade::compileString(array_get($this->attributes, 'content'));
        $blocks = Block::formatBlocksPosition($blocks, true, $content);

        return implode("\n", $blocks);
    }

    /**
     * Blocks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blocks()
    {
        return $this->belongsToMany(
            Block::class,
            Assigns::tablename(),
            'page_id',
            'block_id'
        );
    }

    /**
     * Route key.
     *
     * @return mixed|string
     */
    public function getRouteKey()
    {
        return 'slug';
    }

    /**
     * Задать slug для страници.
     *
     * @param $slug
     */
    public function setSlugAttribute($slug)
    {
        // $this->attributes['slug'] = str_slug($slug);
        $this->attributes['slug'] = $slug;
    }

    /**
     * Не может наследовать сам себя
     * Не может быть NULL.
     *
     * @param $id
     */
    public function setParentIdAttribute($id = 0)
    {
        $parentId = ($id === $this->id) ? 0 : $id;
        $parentId = (!$parentId) ? 0 : $parentId;

        $this->attributes['parent_id'] = $parentId;
    }

    /**
     * Get default slug for main page.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getDefaultSlug()
    {
        $default_slug = config(self::MAIN_PAGE_KEY, 'home');
        if (class_exists('ConfigManager')) {
            $default_slug = \ConfigManager::get(self::MAIN_PAGE_KEY, $default_slug);
        }

        return $default_slug;
    }

    /**
     * Set new main public page.
     *
     * @param null $slug
     *
     * @throws \Exception
     */
    public static function setDefaultSlug($slug = null)
    {
        if (class_exists('ConfigManager')) {
            \ConfigManager::set(self::MAIN_PAGE_KEY, $slug);
        } else {
            throw new \Exception('Config manager dosent load');
        }
    }

    /**
     * Get pages list.
     *
     * @return array
     */
    public static function getPagesList()
    {
        $categories = self::where('type', 'page')->get();

        return ($categories && !empty($categories)) ? $categories : [];
    }

    /**
     * Generate tabs.
     *
     * @param null|Page $page
     *
     * @return array
     */
    public static function getPageTabs(self $page = null)
    {
        $tabs = [];

        if (!empty($page)) {
            $pages = $page->getAvailableTranslates();
            $locale = config('app.locale');

            foreach ($pages->sortBy('id') as $tabPage) {
                $tab_locale = (!empty($tabPage->lang)) ? $tabPage->lang : $locale;
                $prefix = ('page' === $tabPage->type) ? $tab_locale : $tabPage->slug;

                $tabs[] = [
                    'parent' => $tabPage->parent_id,
                    'title' => "{$tabPage->name} ({$prefix})",
                    'url' => route('admin.pages.edit', ['page' => $tabPage->id]),
                    'active' => ($tabPage->id === $page->id) ? true : false,
                ];
            }

            $tabs = collect($tabs)->sortBy('parent')->toArray();
        }

        return $tabs;
    }
}
