(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  $(document).ready(function () {
    $(".l2t-container .chosen-select").chosen();
    $(".l2t-container #error_invalid_filter").addClass("d_none");
    $("#l2t-api-button").on("click", function (e) {
      let private_key = $("#l2t-private-key").val();
      if (private_key) {
        $("#l2t-api-button").prop("disabled", true);
        $(".l2t-container #error_main_sec").addClass("d_none");
        $(".l2t-container #ajax_response").text("");
        l2t_form_submit("validate");
      } else {
        l2t_invalid_private_key();
      }
    });

    if ($("#l2t-api-key").length && $("#l2t-api-key").val()) {
      let private_api = $("#l2t-private-key").val();
      get_configuration_details(private_api);
    }

    $("body").on("change", '.l2t-container .one_input input[type="radio"]', function () {

      l2t_filter_toggle();
      let private_api = document.getElementById("l2t-private-key");
      if (private_api) {
        //////
        if($(this).val() === "filter"){
          get_configuration_details(private_api.value);
        }
      }
    });
    $("body").on("change", "#l2t_widget_hide", function () {
      l2t_metabox_widget_toggle();
    });
    $(".l2t-container .ltr-settings-save").on("click", () => {
      l2t_form_submit("save");
    });
  });


    $( "body" ).on( "mouseover",".l2t_invalid_filter", function() {
      $(".l2t-container .helper_invalid_filter").addClass("visibility_show");
    });
    $( "body" ).on( "mouseleave",".l2t_invalid_filter", function() {
      $(".l2t-container .helper_invalid_filter").removeClass("visibility_show");
    });

  function l2t_filter_toggle() {
    let l2tFilter = document.querySelector(
      "input[name=l2tFilter]:checked"
    ).value;
    if (l2tFilter == "all" || l2tFilter == "default") {
      /////$(".l2t-container .search-choice-close").click();
      $(".l2t-container .defult_confi_form .filter_box").addClass("l2t-disable");

    } else {
      $(".l2t-container .defult_confi_form .filter_box").removeClass("l2t-disable");
    }
  }

  function l2t_metabox_widget_toggle() {
    let l2t_widget_hide = document.getElementById("l2t_widget_hide");
    if (l2t_widget_hide && l2t_widget_hide.checked == true) {
      document.querySelector(".l2t-container .box_form_full").classList.add("d_none");
      document.querySelector(".l2t-container .filter_box").classList.add("d_none");
    } else {
      document.querySelector(".l2t-container .box_form_full").classList.remove("d_none");
      document.querySelector(".l2t-container .filter_box").classList.remove("d_none");
    }
  }

/**
 * VALIDATE FUNCTION
 * event:  validate/save
 * 
 * **/
  function l2t_form_submit(event) {
    let private_api = $.trim(
      document.getElementById("l2t-private-key").value
    );
    let l2t_filter_type = document.getElementById("l2t-filter-type").value;
    let l2t_widget_hide = document.getElementById("l2t_widget_hide");
    let l2tFilter = document.querySelector(
      "input[name=l2tFilter]:checked"
    ).value;
    let ajax_action = "l2t_get_api_key";

    if(event === "save"){
      $(".chosen-choices .l2t_invalid_filter .search-choice-close").trigger("click");
    }

    if (l2t_filter_type == "meta") {
      if (
        l2t_widget_hide &&
        l2t_widget_hide.checked !== true &&
        l2tFilter == "filter"
      ) {
        ajax_action = "l2t_metabox_ajax";
      } else {
        ajax_action = "l2t_metabox_save_ajax";
      }
    }

    if (private_api && ajax_action !== "") {
      $.ajax({
        url: l2t_admin_ajax_object.ajax_url,
        type: "post",
        data: {
          action: ajax_action,
          form_data: $(".l2t-container .ltr-settings-save")
            .closest("form")
            .serializeArray(),
        },
        beforeSend: function () {
          if (event == "validate") {
            $(".l2t-container .validation_check .loading_img").removeClass("d_none");
          }
          $(".l2t-container .configuration_overlay")
            .addClass("position_overlay")
            .removeClass("d_none");
        },
        success: function (res) {
          if (ajax_action != "l2t_metabox_save_ajax") {
            let json = JSON.parse(res);
            $("#l2t-api-key").val("");
            if (json.status == "success") {
              let html = JSON.parse(json.html);
              $(".l2t-container #api_profiles").html(html.profiles_select);
              $(".l2t-container #api_locations").html(html.locations_select);
              $(".l2t-container #api_teams").html(html.teams_select);
              $("#l2t-api-key").val(json.api_key);
              $(".l2t-container .defult_confi_form").removeClass("d_none");
              //$(".chosen-select").chosen();
              $(".l2t-container .chosen-select").trigger("chosen:updated");
              l2t_display_invalid_error(json);
            } else if (json.status == "error") {
              $(".l2t-container #error_main_sec").removeClass("d_none");
              $(".l2t-container .defult_confi_form").addClass("d_none");
            } else {
              $(".l2t-container #ajax_response").text(json.message);
            }
            //get_configuration_details(private_api);
          } else {
            if (event == "validate") {
              $(".l2t-container .validation_check .loading_img").addClass("d_none");
              $("#l2t-api-button").prop("disabled", false);
            }
            $(".l2t-container .configuration_overlay")
              .removeClass("position_overlay")
              .addClass("d_none");
            $(".l2t-container .default_configuration_sec").removeClass("d_none");
            $(".l2t-container .chosen-select").chosen();
          }
        },
        complete: function (res) {
          if (event == "validate") {
            $(".l2t-container .validation_check .loading_img").addClass("d_none");
            $("#l2t-api-button").prop("disabled", false);
          }
          $(".l2t-container .configuration_overlay")
            .removeClass("position_overlay")
            .addClass("d_none");
          if(JSON.parse(res.responseText).status!=="error"){

              $(".l2t-container .default_configuration_sec").removeClass("d_none");
              $(".l2t-container .chosen-select").chosen();
              l2t_filter_toggle();
              let all_filters = l2t_admin_ajax_object.all_filter_labels;
              all_filters.forEach((element) => {
                highlight_invalid_filters("api_" + element);
              });
          }
        },
      });
    } else if (ajax_action !== "") {
      l2t_invalid_private_key();
    }
  }



  function get_configuration_details(private_api) {
    let l2t_filter_type = document.getElementById("l2t-filter-type").value;
    let l2t_widget_hide = document.getElementById("l2t_widget_hide");
    let l2tFilter = document.querySelector(
      "input[name=l2tFilter]:checked"
    ).value;
    let l2t_post_id = "";
    let ajax_action = "l2t_get_configuration";

    if (
      l2t_filter_type == "meta" &&
      l2t_widget_hide &&
      l2t_widget_hide.checked === false
    ) {
      l2t_post_id = document.getElementById("l2t_post_id").value;
    }

    if (
      l2tFilter == "default" ||
      (l2t_widget_hide === "yes" && l2t_filter_type == "meta")
    ) {
      ajax_action = "";
    } else {
      ////$(".defult_confi_form").addClass("d_none");
    }

    if (private_api && ajax_action !== "") {
      $(".l2t-container .error_invalid_key").addClass("d_none");

      $.ajax({
        url: l2t_admin_ajax_object.ajax_url,
        type: "post",
        data: {
          action: ajax_action,
          private_api: private_api,
          post_id: l2t_post_id,
          l2tFilter: l2tFilter,
          nonce: l2t_admin_ajax_object.ajax_nonce,
        },
        beforeSend: function () {
          $(".l2t-container .configuration_overlay")
            .removeClass("d_none")
            .addClass("position_overlay");
        },
        success: function (res) {
          let json = JSON.parse(res);
          let html = JSON.parse(json.html);
          if (json.status == "success") {
          }
          $(".l2t-container #api_profiles").html(html.profiles_select);
          $(".l2t-container #api_locations").html(html.locations_select);
          $(".l2t-container #api_teams").html(html.teams_select);
          if (l2t_post_id != "" && l2tFilter != "filter") {
            //$(".search-choice-close").click();
          }
          $(".l2t-container .chosen-select").chosen().trigger("chosen:updated");
          $(".l2t-container .defult_confi_form").removeClass("d_none");
          l2t_display_invalid_error(json);
        },
        complete: function (res) {
          $(".l2t-container .validation_check .loading_img").addClass("d_none");
          $(".l2t-container .configuration_overlay")
            .removeClass("position_overlay")
            .addClass("d_none");
          $("#l2t-api-button").prop("disabled", false);
          $(".l2t-container .default_configuration_sec").removeClass("d_none");
          $(".l2t-container .chosen-select").chosen();
          let all_filters = l2t_admin_ajax_object.all_filter_labels;
          all_filters.forEach((element) => {
            highlight_invalid_filters("api_" + element);
          });
        },
      });
    }else{

      $(".l2t-container .error_invalid_key").removeClass("d_none");
    }
  }

  function highlight_invalid_filters(filterId) {
    let filter = document.getElementById(filterId);
    try {
      if (filter) {
        let children = filter.childNodes;
        [...children].forEach(function (item) {
          if (item.classList.contains("l2t_invalid_filter")) {
            let chosenId = document.querySelector(
              "#" + filterId + "_chosen ul"
            );
            let chosenChild = chosenId.childNodes;
            [...chosenChild].forEach(function (choice) {
              if (choice.firstElementChild !== undefined) {
                if (item.text == choice.firstElementChild.innerHTML) {
                  //console.log(choice.firstElementChild.innerHTML);
                  choice.classList.add("l2t_invalid_filter");
                }
              }
            });
          }
        });
      }
    } catch (error) {
      console.error(error);
    }
  }

  function l2t_display_invalid_error(json) {
    let error_invalid_filter = document.getElementById("error_invalid_filter");

    if (parseInt(json.invalid_filters) > 0) {
      if (error_invalid_filter !== undefined) {
        error_invalid_filter.classList.remove("d_none");
      }
    } else {
      if (error_invalid_filter !== undefined) {
        error_invalid_filter.classList.add("d_none");
      }
    }
  }

  function l2t_invalid_private_key() {
      $(".l2t-container #error_main_sec").removeClass("d_none");
      $(".l2t-container .defult_confi_form").addClass("d_none");
      document.getElementById('l2t-api-key').value = '';
      document.querySelector('.l2t-container .default_configuration_sec').classList.add('d_none');
  }

  let l2tConfigForm = document.getElementById("l2tConfigForm");
  let lead2team_metabox_id = document.getElementById("lead2team_metabox_id");
  if (l2tConfigForm || lead2team_metabox_id) {
    l2t_filter_toggle();
    l2t_metabox_widget_toggle();
  }
})(jQuery);
