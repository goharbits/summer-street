<script type="text/javascript">
  "use strict";;
  (function($, window, document) {
    $("[data-link]").hide(); // hide the ajax functional button untill the page load completely

    $(document).ready(function() {
      $.ajaxSetup({
        cache: false,
        headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token(), false); ?>"
        }
      });

      initAppPlugins();

      // Activate the tab if the url has any #hash
      $('.nav a').on('show.bs.tab', function(e) {
        window.location = $(this).attr('href');
      });
      $(function() {
        var hash = window.location.hash;
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');
      });

      // Confirmation for actions
      $('body').on('click', '.confirm', function(e) {
        e.preventDefault();

        var form = this.closest("form");
        var url = $(this).attr("href");

        var msg = $(this).data('confirm');

        if (!msg) {
          msg = "<?php echo e(trans('theme.notify.are_you_sure'), false); ?>";
        }

        $.confirm({
          title: "<?php echo e(trans('theme.confirmation'), false); ?>",
          content: msg,
          type: 'red',
          icon: 'fas fa-question-circle',
          class: 'flat',
          animation: 'scale',
          closeAnimation: 'scale',
          opacity: 0.5,
          buttons: {
            'confirm': {
              text: '<?php echo e(trans('theme.button.proceed'), false); ?>',
              keys: ['enter'],
              btnClass: 'btn-primary flat',
              action: function() {
                //Disable mouse pointer events and set wait cursor
                // $('body').css("pointer-events", "none");
                $('body').css("cursor", "wait");

                if (typeof url != 'undefined') {
                  location.href = url;
                } else if (form != null) {
                  form.submit();
                  <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.confirmed'), 'type' => 'success', 'icon' => 'check-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                }

                return true;
              }
            },
            'cancel': {
              text: '<?php echo e(trans('theme.button.cancel'), false); ?>',
              btnClass: 'btn-default flat',
              action: function() {
                <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.canceled'), 'type' => 'warning', 'icon' => 'times-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
              }
            },
          }
        });
      });

      // Product Seach
      $('#autoSearchInput').on('keyup', function(e) {
        var showResult = $("#autoSearchResult");
        var query = $(this).val();
        if (query.length < 3) {
          showResult.html('<li><?php echo e(trans('theme.type_min_char', ['min' => 3]), false); ?></li>');
          return;
        }

        showResult.html('<li><?php echo e(trans('theme.searching'), false); ?></li>');

        $.ajax({
          data: "q=" + query,
          url: "<?php echo e(route('search.autocomplete'), false); ?>",
          success: function(results) {
            showResult.html(results);
          }
        });
      });
      //End product Seach

      // Item Quick View Modal
      $(".itemQuickView").on("click", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');

        // Disable the modal on small screen
        var width = $(window).width();
        if (width < 830) {
          window.location.href = url.replace("/quickView", "");
          return false;
        }

        apply_busy_filter('body');

        $.get(url, function(data) {
          remove_busy_filter('body');

          $('#quickViewModal').html(data).modal();

          //Initialize application plugins after ajax load the content
          if (typeof initAppPlugins == 'function') {
            initAppPlugins();
          }
        });
      });

      // Dynamic MODAL
      $(".modalAction").on("click", function(e) {
        e.preventDefault();
        apply_busy_filter('body');

        var url = $(this).attr('href');
        $.get(url, function(data) {
          remove_busy_filter('body');
          $('#myDynamicModal').html(data).modal();

          //Initialize application plugins after ajax load the content
          if (typeof initAppPlugins == 'function') {
            initAppPlugins();
          }
        });
      });

      // Language changes
      var langRoute = '<?php echo e(route('locale.change', '_lang_'), false); ?>';
      $('select#languageChange').ddslick({
        width: "100%",
        imagePosition: "left",
        selectText: "<?php echo e(trans('theme::lang.language_options'), false); ?>",
        onSelected: function(data) {
          var lang = data.selectedData.value;
          if (lang != '<?php echo e(\App::getLocale(), false); ?>') {
            window.location.href = langRoute.replace("_lang_", lang);
          }
        }
      });

      // Currency changes
      $('select#currencyChange').ddslick({
        width: "100%",
        imagePosition: "left",
        selectText: "Select",
        onSelected: function(data) {
          var currency = data.selectedData.value;
          $("#selected").html(data.selectedData.value);
          console.log(currency);
        }
      });

      // Currency changes Mobile
      $("#mobile-curency").ddslick({
        width: "100%",
        imagePosition: "left",
        selectText: "Mobile Currency",
        onSelected: function(data) {
          $("#selected").html(data.selectedData.value);
        }
      });

      // Language changes mobile
      $("select#mobile-lang").ddslick({
        width: "100%",
        imagePosition: "left",
        selectText: "<?php echo e(trans('theme::lang.language_options'), false); ?>",
        onSelected: function(data) {
          var lang = data.selectedData.value;
          if (lang != '<?php echo e(\App::getLocale(), false); ?>') {
            window.location.href = langRoute.replace("_lang_", lang);
          }
        }
      });

      // Mobile mega menu
      $('.main-menu').mobileMegaMenu({
        changeToggleText: false,
        enableWidgetRegion: true,
        prependCloseButton: true,
        stayOnActive: true,
        // toogleTextOnClose: 'Close Menu',
        menuToggle: 'main-menu-toggle'
      });

      // Remmove the unwanted li that pushed via mobileMegaMenu plugin
      $("#mobile-lang ul li a.close-button").closest('li').remove();
    });

    //App plugins
    function initAppPlugins() {
      //Initialize validator
      $('#form, form[data-toggle="validator"]').validator({
        disable: false,
      });

      $('.sc-add-to-cart').removeAttr('href').css('cursor', 'pointer').show();

      // Add-to-cart
      $(".sc-add-to-cart").off().on("click", function(e) {
        e.preventDefault();
        var item = $(this).closest('.sc-product-item');
        var qtt = item.find('input.product-info-qty-input').val();
        var shipTo = item.find('#shipTo').data('country');
        var shippingZoneId = item.find('input#shipping-zone-id').val();
        var shippingRateId = item.find('input#shipping-rate-id').val();
        var shipToCountryId = item.find('input#shipto-country-id').val();
        var shipToStateId = item.find('input#shipto-state-id').val();

        $.ajax({
          url: $(this).data('link'),
          type: 'POST',
          data: {
            'shipTo': shipTo,
            'shippingZoneId': shippingZoneId,
            'shippingRateId': shippingRateId,
            'shipToCountryId': shipToCountryId,
            'shipToStateId': shipToStateId,
            'quantity': qtt ? qtt : 1
          },
          complete: function(xhr, textStatus) {
            if (200 == xhr.status) {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.item_added_to_cart'), 'type' => 'success', 'icon' => 'check-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
              // Increase global cart item count by 1
              increaseCartItem(1);
            } else if (404 == xhr.status) {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.item_not_available'), 'type' => 'warning', 'icon' => 'info-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            } else if (444 == xhr.status) {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.item_added_already_in_cart'), 'type' => 'info', 'icon' => 'info-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            } else {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.failed'), 'type' => 'warning', 'icon' => 'times-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            }
          },
        });
      });

      // Add-to-wishlist
      $(".add-to-wishlist").off().on("click", function(e) {
        e.preventDefault();

        $.ajax({
          url: $(this).data('link'),
          type: 'get',
          complete: function(xhr, textStatus) {
            if (200 == xhr.status) {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.item_added_to_wishlist'), 'type' => 'success', 'icon' => 'check-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            } else if (401 == xhr.status) {
              location.href = '<?php echo e(route('customer.login'), false); ?>';
            } else if (404 == xhr.status) {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.item_not_available'), 'type' => 'warning', 'icon' => 'info-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            } else {
              <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.failed'), 'type' => 'warning', 'icon' => 'times-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            }
          },
        });
      });

      // Bootstrap fixes
      $('[data-toggle="tooltip"]').tooltip();

      // i-Check plugin
      $('.i-check, .i-radio').iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal',
      });
      $('.i-check-blue, .i-radio-blue').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
      });

      // SelectBoxIt
      $(".selectBoxIt").selectBoxIt();

      // jqzoom
      $('#jqzoom, #quickViewZoom').jqzoom({
        zoomType: 'standard',
        lens: true,
        preloadImages: false,
        alwaysOn: false,
        showEffect: 'fadein',
        hideEffect: 'fadeout',
        fadeinSpeed: 'fast',
        fadeoutSpeed: 'fast',
        showPreload: true,
        zoomWidth: 530,
        zoomHeight: 530,
        xOffset: 10,
        yOffset: 0
      });

      //Datepicker
      $(".datepicker").datepicker({
        format: 'yyyy-mm-dd'
      });

      // Product qty field
      $(".product-info-qty-input").on('keyup', function() {
        var currentVal = parseInt($(this).val(), 10);
        var maxVal = parseInt($(this).data('max'), 10);

        if (!currentVal || currentVal == "" || currentVal == "NaN") {
          currentVal = 1;
        } else if (maxVal < currentVal) {
          currentVal = maxVal;
        }

        $(this).val(currentVal);
      });

      $(".product-info-qty-plus").on('click', function(e) {
        e.preventDefault();
        var node = $(this).prev(".product-info-qty-input");
        var currentVal = parseInt(node.val(), 10);

        if (!currentVal || currentVal == "" || currentVal == "NaN") {
          currentVal = 0;
        }

        if (node.data('max') > currentVal) {
          node.val(currentVal + 1).change();
        } else {
          <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.max_item_stock'), 'type' => 'warning', 'icon' => 'times-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        }
      });

      $(".product-info-qty-minus").on('click', function(e) {
        e.preventDefault();
        var node = $(this).next(".product-info-qty-input");
        var currentVal = parseInt(node.val(), 10);

        if (currentVal == "NaN") {
          currentVal = node.data('min');
        }

        if (currentVal > node.data('min')) {
          $(this).next(".product-info-qty-input").val(currentVal - 1).change();
        } else {
          <?php echo $__env->make('theme::layouts.notification', ['message' => trans('theme.notify.minimum_order_qtt_reached'), 'type' => 'warning', 'icon' => 'times-circle'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        }
      });
      // END Product qty field

      // Address form
      $("#address_country").on('change', function() {
        var country = $(this).val();
        var state_node = $('#address_state');

        $.ajax({
          delay: 250,
          data: "id=" + country,
          url: "<?php echo e(route('ajax.getCountryStates'), false); ?>",
          success: function(result) {
            var data = '<option value=""><?php echo e(trans('theme.placeholder.state'), false); ?></option>';
            if (result.length !== 0) {
              data += $.map(result, function(val, id) {
                return '<option value="' + id + '">' + val + '</option>';
              })

              state_node.attr('required', 'required');
            } else {
              state_node.removeAttr('required');
            }

            state_node.html(data);

            state_node.trigger('change'); // Trigger the onchange event on state id
          }
        });
      });
      // END Address form
    }

    // Price Filters
    $("#price-slider").ionRangeSlider({
      hide_min_max: true,
      keyboard: true,
      min: <?php echo e($priceRange['min'] ?? 0, false); ?>,
      max: <?php echo e($priceRange['max'] ?? 5000, false); ?>,
      from: <?php echo e(Request::get('price') ? explode('-', Request::get('price'))[0] : $priceRange['min'] ?? 0, false); ?>,
      to: <?php echo e(Request::get('price') ? explode('-', Request::get('price'))[1] : $priceRange['max'] ?? 5000, false); ?>,
      step: 10,
      type: 'double',
      prefix: "<?php echo e(get_formated_currency_symbol() ?? '$', false); ?>",
      grid: true,
      onFinish: function(data) {
        var href = removeQueryStringParameter(window.location.href, 'price'); //Remove currect price
        window.location.href = getFormatedUrlStr(href, 'price=' + data.from + '-' + data.to);
      },
    });
  }(window.jQuery, window, document));

  // Helpers
  function getFormatedValue(value = 0, dec = <?php echo e(config('system_settings.decimals', 2), false); ?>) {
    value = value ? value : 0;
    return parseFloat(value).toFixed(dec);
  }

  function getFormatedPrice(value = 0, trim = true) {
    var value = getFormatedValue(value);
    var arr = value.split(".");

    if (arr[1]) {
      value = arr[1] > 0 ? arr[0] + '<sup class="price-fractional">' + arr[1] + '</sup>' : arr[0];
    }

    return "<?php echo e(get_currency_prefix(), false); ?>" + value + "<?php echo e(get_currency_suffix(), false); ?>";
  }

  /*
   * Get result from PHP helper functions
   *
   * @param    {str} funcName The PHP function name will be called
   * @param    {mix} args arguments need to pass into the PHP function
   *
   * @return  {mix}
   */
  function getFromPHPHelper(funcName, args = null) {
    var url = "<?php echo e(route('helper.getFromPHPHelper'), false); ?>";
    var result = 0;
    jQuery.ajax({
      url: url,
      data: "funcName=" + funcName + "&args=" + args,
      async: false,
      success: function(v) {
        result = v;
      }
    });

    return result;
  }
</script>
<?php /**PATH H:\xampp\htdocs\summerstreet\public\themes\default/views/scripts/appjs.blade.php ENDPATH**/ ?>