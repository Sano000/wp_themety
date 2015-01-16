$jq = jQuery.noConflict()

do ($ = $jq) ->

  admin_app = {
    init: ->
      @uploadImages()
      @handleMutlifields()
      return

    ###
    Bind upload image events
    ###
    uploadImages: ->
      file_frame = null

      $( document ).on 'click', '.widgets-upload-image .add', (e)->
        $container = $( e.target ).closest '.widgets-upload-image'
        $preview = $container.find '.preview'
        $template = $container.find '.preview-item.template'
        multi = $container.data('multi') > 0 ? true : false

        e.preventDefault()
        if (file_frame)
          file_frame.open()
          return

        file_frame = wp.media.frames.file_frame = wp.media
          title: $container.data('uploader_title')
          button:
            text: $container.data 'uploader_button_text'
          multiple: multi

        file_frame.on 'select',  ->
          selection = file_frame.state().get 'selection'
          selection.map  (attachment)->
            attachment = attachment.toJSON()
            $item = $template.clone()
            multi || $preview.html ''

            $item.find('input').val attachment.id
            $item.find('.preview-img').css
              'background-image':'url('+attachment.url+')'
            $item.find('.preview-img').attr 'title', attachment.alt
            $item.removeClass('hidden').removeClass('template')
            $item.appendTo $preview
            return

          file_frame = null
          return
        
        file_frame.open()
        return

      $( document ).on 'click', '.widgets-upload-image .remove', (e)->
        e.preventDefault()

        $container = $( e.target ).closest '.meta-upload-image'
        $item = $( e.target ).closest '.preview-item'
        $item.slideUp().remove()
        return

      initUploadSortable = ($el) ->
        $el.sortable
          containment: 'parent'
          cursor: 'move'
          handle: '.preview-img'
        return

      initUploadSortable $( '.widgets-upload-image[data-multi=1] .preview' )

      #need to do correctly!!!
      $( document ).on 'click', '#widgets-right .widget-control-save', (e) ->
        $container = $( e.target ).closest '.widgets-holder-wrap'
        interval = setInterval(->
          if !$container.find('.widget-control-actions .spinner').is ':visible'
            if $container.find( '.widgets-upload-image[data-multi=1]' ).length
              initUploadSortable $container.find(
                '.widgets-upload-image[data-multi=1] .preview')

            clearInterval interval
            return
        , 200)

        return
      return

    ###
    Handle collapsible, sortable multifields
    ###
    handleMutlifields: ->
      $multis = $('.js-wp_themety__meta-multi')
      $multis.each (e)->
        collapsible = $(this).hasClass('collapsible')
        sortable = $(this).hasClass('sortable')
        if collapsible || sortable
          $(this).find('.input-item').prepend (e) ->
            '<h3 class="ui-accordion-header">Item ' + e + '</h3>'

        if collapsible
          $(this).accordion
            header: '> div > h3',
            active: 0
          $(this).accordion "option", "collapsible", true

        if sortable
          $(this).sortable
            containment: 'parent',
            cursor: 'move'
          $(this).disableSelection()

        return

      return
  }

  $( document ).ready ->
    admin_app.init()
    return

  return
