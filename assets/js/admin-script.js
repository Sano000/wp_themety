var $jq = jQuery.noConflict();

(function( $ ){
  
  var admin_app = {
    init: function() {
      
      this.uploadImages();
      
    },
    
    /**
     * Bind upload image events
     */
    uploadImages: function() {
      var file_frame;
      $( document ).on('click', '.widgets-upload-image .add', function(e) {
        var $container = $( e.target ).closest('.widgets-upload-image');
        var $preview = $container.find( '.preview' );
        var $template = $container.find( '.preview-item.template' );
        var multi = $container.data('multi') > 0 ? true : false;

        e.preventDefault();
        if (file_frame) {
          file_frame.open();
          return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
          title: $container.data('uploader_title'),
          button: {
            text: $container.data('uploader_button_text'),
          },
          multiple: multi,
        });

        file_frame.on('select', function() {
          var selection = file_frame.state().get('selection');
          selection.map( function( attachment ) {
            attachment = attachment.toJSON();
            var $item = $template.clone();
            multi || $preview.html('');

            $item.find('input').val( attachment.id );
            $item.find('.preview-img').css({'background-image': 'url(' + attachment.url + ')'});
            $item.find('.preview-img').attr('title', attachment.alt);
            $item.removeClass('hidden').removeClass('template').appendTo( $preview );
          });
          file_frame = null;
        });

        file_frame.open();
      });

      $( document ).on('click', '.widgets-upload-image .remove', function( e ) {
        e.preventDefault();
        var $container = $( e.target ).closest('.meta-upload-image');
        var $item = $( e.target ).closest( '.preview-item' );
        $item.slideUp().remove();
      });

      var initUploadSortable = function( $el ) {
        $el.sortable({
          containment: 'parent',
          cursor: 'move',
          handle: '.preview-img',
        });
      }
      initUploadSortable( $( '.widgets-upload-image[data-multi=1] .preview' ) );

      /* Need to do correcrtly !!! */
      $( document ).on( 'click', '#widgets-right .widget-control-save', function( e ){
        var $container = $(e.target).closest('.widgets-holder-wrap');
        var interval = setInterval( function() {
          if( !$container.find( '.widget-control-actions .spinner' ).is(':visible') ) {
            if( $container.find( '.widgets-upload-image[data-multi=1]' ).length ) {
              initUploadSortable( $container.find( '.widgets-upload-image[data-multi=1] .preview' ));
            }
            clearInterval( interval );
          }
        }, 200);
      });

    },
    
    
  }
  
  /**
   * Run application script
   */
  $( document ).ready( function() {
    admin_app.init();
  });
})( $jq );


