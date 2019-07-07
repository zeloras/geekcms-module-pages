<?php

namespace Modules\Pages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pages\Models\Block;
use Modules\Pages\Models\Variable;

class AdminBlockController extends Controller
{
    /**
     * Show all blocks.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $blocks = Block::getMainBlocksList();
        $locales = config('laravellocalization.supportedLocales', []);

        return view('pages::block.index', [
            'blocks' => $blocks,
            'locales' => $locales,
        ]);
    }

    /**
     * Show form for create new block with variables.
     *
     * @param Request $request
     * @param Block   $block
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Block $block)
    {
        $blocks_list = Block::getMainBlocksList();
        $locales = config('laravellocalization.supportedLocales', []);

        return view('pages::block.create', [
            'block' => $block ?? null,
            'locales' => $locales,
            'blocks_list' => $blocks_list,
        ]);
    }

    /**
     * Show form for edit blocks.
     *
     * @param Block   $block
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, Block $block)
    {
        $block_has = object_get($block, 'id', null);
        $blocks_list = Block::getMainBlocksList();
        $locales = config('laravellocalization.supportedLocales', []);
        $tabs = (!empty($block_has)) ? Block::getBlocksTabs($block) : [];

        return view('pages::block.edit', [
            'tabs' => $tabs,
            'block' => $block,
            'locales' => $locales,
            'blocks_list' => $blocks_list,
        ]);
    }

    /**
     * Update or create block with variable.
     *
     * @param null|Block $block
     * @param Request    $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Block $block)
    {
        $variables = $this->splitFormArray($request->post('variable', []));

        $block = ($block && !empty($block)) ? $block : new Block();
        $current_id = (int) $request->post('current_id', 0);
        if ($current_id) {
            $find_parent = Block::where('id', $current_id)->first();
            $parent_name = ($find_parent && $find_parent->id) ? $find_parent->name : $request->post('name', '');
        } else {
            $parent_name = $request->post('name', '');
        }

        $data_fill = $request->except(['variable', 'current_id']);
        $data_fill['parent_id'] = (isset($data_fill['parent_id'])) ? (int) $data_fill['parent_id'] : 0;
        $data_fill['name'] = (0 === $data_fill['parent_id']) ? $data_fill['name'] : $parent_name;

        if ($block->fill($data_fill) && !$block->validate($data_fill)->fails()) {
            $block->save();
        } else {
            return redirect()->back()->withInput($data_fill)->withErrors($block->errors);
        }

        // variables save
        foreach ($variables as $variable) {
            if (array_get($variable, 'key')) {
                Variable::updateOrCreate([
                    'block_id' => $block->id,
                    'key' => array_get($variable, 'key'),
                ], [
                    'block_id' => $block->id,
                    'key' => array_get($variable, 'key'),
                    'type' => array_get($variable, 'type'),
                    'value' => array_get($variable, 'value'),
                ]);
            }
        }

        return redirect()->route('admin.pages.blocks.edit', [
            'block' => $block->id,
        ]);
    }

    /**
     * Delete block.
     *
     * @param Block   $block
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Block $block, Request $request)
    {
        self::where(['parent_id', $block->id])->delete();
        $block->delete();

        return redirect()->route('admin.pages.blocks');
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
        $get_blocks = $request->get('items', '');
        $get_blocks = explode(',', $get_blocks);

        if (\count($get_blocks)) {
            $find_block = Block::whereIn('id', $get_blocks);
            if ($find_block->count()) {
                foreach ($find_block->get() as $fblock) {
                    self::where(['parent_id', $fblock->id])->delete();
                }
                $find_block->delete();
            }
        }

        return redirect()->route('admin.pages.blocks');
    }

    /**
     * Delete variable.
     *
     * @param Variable $var
     * @param Request  $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function varDestroy(Variable $var, Request $request)
    {
        $id = $var->block_id;

        $var->delete();

        return redirect()->route('admin.pages.blocks.edit', [
            'block' => $id,
        ]);
    }

    /**
     * Build array from request multidata form.
     *
     * @param $formArray
     *
     * @return array
     */
    protected function splitFormArray($formArray)
    {
        $result = [];

        if (\is_array($formArray)) {
            foreach ($formArray as $key => $data) {
                foreach ($data as $k => $value) {
                    $result[$k][$key] = $value;
                }
            }
        }

        return $result;
    }
}
