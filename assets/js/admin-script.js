(function() {
  var $jq;

  $jq = jQuery.noConflict();

  (function($) {
    var admin_app;
    admin_app = {
      init: function() {
        this.uploadImages();
        this.handleMultifields();
        this.updateMetaboxArea();
      },

      /*
      Bind upload image events
       */
      uploadImages: function() {
        var file_frame, initUploadSortable;
        file_frame = null;
        $(document).on('click', '.widgets-upload-image .add', function(e) {
          var $container, $preview, $template, multi, _ref;
          $container = $(e.target).closest('.widgets-upload-image');
          $preview = $container.find('.preview');
          $template = $container.find('.preview-item.template');
          multi = (_ref = $container.data('multi') > 0) != null ? _ref : {
            "true": false
          };
          e.preventDefault();
          if (file_frame) {
            file_frame.open();
            return;
          }
          file_frame = wp.media.frames.file_frame = wp.media({
            title: $container.data('uploader_title'),
            button: {
              text: $container.data('uploader_button_text')
            },
            multiple: multi
          });
          file_frame.on('select', function() {
            var selection;
            selection = file_frame.state().get('selection');
            selection.map(function(attachment) {
              var $item;
              attachment = attachment.toJSON();
              $item = $template.clone();
              multi || $preview.html('');
              $item.find('input').val(attachment.id);
              $item.find('.preview-img').css({
                'background-image': 'url(' + attachment.url + ')'
              });
              $item.find('.preview-img').attr('title', attachment.alt);
              $item.removeClass('hidden').removeClass('template');
              $item.appendTo($preview);
            });
            file_frame = null;
          });
          file_frame.open();
        });
        $(document).on('click', '.widgets-upload-image .remove', function(e) {
          var $container, $item;
          e.preventDefault();
          $container = $(e.target).closest('.meta-upload-image');
          $item = $(e.target).closest('.preview-item');
          $item.slideUp().remove();
        });
        initUploadSortable = function($el) {
          $el.sortable({
            containment: 'parent',
            cursor: 'move',
            handle: '.preview-img'
          });
        };
        initUploadSortable($('.widgets-upload-image[data-multi=1] .preview'));
        $(document).on('click', '#widgets-right .widget-control-save', function(e) {
          var $container, interval;
          $container = $(e.target).closest('.widgets-holder-wrap');
          interval = setInterval(function() {
            if (!$container.find('.widget-control-actions .spinner').is(':visible')) {
              if ($container.find('.widgets-upload-image[data-multi=1]').length) {
                initUploadSortable($container.find('.widgets-upload-image[data-multi=1] .preview'));
              }
              clearInterval(interval);
            }
          }, 200);
        });
      },

      /*
      Handle collapsible, sortable multifields
       */
      handleMultifields: function() {
        var $multis;
        $multis = $('.js-wp_themety__meta-multi');
        $multis.each(function(n, el) {
          var $el, collapsible, sortable;
          $el = $(el);
          collapsible = $el.hasClass('collapsible');
          sortable = $el.hasClass('sortable');
          if (collapsible || sortable) {
            $el.find('.input-item').prepend(function(n) {
              return "<h3 class='ui-accordion-header'>Item " + n + "</h3>";
            });
          }
          if (collapsible) {
            $el.accordion({
              header: '> div > h3',
              active: 0
            });
            $el.accordion("option", "collapsible", true);
          }
          if (sortable) {
            $el.sortable({
              containment: 'parent',
              cursor: 'move'
            });
            $el.disableSelection();
          }
        });
      },
      disableMultifieldsHandle: function() {
        var $multis;
        $multis = $('.js-wp_themety__meta-multi');
        return $multis.each(function(n, el) {
          var $el;
          $el = $(el);
          if ($el.is('.iu-accordion')) {
            $el.accordion('disable');
          }
          if ($el.is('.iu-sortable')) {
            return $el.sortable('disable');
          }
        });
      },
      updateMetaboxArea: (function(_this) {
        return function() {
          $(document).on('change', '#page_template', function(e) {
            var pageTemplate, postId;
            pageTemplate = $(this).val();
            postId = $('#post_ID[name="post_ID"]:first').val();
            admin_app.disableMultifieldsHandle();
            return $.post(ajaxurl, {
              action: 'themety_metabox_update',
              post_id: postId,
              page_template: pageTemplate
            }, (function(_this) {
              return function(resp) {
                $('#postbox-container-2').html(resp.html);
                postboxes.add_postbox_toggles();
                return admin_app.handleMultifields();
              };
            })(this));
          });
        };
      })(this)
    };
    $(document).ready(function() {
      admin_app.init();
    });
  })($jq);

}).call(this);
