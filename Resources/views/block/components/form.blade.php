<form method="POST" action="{{ route('admin.pages.blocks.save', ['block' => $block->id ?? null]) }}">
    @csrf
    <input type="hidden" name="current_id"
           value="{{ (isset($block) && !empty($block->parent_id)) ? $block->parent_id : 0 }}">
    <div class="tab-content" id="blockContentTab">
        <div class="tab-pane fade show active" id="form" role="tabpanel" aria-labelledby="form-tab">
            <div class="row">
                @if (isset($block) && empty($block->parent_id))
                    <div class="form-group col-12">
                        <label for="name">{{ Translate::get('module_pages::admin/main.list.block.page_block_name') }}
                            :</label>
                        <input class="form-control" id="name" name="name" value="{{ old('name',$block->name ?? '') }}">
                    </div>
                @endif

                <div class="form-group col-6">
                    <label for="lang">{{ Translate::get('module_pages::admin/main.form.lang') }}:</label>
                    <select class="form-control" id="lang" name="lang" required>
                        @foreach($locales as $locale => $lang)
                            <?php
                            $selected = (isset($block) && !empty($block) && $locale === $block->lang || (!isset($block) || !$block) && $locale === App::getLocale()) ? 'selected' : null;
                            ?>
                            <option value="{{ $locale }}" {{$selected}}>
                                {{ array_get($lang, 'name', $locale) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-6">
                    <label for="parent_id">{{ Translate::get('module_pages::admin/main.form.category') }}:</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option></option>
                        @foreach($blocks_list as $blockItem)
                            @if (!isset($block) || $blockItem->id !== $block->id)
                                @php($selected = (isset($block) && $blockItem->id == $block->parent_id) ? 'selected': null)
                                @php($selected = (old('parent_id') == $blockItem->id) ? 'selected' : $selected)

                                {{--not select self page id--}}
                                @if(!isset($page) || $block->id != $blockItem->id)
                                    <option data-page="{{ $blockItem }}" value="{{ $blockItem->id }}" {{$selected}}>
                                        {{ $blockItem->name }}
                                    </option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>


                <div class="form-group col-12">
                    <label for="content">{{ Translate::get('module_pages::admin/main.list.block.page_table_content') }}
                        :</label>
                    @component('pages::page.components.wysiwyg')
                        @slot('name', 'content')
                        @slot('id', 'content')
                        @slot('class', 'content')
                        @slot('content', $block->content ?? old('content'))
                    @endcomponent
                </div>

            </div>
        </div>
        <div class="tab-pane fade" id="variable" role="tabpanel" aria-labelledby="variable-tab">
            <table class="table table-bordered table-hover table-custom pageblocks-container" id="variables">
                <thead>
                <tr>
                    <th></th>
                    <th>
                            <span class="form-text text-muted">
                                {{ Translate::get('module_pages::admin/main.list.block.variable_key_help') }}
                            </span>
                    </th>
                    <th>
                            <span class="form-text text-muted">
                                {{ Translate::get('module_pages::admin/main.list.block.variable_type_help') }}
                            </span>
                    </th>
                    <th>
                            <span class="form-text text-muted">
                                {{ Translate::get('module_pages::admin/main.list.block.variable_value_help') }}
                            </span>
                    </th>
                    <th class="table-icon-cell table-actions"></th>
                </tr>
                </thead>
                <tbody class="pageblocks-container-wrap">
                <tr id="variableRow0" class="pageblocks-container__line">
                    <th scope="row"></th>
                    <td>
                        <input data-key="key" name="variable[key][]" type="text" class="form-control"
                               placeholder="{{ Translate::get('module_pages::admin/main.list.block.variable_key_help') }}">
                    </td>
                    <td>
                        <select data-key="type" name="variable[type][]" class="form-control">
                            @inject('variableModel','GeekCms\Pages\Models\Variable')
                            @if(isset($variableModel::$types))
                                @foreach($variableModel::$types as $variableType)
                                    <option value="{{ $variableType }}">
                                        {{ $variableType }}
                                    </option>
                                @endforeach
                            @endif

                        </select>
                    </td>
                    <td>
                        <input data-key="value" type="text" name="variable[value][]" class="form-control"
                               placeholder="{{ Translate::get('module_pages::admin/main.list.block.variable_value_help') }}">
                    </td>
                    <td>
                        <input data-key="uid" name="variable[uid][]" type="hidden" value="">
                        <button type="button"
                                data-action="{{ route('admin.pages.blocks.var_delete', ['var' => null]) }}"
                                class="btn btn-primary pageblocks-container-remove"
                                title="{{ Translate::get('module_pages::admin/main.list.block.page_delete_variable') }}">
                            <i class="fa fa-minus-circle"></i>
                        </button>
                        <button data-key="create" type="button" class="btn btn-success pageblocks-container-add"
                                title="{{ Translate::get('module_pages::admin/main.list.block.page_create_variable') }}">
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group text-center pt-3">
        <button type="submit" class="btn btn-primary">
            @if(isset($block))
                {{ Translate::get('module_pages::admin/main.form.action_block_save') }}
            @else
                {{ Translate::get('module_pages::admin/main.form.action_block_create') }}
            @endif
        </button>
    </div>
</form>

@push('script')
    <script>
        var pageblocks_list_admin = '@json((isset($block) && $block->variables->count()) ? $block->variables : [[]])';
    </script>
@endpush
