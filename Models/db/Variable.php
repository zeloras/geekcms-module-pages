<?php

namespace Modules\Pages\Models\db;

use App\Models\MainModel;
use Modules\Pages\Models\Assigns;
use Modules\Pages\Models\Block;
use Modules\Pages\Models\Page;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->belongsTo(Block::class, 'id', 'block_id');
    }

    /**
     * Relation for pages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
