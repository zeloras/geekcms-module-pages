<table class="table table-hover table-bordered table-custom">
    <thead>
    <tr>
        <th class="table-check"></th>
        <th class="table-title">
            {{ Translate::get('module_pages::admin/main.list.title') }}
        </th>
        <th>
            {{ Translate::get('module_pages::admin/main.list.languages') }}
        </th>
        <th>
            {{ Translate::get('module_pages::admin/main.list.type') }}
        </th>
        <th>
            {{ Translate::get('module_pages::admin/main.list.updated') }}
        </th>
        <th class="table-icon-cell table-actions"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($pages as $page)
        <tr>
            <td class="table-check">
                <div class="checkbox checkbox-only">
                    <input type="checkbox" class="delete-item-check" id="table-check-{{ $page->id }}"
                           value="{{ $page->id }}">
                    <label for="table-check-{{ $page->id }}"></label>
                </div>
            </td>
            <td>
                <div class="d-flex flex-row justify-content-between">
                    <a href="{{ route('admin.pages.edit', ['page' => $page->id]) }}">
                        {{ $page->name }}
                    </a>
                    @if ($default_slug === $page->slug)
                        <span class="label label-pill label-primary">{{ Translate::get('module_pages::admin/main.list.main_page') }}</span>
                    @endif
                </div>
            </td>
            <td>
                <ul class="nav d-flex flex-column table-custom-languages">
                    @php($used_locales = [])
                    @foreach($page->getAvailableTranslates(['lang']) as $locale_key => $locale)
                        @if (!in_array($page->lang, $used_locales))
                            @php($url =  getLocalizedRouteURL($locale, 'page.open', ['page' => $page->slug]))
                            @php($langue = array_get($locales, "{$locale}.name", $locale))

                            <li class="nav-item">
                                <a class="btn-square-icon nav-link"
                                   data-toggle="tooltip" data-placement="left"
                                   data-original-title="{{ Translate::get('module_pages::admin/main.list.action_topage') }}: {{ $url }}"
                                   href="{{ $url }}" target="_blank">
                                    <span class="flag-icon flag-icon-{{$locale}}"></span>
                                    {{ $langue }}
                                </a>
                            </li>
                            @php($used_locales[] = $locale)
                        @endif
                    @endforeach
                </ul>
            </td>
            <td class="color-blue">
                {{ Translate::get("pages::admin/main.type.pages.{$page->type}") }}
            </td>
            <td class="table-date">
                {{ $page->updated_at }} <i class="font-icon font-icon-clock"></i>
            </td>
            <td class="table-icon-cell">
                @php($url_current = route('page.open', ['page' => $page->slug]))
                <a href="{{ $url_current }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ Translate::get('module_pages::admin/main.list.action_topage') }}: {{$url_current}}"
                   class="btn-link btn btn-success-outline btn-sm">
                    <i class="fa fa-globe"></i>
                </a>
                <a href="{{ route('admin.pages.edit', ['page' => $page->id]) }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ Translate::get('module_pages::admin/main.list.action_edit') }}"
                   class="btn-link btn btn-success-outline btn-sm">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="{{ route('admin.pages.delete', ['page' => $page->id]) }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ Translate::get('module_pages::admin/main.list.action_delete') }}"
                   class="btn-link btn btn-success-outline btn-sm"
                   data-delete="{{ Translate::get('module_pages::admin/main.list.action_delete_confirm') }}">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>