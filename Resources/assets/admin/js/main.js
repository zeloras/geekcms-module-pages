var modulePagesAdmin = {
    config: {
        'page_type_select': '.pages_admin_type',
        'page_slug_field': '.pages_admin_slug',
        'page_slug_field_class': 'pages_admin_slug',
        'page_slug_name': '.pages_admin_name',
        'page_blocks_lists': '.page-blocks-sortable',
        'page_blocks_items': '.page-blocks-item',
        'page_blocks_search': '.page-blocks-search',
        'pages_blocks_template': '.page-blocks-template',
        'main_config': {
            //block: {
            //    off: '#category, #slug, #main_page', on: '#theme', required: true
            //},
            page: {
                off: '#category', on: '#slug, #theme, #main_page', required: true
            },
            trans: {
                off: '#slug, #theme, #main_page', on: '#category', required: true
            }
        }
    },

    init: function (preload) {
        let self = this;
        let config = self.config;

        adminMainComponent.toggleFormGroup($(config.page_type_select), config.main_config);
        $(config.page_type_select).unbind('change').on('change', function () {
            adminMainComponent.toggleFormGroup(this, config.main_config);
        });

        $(config.page_slug_field + ', ' + config.page_slug_name).keyup(function (e) {
            self.setSlugGenerate(this);
        });

        if (preload) {
            self.preloadBlocks();
        }

        self._rebuildBlocksScripts(true);

        $(config.page_blocks_items).unbind('dragged.lobiCard').on('dragged.lobiCard', function (e, elem) {
            self._rebuildBlocksScripts(false);
        });

        $(config.page_blocks_items).unbind('onClose.lobiCard').on('onClose.lobiCard', function () {
            self._rebuildBlocksScripts(true);
        });
    },

    _rebuildBlocksScripts: function (destroy) {
        let self = this;
        let config = self.config;
        destroy = destroy || false;

        if (destroy) {
            $(config.page_blocks_items).lobiCard('destroy');
        }

        setTimeout(function () {
            if (destroy) {
                $(config.page_blocks_items).lobiCard({
                    reload: false,
                    editTitle: false,
                    expand: false,
                    changeStyle: false,
                    state: 'collapsed',
                    sortable: true,
                    draggable: true,
                    unpin: false
                });
            }
        }, 50);

        setTimeout(function () {
            $(config.page_blocks_items).each(function () {
                let item = $(this);
                let index = Number(item[0].attributes['data-index'].value);
                let set_index_to = item.data('set-index');
                let input = item.find(set_index_to);

                if (input && input.length) {
                    input.val(index);
                }
            });
        }, 100);
    },

    preloadBlocks: function () {
        let self = this;
        let config = self.config;

        if (window.hasOwnProperty('page_blocks_list_enabled') && page_blocks_list_enabled.length) {
            adminMainComponent.appendTemplate({
                template: config.pages_blocks_template,
                insert: config.page_blocks_lists,
                data: page_blocks_list_enabled,
                input_group: 'model_data_edit'
            });
        }

        if (window.hasOwnProperty('pages_blocks_list') && pages_blocks_list.length) {
            $(config.page_blocks_search).typeahead({
                input: config.page_blocks_search,
                order: "asc",
                minLength: 1,
                display: ["name"],
                source: {
                    data: pages_blocks_list
                },
                filter: function (item, displayKey) {
                    let match = item.name + '-' + item.id;
                    if (item.hasOwnProperty('id') && item.hasOwnProperty('name')) {
                        let find = $(config.page_blocks_lists).find('[data-match="' + match + '"]');
                        if (find && find.length) {
                            return undefined;
                        }
                    }

                    return item;
                },
                callback: {
                    onClick: function (node, a, item_data) {
                        adminMainComponent.appendTemplate({
                            template: config.pages_blocks_template,
                            insert: config.page_blocks_lists,
                            data: [item_data],
                            input_group: 'model_data_new'
                        });
                        self.init();

                        setTimeout(function () {
                            $(config.page_blocks_search).val(null);
                        }, 250);
                    }
                }
            });
        }
    },

    /**
     * Set handles for url fields
     *
     * @param elem
     */
    setSlugGenerate: function (elem) {
        let self = this;
        let config = self.config;
        let el = $(elem);
        let name = $(config.page_slug_name).val();
        let can_edit = ($(config.page_slug_field).val().length !== 0 || name.length !== 0);

        if (el.hasClass(config.page_slug_field_class)) {
            adminMainComponent.holdTimeout('url_slug_convert', function () {
                el.val(adminMainComponent.generateSlug(el.val()));
            }, 1000);
        } else {
            if (can_edit) {
                $(config.page_slug_field).val(adminMainComponent.generateSlug(name));
            }
        }
    }
};

$(document).on('mainComponentsAdminLoaded', function () {
    modulePagesAdmin.init(true);
});