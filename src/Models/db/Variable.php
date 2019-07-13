<?php

namespace GeekCms\Pages\Models\db;

use App\Models\MainModel;
use GeekCms\Pages\Models\Assigns;
use GeekCms\Pages\Models\Block;
use GeekCms\Pages\Models\Page;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variable extends MainModel
{
    public static $types = [
        'string',
        'text',
        'number',
        'image',
        'file',
        'map',
        'slider',
        'hidden',
        'email',
        'url',
        'phone',
        'color',
        'time',
        'date',
        'datetime',
        'time_range',
        'date_range',
    ];
    /**
     * @internal
     */
    protected $fillable = [
        'block_id', 'type', 'key', 'value',
    ];

    /**
     * @internal
     */
    protected $table = 'page_block_variables';

    /**
     * Relation for block.
     *
     * @return BelongsTo
     */
    public function block()
    {
        return $this->belongsTo(Block::class, 'id', 'block_id');
    }

    /**
     * Relation for pages.
     *
     * @return BelongsToMany
     */
    public function page()
    {
        return $this->belongsToMany(
            Page::class,
            Assigns::tablename(),
            'block_id',
            'page_id'
        );
    }
}
