var modulePagesBlocksAdmin = {
    config: {
        'table': '.pageblocks-container',
        'line': '.pageblocks-container__line',
        'wrap': '.pageblocks-container-wrap',
        'button': '.pageblocks-container-add',
        'remove': '.pageblocks-container-remove',
        'row_id': 'variableRow'
    },

    init: function () {
        let self = this;
        let config = self.config;

        $(config.button).unbind('click').on('click', function () {
            self.addConfig(null, null, null, null, true);
        });

        $(config.remove).unbind('click').on('click', function () {
            self.removeVar(this);
        });
    },

    /**
     * Remove variable from block
     *
     * @param elem
     * @return {boolean}
     */
    removeVar: function (elem) {
        let self = this;
        let config = self.config;
        let item = $(elem);
        let action = item.data('action');
        let message = item.data('message');
        let find_id = item.parent().find('[data-key="uid"]');

        if (item.length && action && find_id && find_id.length && find_id.val().length) {
            let xhr = new XMLHttpRequest();

            xhr.onload = xhr.onerror = function() {
                item.parents(config.line).remove();
                self.hideButtons();
            };

            xhr.open("GET", action + '/' + find_id.val(), true);
            if (message && message.length) {
                if (!confirm(message)) {
                    return false;
                }
            }
            xhr.send();
        } else {
            item.parents(config.line).remove();
            self.hideButtons();
        }
    },

    /**
     * Append lines
     *
     * @param pageblocks_list_admin
     */
    loadConfig: function (pageblocks_list_admin) {
        let self = this;

        if (pageblocks_list_admin) {
            for (let key in pageblocks_list_admin) {
                let plist = pageblocks_list_admin[key];
                self.addConfig(plist.key, plist.value, plist.type, plist.id);
            }
        }
    },

    /**
     * Add new config line
     *
     * @param key
     * @param value
     * @param type
     * @param uid
     * @param rebind
     */
    addConfig: function (key, value, type, uid, rebind) {
        let self = this;
        let config = self.config;
        let line = $(config.line);

        key = key || null;
        value = value || null;
        type = type || null;
        rebind = rebind || null;
        uid = uid || null;

        let id = line.length;
        let last = line.first().hide().clone(true);
        let wrap = $(config.wrap);

        last.attr('id', config.row_id + id);


        adminMainComponent.setFormElementValue(last.find('[data-key="key"]'), key);
        adminMainComponent.setFormElementValue(last.find('[data-key="type"]'), type);
        adminMainComponent.setFormElementValue(last.find('[data-key="value"]'), value);
        adminMainComponent.setFormElementValue(last.find('[data-key="uid"]'), uid);

        wrap.append(last.show());

        if (rebind) {
            self.init();
            self.hideButtons();
        }
    },

    /**
     * Hide/show remove/add buttons
     */
    hideButtons: function () {
        let self = this;
        let config = self.config;

        $(config.button + ':last').removeClass('hidden');
        $(config.button + ':not(:last)').addClass('hidden');
         $(config.remove + ':not(:last)').removeClass('hidden');

        if ($(config.remove).length <= 2) {
            $(config.remove + ':last').addClass('hidden');
        } else {
            $(config.remove + ':last').removeClass('hidden');
        }
    }
};

if (window.hasOwnProperty('pageblocks_list_admin')) {
    modulePagesBlocksAdmin.loadConfig(pageblocks_list_admin);
}

$(document).on('mainComponentsAdminLoaded', function () {
    modulePagesBlocksAdmin.init();
    modulePagesBlocksAdmin.hideButtons();
});
