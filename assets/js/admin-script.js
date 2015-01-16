(function() {
  var $jq;

  $jq = jQuery.noConflict();

  (function($) {
    var admin_app;
    admin_app = {
      init: function() {
        this.uploadImages();
        this.handleMutlifields();
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
      handleMutlifields: function() {
        var $multis;
        $multis = $('.js-wp_themety__meta-multi');
        $multis.each(function($el) {
          var collapsible, sortable;
          collapsible = $(this).hasClass('collapsible');
          sortable = $(this).hasClass('sortable');
          if (collapsible || sortable) {
            $(this).find('.input-item').prepend(function(n) {
              return "<h3 class='ui-accordion-header'>Item " + n + "</h3>";
            });
          }
          if (collapsible) {
            $(this).accordion({
              header: '> div > h3',
              active: 0
            });
            $(this).accordion("option", "collapsible", true);
          }
          if (sortable) {
            $(this).sortable({
              containment: 'parent',
              cursor: 'move'
            });
            $(this).disableSelection();
          }
        });
      }
    };
    $(document).ready(function() {
      admin_app.init();
    });
  })($jq);

}).call(this);
