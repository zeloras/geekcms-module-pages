@extends('admin.layouts.main')

@section('title',  \Translate::get('pages::admin/main.list.block.page_index_title') )

@section('content')
<section class="box-typical container pb-3">
    <header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
                <h3>{{ \Translate::get('pages::admin/main.list.block.page_index_title') }}</h3>
            </div>
            <div class="tbl-cell tbl-cell-action-bordered">
                <a href="{{ route('admin.pages.blocks.create') }}"
                   data-toggle="tooltip" data-placement="left"
                   data-original-title="{{ \Translate::get('pages::admin/main.list.block.action_index_create') }}" class="action-btn">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
            <div class="tbl-cell tbl-cell-action-bordered">
                <button type="button" data-token="{!! csrf_token() !!}" data-inputs=".delete-item-check:checked"
                        data-toggle="tooltip" data-placement="left"
                        data-original-title="{{ \Translate::get('pages::admin/main.list.block.page_delete_selected') }}"
                        data-text="{{ \Translate::get('pages::admin/main.list.block.action_delete_confirm') }}" data-action="{{ route('admin.pages.blocks.delete.all') }}"
                        class="action-btn delete-all">
                    <i class="font-icon font-icon-trash"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="box-typical-body">
        <div class="table-responsive">
            @component('pages::block.components.table')
                @slot('blocks', $blocks ?? collect())
                @slot('locales', $locales)
            @endcomponent
        </div>
    </div>
</section>
@stop
