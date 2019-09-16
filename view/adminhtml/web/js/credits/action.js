/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
require([
    "jquery",
    "uiRegistry",
    "Magento_Ui/js/modal/confirm",
    "mageUtils",
    "mage/validation"
], function ($, registry, confirm, utils) {
    $(function () {
        "use strict";

        var modal = function (action, confirmFunction) {
            confirm({
                title: action.title,
                content: $("#swarming-credits-actions-modal").html(),
                buttons: [{
                    text: $.mage.__("Cancel"),
                    class: "action-tertiary",
                    click: function(event){
                        this.closeModal(event);
                    }
                }, {
                    text: $.mage.__(action.label),
                    class: "action-secondary",
                    click: function(event) {
                        var $form = $(".modal-content").find("form");
                        if ($form.validation && $form.validation("isValid")) {
                            this.closeModal(event, true);
                        }
                    }
                }],
                actions: {
                    confirm: confirmFunction
                }
            });

            $(".modal-content").find("form").mage('validation');
        };

        registry.set("swarmingCreditsAction", {
            massAction: function (action, selections) {
                modal(action, function() {
                    var $form = $(".modal-content").find("form");
                    utils.submit({
                        url: action.url,
                        data: {
                            adjustment: {
                                amount: $form.find("[name='amount']").val(),
                                summary: $form.find("[name='summary']").val(),
                                suppress_notification: ($form.find("[name='suppress_notification']").is(":checked") ? 1 : 0)
                            },
                            excludeMode: selections.excludeMode,
                            excluded: selections.excluded,
                            selected: selections.selected,
                            total: selections.total,
                            filters: selections.params.filters,
                            namespace: selections.params.namespace
                        }
                    });
                });
            },
            action: function (index, recordId, action) {
                modal(action, function() {
                    var $form = $(".modal-content").find("form");
                    utils.submit({
                        url: action.url,
                        data: {
                            adjustment: {
                                type: action.type,
                                amount: $form.find("[name='amount']").val(),
                                summary: $form.find("[name='summary']").val(),
                                suppress_notification: ($form.find("[name='suppress_notification']").is(":checked") ? 1 : 0)
                            }
                        }
                    });
                });
            }
        });
    });
});
