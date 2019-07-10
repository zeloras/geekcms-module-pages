<form action="{{ route('admin.pages.save', ['page' => $page->id ?? null]) }}" method="POST">
    @csrf
    @if (isset($page) && $page)
        <input type="hidden" name="edit_id" value="{{$page->id}}">
    @endif
    <div class="row">
        <div class="form-group col-6">
            <label for="name">{{ Translate::get('module_pages::admin/main.form.name') }}:</label>
            <input class="form-control pages_admin_name"
                   id="name"
                   name="name"
                   value="{{ $page->name ?? old('name') }}"
                   required>
        </div>

        <div class="form-group col-6">
            <label for="type">{{ Translate::get('module_pages::admin/main.form.type') }}:</label>
            <select class="form-control pages_admin_type" id="type" name="type" required>
                <option></option>
                @foreach(GeekCms\Pages\Models\Page::$types as $type)
                    @php($selected = (isset($page) && $page->type == $type) ? 'selected': null)
                    @php($selected = (old('type') == $type) ? 'selected' : $selected)

                    <option value="{{ $type }}" {{$selected}}>
                        {{ Translate::get("pages::admin/main.type.pages.{$type}") }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-6">
            <label for="lang">{{ Translate::get('module_pages::admin/main.form.lang') }}:</label>
            <select class="form-control" id="lang" name="lang" required>
                @foreach($locales as $locale => $lang)
                    <?php
                    $selected = ($page && $locale === $page->lang || !$page && $locale === App::getLocale()) ? 'selected' : null;
                    ?>
                    <option value="{{ $locale }}" {{$selected}}>
                        {{ array_get($lang, 'name', $locale) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-6">
            <label for="category">{{ Translate::get('module_pages::admin/main.form.category') }}:</label>
            <select class="form-control" id="category" name="parent_id" required>
                <option></option>
                @foreach($categories as $pageCategory)
                    @php($selected = (isset($page) && $pageCategory->id == $page->parent_id) ? 'selected': null)
                    @php($selected = (old('parent_id') == $pageCategory->id) ? 'selected' : $selected)

                    {{--not select self page id--}}
                    @if(!isset($page) || $page->id != $pageCategory->id)
                        <option data-page="{{ $pageCategory }}" value="{{ $pageCategory->id }}" {{$selected}}>
                            {{ $pageCategory->name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="form-group col-6">
            <label for="slug">{{ Translate::get('module_pages::admin/main.form.slug') }}:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        {{ url(DIRECTORY_SEPARATOR) }}/
                    </span>
                </div>
                <input class="form-control pages_admin_slug"
                       id="slug"
                       name="slug"
                       value="{{ $page->slug ?? old('slug') }}"
                       max="50"
                       required>
            </div>
        </div>

        <div class="form-group col-6">
            <label for="main_page">{{ Translate::get('module_pages::admin/main.form.do_main_page') }}:</label>
            <select class="form-control" id="main_page" name="main_page">
                <option value="0"
                        @if ((empty($default_slug) || empty($page->slug)) && $page->slug !== $default_slug) selected="selected" @endif>{{ Translate::get('module_pages::admin/main.form.do_main_page_no') }}</option>
                <option value="1"
                        @if (!empty($default_slug) && !empty($page->slug) && $page->slug === $default_slug) selected="selected" @endif>{{ Translate::get('module_pages::admin/main.form.do_main_page_yes') }}</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label for="theme">{{ Translate::get('module_pages::admin/main.form.theme') }}:</label>
            <select class="form-control" id="theme" name="theme" required>
                <option></option>
                @foreach(Theme::all() as $theme)
                    @php($selected = (isset($page) && $page->theme == $theme->name) ? 'selected': null)
                    @php($selected = (old('theme') == $theme->name) ? 'selected' : $selected)

                    <option value="{{ $theme->name }}" {{$selected}}>{{ $theme->name }}</option>
                @endforeach
            </select>
        </div>

        @if (count($blocks) || count($assigned))
            <div class="form-group col-12">
                <div class="col-6 pl-0">
                    <label for="blocks-search">{{ Translate::get('module_pages::admin/main.form.find_blocks_for_append') }}
                        :</label>
                    <div class="typeahead-container">
                        <div class="typeahead-field">
                        <span class="typeahead-query">
                            <input class="form-control page-blocks-search" id="blocks-search" type="text"
                                   autocomplete="off">
                        </span>
                        </div>
                    </div>
                    <div class="page-blocks-sortable panels"></div>
                </div>
            </div>
        @endif

        <div class="form-group col-12">
            <label for="inputId">{{ Translate::get('module_pages::admin/main.form.content') }}:</label>

            @include('pages::page.components.wysiwyg', [
                'name' => 'content',
                'id' => 'content',
                'class' => 'content',
                'content' => $page->content ?? old('content')
            ])
        </div>

        <div class="form-group text-center pt-3 col-12">
            <button type="submit" class="btn btn-primary">
                @if(isset($page))
                    {{ Translate::get('module_pages::admin/main.form.action_save') }}
                @else
                    {{ Translate::get('module_pages::admin/main.form.action_create') }}
                @endif
            </button>
        </div>
    </div>
</form>

<template class="page-blocks-template">
    <div class="page-blocks-template" data-elements=".page-blocks-item">
        <section class="page-blocks-item panel panel-info %is.data.main%" data-set-index=".page-blocks-position"
                 data-match="%data.name%-%data.id%">
            <div class="card-header">
                <div class="card-title">
                    <div class="page-blocks-switcher">
                        <div class="checkbox-toggle">
                            <input type="hidden" name="%input_group%[old_position][%id%]" value="%data.old_position%">
                            <input type="hidden" name="%input_group%[block_id][%id%]" value="%data.id%">
                            <input type="hidden" class="page-blocks-position" name="%input_group%[position][%id%]"
                                   value="%data.position%">
                            <input type="checkbox" value="1" name="%input_group%[enabled][%id%]"
                                   id="check-toggle-blocks-%input_group%-%id%" %is.data.enabled%>
                            <label for="check-toggle-blocks-%input_group%-%id%"></label>
                        </div>
                    </div>
                    <div class="page-blocks-name">
                        %data.name%
                    </div>
                </div>
            </div>
            <div class="card-block">
                %data.content_compile%
            </div>
        </section>
    </div>
</template>

@push('script')
    <script>
        var pages_blocks_list = '@json($blocks)';
        var page_blocks_list_enabled = '@json($assigned)';
    </script>
@endpush