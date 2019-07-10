<table class="table table-hover table-bordered table-custom">
    <thead>
    <tr>
        <th class="table-check"></th>
        <th class="table-title">{{ Translate::get('module_pages::admin/main.list.block.page_table_title') }}</th>
        <th>
            {{ Translate::get('module_pages::admin/main.list.block.languages') }}
        </th>
        <th>{{ Translate::get('module_pages::admin/main.list.block.page_table_updated') }}</th>
        <th>{{ Translate::get('module_pages::admin/main.list.block.page_table_variables') }}</th>
        <th class="table-icon-cell table-actions"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($blocks as $block)
        <tr>
            <td class="table-check">
                <div class="checkbox checkbox-only">
                    <input type="checkbox" class="delete-item-check" id="table-check-{{ $block->id }}"
                           value="{{ $block->id }}">
                    <label for="table-check-{{ $block->id }}"></label>
                </div>
            </td>
            <td>
                <a href="{{ route('admin.pages.blocks.edit', ['block' => $block->id]) }}">
                    {{ $block->name }}
                </a>
            </td>
            <td>
                <ul class="nav d-flex flex-column table-custom-languages">
                    @php($used_locales = [])
                    @foreach($block->getAvailableTranslates(['lang']) as $locale_key => $locale)
                        @if (!in_array($block->lang, $used_locales))
                            @php($langue = array_get($locales, "{$locale}.name", $locale))

                            <li class="nav-item">
                                <a class="btn-square-icon nav-link" href="#">
                                    <span class="flag-icon flag-icon-{{$locale}}"></span>
                                    {{ $langue }}
                                </a>
                            </li>
                            @php($used_locales[] = $locale)
                        @endif
                    @endforeach
                </ul>
            </td>
            <td class="table-date">
                {{ $block->updated_at }} <i class="font-icon font-icon-clock"></i>
            </td>
            <td>
                @foreach($block->variables as $variable)
                    <a href="#" class="badge badge-secondary">{{ $variable->key }}</a>
                @endforeach
            </td>

            <td class="table-icon-cell">
                <a href="{{ route('admin.pages.blocks.edit', ['block' => $block->id]) }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ Translate::get('module_pages::admin/main.list.block.page_table_edit') }}"
                   class="btn-link btn btn-success-outline btn-sm">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="{{ route('admin.pages.blocks.delete', ['block' => $block->id]) }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ Translate::get('module_pages::admin/main.list.block.page_table_delete') }}"
                   class="btn-link btn btn-success-outline btn-sm"
                   data-delete="{{ Translate::get('module_pages::admin/main.list.block.action_delete_confirm') }}">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


