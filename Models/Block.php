<?php

namespace Modules\Pages\Models;

use App\Models\MainModel;
use Modules\Pages\Models\db\Block as DBModel;

class Block extends DBModel
{
    public static $originLayoutPath = false;

    /**
     * Clear array with all attrs for show blocks, use as template.
     *
     * @var array
     */
    public static $blocks_list_data = [
        'id' => 0,
        'name' => null,
        'content' => null,
        'content_compile' => null,
        'position' => 0,
        'old_position' => 0,
        'enabled' => false,
        'page_id' => 0,
        'block_id' => 0,
    ];

    /**
     * Template compilation mutator.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getContentCompileAttribute()
    {
        return app('blade.compiler')->compileWiths(
            array_get($this->attributes, 'content'),
            $this->getVariables()
        );
    }

    /**
     * Mutator for content.
     *
     * @return null|string|string[]
     */
    public function getContentAttribute()
    {
        $content = (isset($this->attributes['content'])) ? $this->attributes['content'] : '';

        $regex = '/(?<block>\{\{.*theme_url\([\'|"](?<attach>\S+)[\'|"]\).*\}\})/m';

        $content = preg_replace_callback($regex, function ($matches) {
            if ($attach = array_get($matches, 'attach')) {
                return theme_url($attach);
            }
        }, $content);

        return $content;
    }

    public function getVariables()
    {
        $variables = $this->variables;
        $result = [];

        foreach ($variables as $variable) {
            $result[$variable->key] = $variable->value;
        }

        return $result;
    }

    /**
     * Prepare array with active blocks on current page.
     *
     * @param null|MainModel $model
     * @param bool           $current
     *
     * @return array
     */
    public static function formatBlocksAssigned(MainModel $model = null, $current = false)
    {
        $blocks = [];

        if ($model && $model->assigns) {
            $get_sort = $model->assigns()->orderBy('position', 'asc')->get();
            foreach ($get_sort as $assign) {
                if ($assign->block) {
                    $blocks[(int) $assign->position] = [
                        'id' => $assign->block->id,
                        'name' => $assign->block->name.' ('.$assign->block->lang.')',
                        'content' => $assign->block->content,
                        'content_compile' => $assign->block->content_compile,
                        'position' => $assign->position,
                        'old_position' => $assign->old_position,
                        'enabled' => (bool) $assign->enabled,
                        'page_id' => $assign->page_id,
                        'block_id' => $assign->block->id,
                    ];
                }
            }
        }

        return self::formatBlocksPosition($blocks, $current, Page::$block_list_main);
    }

    /**
     * Sort blocks by position for view.
     *
     * @param array  $blocks
     * @param bool   $current
     * @param string $main_content
     *
     * @return array
     */
    public static function formatBlocksPosition($blocks = [], $current = false, $main_content = '')
    {
        $free_key = -1;

        // For find free key, for find current page block position
        if (\count($blocks)) {
            $max_key = max(array_keys($blocks)) + 1;
            for ($min_key = 0; $min_key <= $max_key; ++$min_key) {
                if (!isset($blocks[$min_key]) && $free_key < 0) {
                    $free_key = $min_key;
                }
            }
        }

        // For put current page block array
        if ($current) {
            if ($free_key >= 0) {
                $blocks[(int) $free_key] = $main_content;
            } else {
                $blocks[] = $main_content;
            }
        }

        ksort($blocks);

        return $blocks;
    }

    /**
     * Get all blocks and prepare they.
     *
     * @return array
     */
    public static function formatBlocks()
    {
        $blocks = [];
        $blocks_list = self::all();

        if ($blocks_list && \count($blocks_list)) {
            foreach ($blocks_list as $item) {
                $blocks[] = self::$blocks_list_data;
                end($blocks);
                $key = key($blocks);
                $blocks[$key]['name'] = $item->name.' ('.$item->lang.')';
                $blocks[$key]['content'] = $item->content;
                $blocks[$key]['content_compile'] = $item->content_compile;
                $blocks[$key]['block_id'] = $item->id;
                $blocks[$key]['id'] = $item->id;
            }
        }

        return $blocks;
    }

    /**
     * Get blocks main list.
     *
     * @return array
     */
    public static function getMainBlocksList()
    {
        $categories = self::where('parent_id', 0)->get();

        return ($categories && !empty($categories)) ? $categories : [];
    }

    /**
     * Get available translates.
     *
     * @param null|array $pluck
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function getAvailableTranslates($pluck = null)
    {
        $return = [];
        $root = ($this->parent_id) ? false : true;
        $start_lang = (!empty($this->lang)) ? $this->lang : \App::getLocale();

        $model = ($root) ? $this : $this->parent;
        $items = $model->children;
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
     * Get block tabs.
     *
     * @param null|Block $block
     *
     * @return array
     */
    public static function getBlocksTabs(self $block = null)
    {
        $tabs = [];

        if (!empty($block)) {
            $blocks = $block->getAvailableTranslates();
            $locale = config('app.locale');

            foreach ($blocks->sortBy('id') as $tabBlock) {
                $tab_locale = (!empty($tabBlock->lang)) ? $tabBlock->lang : $locale;

                $tabs[] = [
                    'parent' => $tabBlock->parent_id,
                    'title' => "{$tabBlock->name} ({$tab_locale})",
                    'url' => route('admin.pages.blocks.edit', ['block' => $tabBlock->id]),
                    'active' => ($tabBlock->id === $block->id) ? true : false,
                ];
            }

            $tabs = collect($tabs)->sortBy('parent')->toArray();
        }

        return $tabs;
    }
}
