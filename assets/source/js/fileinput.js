function filemanagerTinyMCE(callback, value, meta) {
    var inputId = tinymce.activeEditor.settings.id,
        modal = $('[data-btn-id="' + inputId + '-btn"]'),
        iframe = $('<iframe src="' + modal.attr("data-frame-src")
            + '" id="' + modal.attr("data-frame-id") + '" frameborder="0" role="filemanager-frame"></iframe>');

    iframe.on("load", function () {
        var modal = $(this).parents('[role="filemanager-modal"]'),
            input = $("#" + modal.attr("data-input-id"));

        $(this).contents().find(".dashboard").on("click", "#insert-btn", function (e) {
            e.preventDefault();

            var data = getFormData($(this).parents("#control-form"));

            input.trigger("fileInsert", [data]);

            callback(data.url, {alt: data.alt});
            modal.modal("hide");
            $('.modal-backdrop').remove();
        });
    });

    modal.find(".filemanager-modal-body").html(iframe);
    modal.modal("show");
}

function getFormData(form) {
    var formArray = form.serializeArray(),
        modelMap = {
            'Mediafile[alt]': 'alt',
            'Mediafile[description]': 'description',
            'Mediafile[type]': 'type',
            url: 'url',
            id: 'id'
        },
        data = [];

    for (var i = 0; formArray.length > i; i++) {
        if (modelMap[formArray[i].name]) {
            data[modelMap[formArray[i].name]] = formArray[i].value;
        }
    }
    return data;
}

