<?php

namespace GeekCms\Pages\Models\db;

use App\Models\MainModel;
use GeekCms\Pages\Models\Block as MainBlockModel;
use GeekCms\Pages\Models\Variable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

class Block extends MainModel
{
    /**
     * @internal
     */
    public $rules = [
        'name' => ['required', 'max:255', 'min:1'],
        'content' => ['nullable', 'string', 'sometimes'],
        'lang' => ['required', 'max:3', 'min:1'],
        'parent_id' => ['nullable', 'numeric', 'sometimes'],
    ];

    /**
     * @internal
     */
    public $with = [
        'variables',
        'children',
    ];

    /**
     * @internal
     */
    //protected $touches = ['page_assign'];

    /**
     * @internal
     */
    protected $fillable = [
        'name', 'content', 'lang', 'parent_id',
    ];

    /**
     * @internal
     */
    protected $table = 'page_blocks';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->rules['name'][] = function ($attribute, $value, $fail) {
            $uid = data_get($this, 'id', null);
            $find = MainBlockModel::where([
                ['name', '=', $value],
                ['id', '!=', $uid],
                ['lang', '=', $this->lang],
            ])->first();

            if (!empty($find)) {
                $fail($attribute . ' not unique');
            }
        };

        $this->rules['parent_id'][] = function ($attribute, $value, $fail) {
            if (empty($value) && 0 !== (int)$value) {
                return Rule::exists('page_blocks', 'id');
            }

            return null;
        };

        parent::__construct($attributes);
    }

    /**
     * Get block variables.
     *
     * @return HasMany
     */
    public function variables()
    {
        return $this->hasMany(Variable::class, 'block_id', 'id');
    }

    /**
     * Children elements.
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(MainBlockModel::class, 'parent_id');
    }

    /**
     * Parent elements.
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(MainBlockModel::class, 'parent_id');
    }
}
