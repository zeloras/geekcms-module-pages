<?php

namespace GeekCms\Pages\Models\db;

use App\Models\MainModel;
use Illuminate\Validation\Rule;
use GeekCms\Pages\Models\Assigns;
use GeekCms\Pages\Models\Block;
use GeekCms\Pages\Models\Page as MainPageModel;

class Page extends MainModel
{
    const PAGE_TYPE_PAGE = 'page';
    const PAGE_TYPE_TRANSLATE = 'trans';

    /**
     * Key for get config and check if page is main.
     */
    const MAIN_PAGE_KEY = 'module_pages.main_page_slug';

    /**
     * Open graph types for seo.
     *
     * @var array
     */
    public static $openGraphTypes = [
        'article',
        'product',
        'book',
        'profile',
        'place',
        'video.other',
        'music.song',
    ];

    /**
     * Page types.
     *
     * @var array
     */
    public static $types = [
        self::PAGE_TYPE_PAGE,
        self::PAGE_TYPE_TRANSLATE,
        //'block',
    ];

    /**
     * @internal
     */
    public $rules = [
        'name' => ['required', 'max:255', 'min:1'],
        'lang' => ['required', 'max:3', 'min:1', 'translatePage'],
        'type' => ['required', 'min:1'],
        'theme' => ['required', 'min:1'],
        'slug' => ['min:1', 'checkSlug'],
        'parent_id' => ['nullable', 'string', 'sometimes', 'exists:pages,id'],
    ];

    /**
     * @internal
     */
    public $with = [
        'assigns',
    ];

    /**
     * @internal
     */
    protected $table = 'pages';

    /**
     * @todo slug validate needed
     *
     * @internal
     */
    protected $fillable = [
        'parent_id', 'lang', 'type',
        'theme', 'name', 'slug', 'content',
    ];

    /**
     * {@inheritdoc}
     */
    public function fill(array $attributes)
    {
        $this->messages = ['*.check_slug' => \Translate::get('module_pages::admin/validate.check_slug')];
        $this->messages = ['*.translate_page' => \Translate::get('module_pages::admin/validate.check_translate_duplicate')];
        $this->rules['type'][] = Rule::in(self::$types);

        return parent::fill($attributes);
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

    public function assigns()
    {
        return $this->hasMany(Assigns::class, 'page_id', 'id');
    }

    /**
     * Parent elements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(MainPageModel::class, 'parent_id');
    }

    /**
     * Child elements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(MainPageModel::class, 'parent_id');
    }
}
