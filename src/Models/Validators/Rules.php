<?php

namespace GeekCms\Pages\Models\Validators;

use GeekCms\Pages\Models\Page;
use Illuminate\Validation\Validator;
use function count;

class Rules extends Validator
{
    /**
     * Validate page slug.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return null|bool
     */
    public function validateCheckSlug($attribute, $value, $parameters)
    {
        $edit_id = (isset($this->data['edit_id'])) ? (int)$this->data['edit_id'] : 0;
        if (Page::PAGE_TYPE_PAGE === $this->data['type']) {
            $find_page = Page::where([
                ['slug', $this->data['slug']],
                ['id', '!=', $edit_id],
                ['type', Page::PAGE_TYPE_PAGE],
            ])->get();

            if (count($find_page)) {
                return null;
            }
        }

        return true;
    }

    /**
     * Validate translate.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return null|bool
     */
    public function validateTranslatePage($attribute, $value, $parameters)
    {
        $edit_id = (isset($this->data['edit_id'])) ? (int)$this->data['edit_id'] : 0;
        $parent_id = (isset($this->data['parent_id'])) ? (int)$this->data['parent_id'] : 0;
        $lang = (isset($this->data['lang'])) ? $this->data['lang'] : config('app.locale');

        if ($parent_id > 0) {
            $find_page = Page::where(function ($query) use ($parent_id, $lang) {
                return $query->where([
                    ['parent_id', '=', $parent_id],
                    ['lang', '=', $lang],
                    ['type', '!=', Page::PAGE_TYPE_PAGE],
                ])->orWhere([
                    ['id', '=', $parent_id],
                    ['lang', '=', $lang],
                    ['type', '=', Page::PAGE_TYPE_PAGE],
                ]);
            })->where([['id', '!=', $edit_id]])->get();

            if (count($find_page)) {
                return null;
            }
        }

        return true;
    }
}