$(document).ready(function () {

    function frameHandler(e) {
      var modal = $(this).parents('[role="filemanager-modal"]'),
          imageContainer = $(modal.attr("data-image-container")),
          pasteData = modal.attr("data-paste-data"),
          input = $("#" + modal.attr("data-input-id")),
          DisplayImage = modal.attr("data-displayimage-class"),
          displayGridView = modal.attr("data-displayGridView"),
          imageValidations = modal.attr("data-image-validation"),
          inputAttribute = modal.attr("data-input-attribute");
      KTree.removeLoader();
      var base_url = window.location.origin;
      var insertUrl = base_url + '/filemanager/file/mass-insert';

      if(modal.attr('data-multiple') == 1){
        $(this).contents().find('.global-buttons').append('<a href="'+insertUrl+'" id = "mass-insert-btn" class = "btn btn-primary">Insert</a>');
      }

      $(this).contents().on("click", "#insert-btn", function (e) {
          e.preventDefault();
          var largeThumb=$(this).attr('href')+"&thumb=large";
          $.ajax({
              type: "POST",
              url: $(this).attr('href')+"&thumb="+modal.attr("data-thumb"),
              success: function (json) {
                  data = json;
                  input.trigger("fileInsert", [data]);
                  var fileUrl = data.url;
                  var imageExtension = fileUrl.split('.').pop().toLowerCase();

                  if (imageValidations != '' && imageValidations != null) {
                      if (imageValidations.toLowerCase().indexOf(imageExtension) < 0) {
                          alert('Please select valid file/image format . ' + imageValidations);
                          return false;
                      }
                  }

                  if(displayGridView == true){
                      if(!($("table[id='grid-view-media-manager'] > tbody > tr:first-child").attr('data-key'))){
                          $("table[id='grid-view-media-manager'] > tbody > tr:first-child").remove();
                      }
                      $(".grid-view").show();
                      $.ajax({
                        url:base_url + "/filemanager/file/get-media-manager-data?id=" + data[pasteData],
                        data:{inputAttribute : inputAttribute},
                        success: function (response) {
                            $(".media_manager_grid_view tbody").append(response);
                            $('[data-key="' +data[pasteData]+ '"]').find('.featured_attachments input[type=checkbox]').attr('value',data[pasteData]);
                          },
                        });
                      }else{
                        if(data.type != "embed"){
                          if (imageContainer) {
                              imageContainer.html(data.thumbUrl);
                          }

                          if (DisplayImage){
                              $("." + DisplayImage).html(data.thumbUrl);
                              $('[role="clear-input"]').show();
                          }
                        }else{
                          if (DisplayImage){
                            $("." + DisplayImage).html(data.thumbUrl);
                            $('[role="clear-input"]').show();
                          }
                        }
                      }
                      if (modal.attr("data-hiddenimage-id")) {
                          hiddenInsert = $("." + modal.attr("data-hiddenimage-id"));
                          hiddenInsert.val(data[pasteData]);
                      }
                      else {
                          input.val(data[pasteData]);
                      }
                      if((typeof(tinyMCE)!=='undefined') && (tinyMCE.activeEditor) && modal.find('iframe').attr('class')=="editor-class"){
                        var thumbUrl = data.thumbUrl;
                        if(data.type == 'image/gif' || data.type == 'image/jpeg' || data.type == 'image/png'){
                          tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="'+data.largeThumbUrl+'"><img src="' + editorPath + data.dataThumbUrl + '" data-mce-src="'+ editorPath + data.dataThumbUrl +'"></a>');
                        }else if(data.type == 'embed'){
                          tinyMCE.activeEditor.execCommand('mceInsertContent', false, thumbUrl);
                        }else{
                          tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="' + editorPath + data.url + '" >"'+ editorPath + data.url +'"</a>');
                        }
                      }
                      modal.modal("hide");
                      $('.modal-backdrop').remove();

                }
          });
      });

      $(this).contents().on("click", "#mass-insert-btn", function (e) {
          e.preventDefault();
          var selectedImageIds = $(this).closest("div.btn-container").find("input[name='images_selected']").val();
          var url = $(this).attr("href"),
              id = selectedImageIds;
          $.ajax({
                type: "POST",
                url: url+"?thumb="+modal.attr("data-thumb"),
                data: {id:id},
                success: function (json) {
                    if (json) {
                      $.map(json, function(el) {
                        data=el;
                        input.trigger("fileInsert", [data]);
                        var fileUrl = data.url;
                        var imageExtension = fileUrl.split('.').pop().toLowerCase();

                        if (imageValidations != '' && imageValidations != null) {
                            if (imageValidations.toLowerCase().indexOf(imageExtension) < 0) {
                                alert('Please select valid file/image format . ' + imageValidations);
                                return false;
                            }
                        }

                        if(displayGridView == true){
                            $(".grid-view").show();

                            var mediaId = data[pasteData];
                            $.ajax({
                                url:base_url + "/filemanager/file/get-media-manager-data?id=" + data[pasteData],
                                data:{inputAttribute : inputAttribute},
                                success: function (response) {
                                  $(".media_manager_grid_view tbody").append(response);
                                  $('[data-key="' +mediaId+ '"]').find('.featured_attachments input[type=checkbox]').attr('value',mediaId);
                                },
                              });
                            }else{
                              if(data.type != "embed"){
                                if (imageContainer) {
                                    imageContainer.append(data.thumbUrl);
                                }

                                if (DisplayImage){
                                    $("." + DisplayImage).append(data.thumbUrl);
                                    $('[role="clear-input"]').show();
                                }
                              }else{
                                if (DisplayImage){
                                  $("." + DisplayImage).append(data.thumbUrl);
                                  $('[role="clear-input"]').show();
                                }
                              }
                            }
                            if (modal.attr("data-hiddenimage-id")) {
                                hiddenInsert = $("." + modal.attr("data-hiddenimage-id"));
                                hiddenInsert.val(data[pasteData]);
                            }
                            else {
                                input.val(data[pasteData]);
                            }
                            if((typeof(tinyMCE)!=='undefined') && (tinyMCE.activeEditor) && modal.find('iframe').attr('class')=="editor-class"){
                             var thumbUrl = data.thumbUrl;
                             if(data.type == 'image/gif' || data.type == 'image/jpeg' || data.type == 'image/png'){
                               tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="'+data.largeThumbUrl+'"><img src="' + editorPath + data.dataThumbUrl + '" data-mce-src="'+ editorPath + data.dataThumbUrl +'"></a>');
                             }else if(data.type == 'embed'){
                               tinyMCE.activeEditor.execCommand('mceInsertContent', false, thumbUrl);
                             }else{
                               tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href="' + editorPath + data.url + '" >"'+ editorPath + data.url +'"</a>');
                             }
                           }
                            modal.modal("hide");
                            $('.modal-backdrop').remove();
                      });
                    }
                }
            });
        });
    }

    $(document).on('click', '[role="filemanager-launch"],[role="editor-launch"]', function (e) {
         KTree.addLoader('Please wait..');
        e.preventDefault();
        if($(this).attr('role')=="filemanager-launch"){
          var iframeClass = "filemanager-class";
        } else {
          var iframeClass = "editor-class";
        }
        var modal = $('[data-btn-id="' + $(this).attr("id") + '"]'),
            iframe = $('<iframe src="' + modal.attr("data-frame-src")
                + '" id="' + modal.attr("data-frame-id") + '" frameborder="0" role="filemanager-frame" class='+iframeClass+'></iframe>');

        iframe.on("load", frameHandler);
        modal.find(".filemanager-modal-body").html(iframe);
        modal.modal("show");

    });

    $(document).on("click", '[role="clear-input"]', function (e) {
        e.preventDefault();

        $("." + $(this).attr("data-clear-hiddenelement-id")).val("");
        DisplayImage = $(this).attr("data-clear-display-image");
        $("." + DisplayImage).attr('src', "");
        $($(this).attr("data-image-container")).empty();
        $("." + DisplayImage).html('');
        $(".media-clear-input").hide();
    });

    $(document).on("click",".delete-media-manager",function(){
      var id = $(this).attr('data-rel');
      $('[data-key="' + id + '"]').remove();
    });

    $(document).on("click",".featured_attachments",function(){
      var count=0;
      $('.featured_attachments').each(function(){
        if($(this).find('label').hasClass('active')){
          count = count + 1;
        }
      });
      if(count > 1){
        $(this).find('label').removeClass('active');
        alert('Please select only one featured image');
      }else{
        if($(this).find('label').hasClass('active')){
          var id = $(this).find('input').val();
          $(".media_attachments_"+id).val('');
        }else{
          var id = $(this).find('input').val();
          $(".media_attachments_"+id).val(id);
        }
      }
    });

    $(document).on("click",'.upload-filemanager-close-btn',function(){
      var btn_id = $(this).attr('data-btn-id');
      var modal = $('[data-btn-id="' + btn_id + '"]');
      modal.modal("hide");
      $('.modal-backdrop').remove();
    });

});
