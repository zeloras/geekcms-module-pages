@extends('admin.layouts.main')

@section('title',  Translate::get('module_pages::admin/main.list.block.page_edit_title') )

@section('content')
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title border-bottom">
                    <h3>{{ Translate::get('module_pages::admin/main.form.page_title_edit') }}</h3>
                </div>
            </div>
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title border-bottom">
                    <nav class="nav pages-tabs-lang">
                        @foreach($tabs as $tab)
                            <a class="nav-link {{ ($tab['active']) ? 'disabled active' : '' }}"
                               href="{{ $tab['url'] }}">
                                {{ $tab['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </header>
        <div class="box-typical-body pt-3 pb-3">
            <div class="table-responsive container">
                <div class="row">
                    <div class="col-12">
                        @if(isset($page))
                            @include('pages::page.components.form', [$page, $default_slug])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop