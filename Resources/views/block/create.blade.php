@extends('admin.layouts.main')

@section('title',  \Translate::get('pages::admin/main.list.block.action_index_create') )

@section('content')
<section class="box-typical">
    <header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title border-bottom">
                <h3>{{ \Translate::get('pages::admin/main.list.block.action_index_create') }}</h3>
            </div>
        </div>
    </header>
    <header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
                <ul class="nav nav-tabs" id="blockControlTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab" aria-controls="form" aria-selected="true">
                            {{ \Translate::get('pages::admin/main.list.block.page_table_content') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="variable-tab" data-toggle="tab" href="#variable" role="tab" aria-controls="variable" aria-selected="false">
                            {{ \Translate::get('pages::admin/main.list.block.page_table_variables') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="box-typical-body pt-3 pb-3">
        <div class="table-responsive container">
            <div class="row">
                <div class="col-12">
                    @include('pages::block.components.form', [$block, $locales, $blocks_list])
                </div>
            </div>
        </div>
    </div>
</section>
@stop