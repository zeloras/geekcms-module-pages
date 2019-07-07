<?php

namespace Modules\Pages\Models\db;

use App\Models\MainModel;
use Modules\Pages\Models\Block;

class Assigns extends MainModel
{
    /**
     * @internal
     */
    public $with = [
        'block',
    ];

    /**
     * @internal
     */
    public $rules = [
        'page_id' => ['required', 'numeric', 'exists:pages,id'],
        'block_id' => ['required', 'string', 'exists:page_blocks,id'],
        'position' => ['string', 'min:0'],
        'enabled' => ['nullable', 'sometimes', 'boolean'],
    ];
    /**
     * @internal
     */
    protected $appends = ['old_position'];

    /**
     * @internal
     */
    protected $casts = [
        'old_position' => 'integer',
        'position' => 'integer',
        'enabled' => 'boolean',
    ];

    /**
     * @internal
     */
    protected $table = 'page_block_assings';

    /**
     * @internal
     */
    //public $incrementing = false;

    /**
     * @internal
     */
    //protected $primaryKey = null;

    /**
     * @internal
     */
    protected $fillable = [
        'page_id', 'block_id', 'position', 'enabled',
    ];

    public function block()
    {
        return $this->hasOne(Block::class, 'id', 'block_id');
    }

    public function getOldPositionAttribute()
    {
        return $this->getOriginal('position', 0);
    }
}
