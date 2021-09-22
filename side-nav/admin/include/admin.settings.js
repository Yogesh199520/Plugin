jQuery(document).ready(function ($) {
    $(".smpnav_instance_notice_close, .smpnav_instance_close").on("click", function () {
        $(".smpnav_instance_wrap").fadeOut();
    });
    $(".smpnav_instance_wrap").on("click", function (e) {
        if ($(e.target).hasClass("smpnav_instance_wrap")) {
            $(this).fadeOut();
        }
    });

    $(".smpnav_instance_toggle").on("click", function () {
        $(".smpnav_instance_container_wrap").fadeIn();
        $(".smpnav_instance_container_wrap .smpnav_instance_input").focus();
    });

    $form = $("form.smpnav_instance_form");
    $form.on("submit", function (e) {
        e.preventDefault();
        smpnav_save_instance();
        return false;
    });

    $(".smpnav_instance_create_button").on("click", function (e) {
        e.preventDefault();
        smpnav_save_instance();
        return false;
    });

    $(".smpnav_button_reset").on("click", function (e) {
        var r = confirm('Are you really want to undone?  if you click on "OK", then all setting will be losed in this tab.  This cannot be undone.');
        if (r == false) {
            e.preventDefault();
            return false;
        }
    });

    function smpnav_save_instance() {
        var data = {
            action: "smpnav_add_instance",
            smpnav_data: $form.serialize(),
            smpnav_nonce: $form.find("#_wpnonce").val(),
        };

        jQuery
            .post(
                ajaxurl,
                data,
                function (response) {
                    console.log(response);

                    if (response == -1) {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_notice_error").fadeIn();

                        $(".smpnav-error-message").text("Please try again.");

                        return;
                    } else if (response.error) {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_notice_error").fadeIn();

                        $(".smpnav-error-message").text(response.error);

                        return;
                    } else {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_notice_success").fadeIn();
                    }
                },
                "json"
            )
            .fail(function () {
                $(".smpnav_instance_container_wrap").fadeOut();
                $(".smpnav_instance_notice_error").fadeIn();
            });
    }

    $(".smpnav_instance_button_delete").on("click", function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to delete this smpnav Instance?")) {
            smpnav_delete_instance($(this));
        }
        return false;
    });

    function smpnav_delete_instance($a) {
        var data = {
            action: "smpnav_delete_instance",
            smpnav_data: {
                smpnav_instance_id: $a.data("smpnav-instance-id"),
            },
            smpnav_nonce: $a.data("smpnav-nonce"),
        };

        jQuery
            .post(
                ajaxurl,
                data,
                function (response) {
                    console.log(response);

                    if (response == -1) {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_delete_notice_error").fadeIn();

                        $(".smpnav-delete-error-message").text("Please try again.");

                        return;
                    } else if (response.error) {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_delete_notice_error").fadeIn();

                        $(".smpnav-delete-error-message").text(response.error);

                        return;
                    } else {
                        $(".smpnav_instance_container_wrap").fadeOut();
                        $(".smpnav_instance_delete_notice_success").fadeIn();

                        var id = response.id;
                        $("#smpnav_" + id + ", #smpnav_" + id + "-tab").remove();
                        $(".nav-tab-wrapper > a").first().trigger("click");
                    }
                },
                "json"
            )
            .fail(function () {
                $(".smpnav_instance_container_wrap").fadeOut();
                $(".smpnav_instance_delete_notice_error").fadeIn();
            });
    }

    function smpnav_selected_selectText(element) {
        var doc = document,range,selection;
        if (doc.body.createTextRange) {
            range = doc.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = doc.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    $(".smpnav-highlight-code").on("click", function (e) {
        smpnav_selected($(this)[0]);
    });

    setTimeout(function () {
        if (window.location.hash) {
            $(window.location.hash + "-tab").trigger("click");
        }
    }, 500);
});
