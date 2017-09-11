$(document).ready(function () {

    $(document).on('pjax:complete', function () {
        KTree.removeLoader();
    });

    var fileInfoContainer = $("#fileinfo");


    var ajaxRequest = null,
        strictThumb = $(window.frameElement).parents('[role="filemanager-modal"]').attr("data-thumb");


    $(document).on("click", ".create_folder,.add-media-root", function (e) {
      e.preventDefault();
      url = $(this).attr('href');
      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function () {
              KTree.addLoader();
          },
          success: function (data) {
              KTree.removeLoader();
              var modal = $('#global-modal');
              modal.modal('show').find('#modalHeader').html('<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>Create Folder');
              if (modal.is(":visible")) {
                  modal.find('#modalContent').html(data);
              } else {
                  modal.modal('show').find('#modalContent').html(data);
              }
          }
      });
    });

    $(document).on("click", '[role="mediafile-embed-video"]', function (e) {
      e.preventDefault();
      url = $(this).attr('href');
      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function () {
              KTree.addLoader();
          },
          success: function (data) {
              KTree.removeLoader();
              var modal = $('#global-modal');
              modal.modal('show').find('#modalHeader').html('<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>Create Embed Video');
              if (modal.is(":visible")) {
                  modal.find('#modalContent').html(data);
              } else {
                  modal.modal('show').find('#modalContent').html(data);
              }
          }
      });
    });

    $(document).on("click", '[href="#mediafile"]', function (e) {
        e.preventDefault();

        if (ajaxRequest) {
            ajaxRequest.abort();
            ajaxRequest = null;
        }

        $(".item a").removeClass("active");
        $(this).addClass("active");
        var id = $(this).attr("data-key"),
            url = $("#filemanager").attr("data-url-info"),
            name = $(this).attr('data-name');

        ajaxRequest = $.ajax({
            type: "GET",
            url: url,
            data: "id=" + id + "&strictThumb=" + strictThumb,
            beforeSend: function () {
                KTree.addLoader();
            },
            success: function (data) {
                KTree.removeLoader();
                var modal = $('#global-modal');
                modal.modal('show').find('#modalHeader').html('<button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>Edit '+name);
                if (modal.is(":visible")) {
                    modal.find('#modalContent').html(data);
                } else {
                    modal.modal('show').find('#modalContent').html(data);
                }
            }
        });
    });

    $(document).on("click", '[role="mediafile-delete"]', function (event) {

        event.preventDefault();
        var url = $(this).attr("href"),
            id = $(this).attr("data-id"),

            type = $(this).attr("type");
        if(type == 'embed'){
          var confirmMessage = 'Are you sure you want to delete this video?';
        }else if(type == 'folder'){
          var confirmMessage = 'Are you sure you want to delete this folder, If deleted this folder related images and child folders are also deleted?';
        }else{
          var confirmMessage = 'Are you sure you want to delete this image?';
        }
        var base_url = window.location.origin;
        $.ajax({
            type: "POST",
            url: url,
            data: "id=" + id,
            beforeSend: function () {
                if (!confirm(confirmMessage)) {
                    return false;
                }
            },
            success: function (json) {
				$("#w6-success").remove();
		        $("#w6-error").remove();
                if (json.success) {
                    var modal = $('#global-modal');
                    modal.modal('hide');
                    $('.modal-backdrop').remove();
                    //$('[data-key="' + id + '"]').fadeOut();
                    $.pjax.defaults.timeout = false;
                    $.pjax.reload({
                       container: '#pjax-grid-filtering',
                       url: base_url+"/filemanager/file/filemanager-view?parent=" + $("#filemanager_parent").val()+"&popup="+$(".popup-visible").val()
                    });
                    $('#jsTree_mediafile-filename').jstree(true).settings.core.data = json.jstreeData;
                    $('#jsTree_mediafile-filename').jstree(true).refresh();
                    $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>"+json.filename+" successfully deleted</div>");
                }
            },
            error: function(data){
			  $("#w6-success").remove();
		      $("#w6-error").remove();
              $('body').append("<div id='w6-error' class='alert-error alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>Failed to delete the record</div>");
            }
        });
    });

	$(document).on("click", '[role="mediafile-folder-delete"]', function (event) {

        event.preventDefault();
        var url = $(this).attr("href"),
            id = $(this).attr("data-id"),
            type = $(this).attr("type");
        var confirmMessage = 'Are you sure you want to delete this folder, If deleted this folder related images and child folders are also deleted?';
        var base_url = window.location.origin;
        $.ajax({
            type: "POST",
            url: url,
            data: "id=" + id,
            beforeSend: function () {
                if (!confirm(confirmMessage)) {
                    return false;
                }
            },
            success: function (json) {
                if (json.success) {
                    var modal = $('#global-modal');
                    modal.modal('hide');
                    $('.modal-backdrop').remove();
					window.location.href = base_url+"/filemanager/file/filemanager-view?parent=0"+"&popup="+$(".popup-visible").val();
                }
            },
            error: function(data){
			  $("#w6-success").remove();
		      $("#w6-error").remove();
              $('body').append("<div id='w6-error' class='alert-error alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>Failed to delete the record</div>");
            }
        });
    });

    $(document).on("submit", "#control-form", function (e) {
        e.preventDefault();

        var url = $(this).attr("action"),
            data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            beforeSend: function () {
                KTree.addLoader();
            },
            success: function (html) {
              KTree.removeLoader();
              var modal = $('#global-modal');
              modal.modal('hide');
              $('.modal-backdrop').remove();
				$("#w6-success").remove();
		  		$("#w6-error").remove();
              if(html.success){
                $.pjax.defaults.timeout = false;
                $.pjax.reload({
                   container: '#pjax-grid-filtering'
                });
                $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>"+html.filename+" successfully updated</div>");
              }else if(html.error){
                $('body').append("<div id='w6-error' class='alert-danger alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-ban'></i>"+html.filename+" failed to update</div>");
              }
            }
        });
    });

    var DELAY = 300, clicks = 0, timer = null;
    $(document).on('click', '[href="#mediafolder"]', function (e) {
        var updateUrl = $(this).attr('updateUrl');
        clicks++;  //count clicks
        if (clicks === 1) {
            timer = setTimeout(function () {
                $.ajax({
                    type: "GET",
                    url: updateUrl,
                    beforeSend: function () {
                        KTree.addLoader();
                    },
                    success: function (html) {
                        KTree.removeLoader();
                        $("#fileinfo").html(html);
                    }
                });
                clicks = 0;             //after action performed, reset counter

            }, DELAY);
        } else {

            clearTimeout(timer);    //prevent single-click action
            clicks = 0;             //after action performed, reset counter
        }

    })
        .on('dblclick', '[href="#mediafolder"]', function (e) {
            var url = $(this).attr('url');
            window.location.href = url;
        });

    $(document).on("submit", "#updateFolder-form", function (e) {
        e.preventDefault();
        var id = $(this).attr('id');
        var url = $(this).attr("action"),
            data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            beforeSend: function () {
                KTree.addLoader();
            },
            success: function (html) {
                KTree.removeLoader();
                $("#fileinfo").html(html);
                var folder_name = $(".folder_filename").val();
                var folder_id = $(".folder_id").val();
                $('#folderName' + folder_id).html(folder_name);
            }
        });
    });

    $(document).on('dblclick', '[href="#media_folder"]', function (e) {
        var url = $(this).attr('url');
        window.location.href = url;
    });

    $(document).on('change', '.mediafile-checkbox', function () {
          var imageId = $(this).val();
          var checked = $(this).is(':checked');
          updateData(imageId, checked);

    });

    function updateData(imageId, checked) {
      var selectedIds = $('.images_selected').val();
      var imageIds = (selectedIds != '')?selectedIds.split(','):[];
      if (checked) {
        imageIds.push(imageId);
      } else {
        for (var i = imageIds.length; i--;) {
          if (imageIds[i] === imageId) {
            imageIds.splice(i, 1);
          }
        }
      }
      selectedIds = imageIds.join();
      $('.images_selected').val(selectedIds);
    }

    $(document).on('change', '.filemanager-grid .select-on-check-all', function () {
        var globalSelectedValue = $(this).is(':checked');
        $('#filemanager .mediafile-checkbox').each(function () {
            $(this).prop("checked", globalSelectedValue);
            var imageId = $(this).val();
            var checked = $(this).is(':checked');
            updateData(imageId, checked);

        });
    });

    $(document).on("click", '[role="mediafile-mass-delete"]', function (event) {
        event.preventDefault();
        var base_url = window.location.origin;
        var selectedImageIds = $('.images_selected').val();
        if(selectedImageIds == ''){
          alert("Please select one or more items from the list");
        }else {
          var url = $(this).attr("href"),
              id = selectedImageIds,
              confirmMessage = 'Are you sure you want to delete this item?';
		  $("#w6-success").remove();
		  $("#w6-error").remove();
          $.ajax({
              type: "POST",
              url: url,
              data: {id:id},
              beforeSend: function () {
                  if (!confirm(confirmMessage)) {
                      return false;
                  }
                  $("#fileinfo").html('<div class="loading"><span class="glyphicon glyphicon-refresh spin"></span></div>');
              },
              success: function (json) {
                  if (json.success) {
                      $.pjax.defaults.timeout = false;
                      $.pjax.reload({
                         container: '#pjax-grid-filtering',
                         url: base_url+"/filemanager/file/filemanager-view?parent=" + $("#filemanager_parent").val()+"&popup="+$(".popup-visible").val()
                      });
                      $('.images_selected').val('');
                      $('#jsTree_mediafile-filename').jstree(true).settings.core.data = json.jstreeData;
                      $('#jsTree_mediafile-filename').jstree(true).refresh();
                      $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>Successfully Deleted</div>");
                  }
              },
              error : function(response){
                $('body').append("<div id='w6-error' class='alert-danger alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-ban'></i>Failed to delete the record</div>");
              }
          });
        }

    });

    $(document).on("click", '[role="mediafile-folder"]', function (event) {
        event.preventDefault();
        var base_url = window.location.origin;
        var url = $(this).attr("href"),
            parent = $(this).attr("data-id"),
            popup = $(".popup-visible").val(),
            type = $(this).attr("type");
        $.pjax.defaults.timeout = false;
        $.pjax.reload({
               container: '#pjax-grid-filtering',
               url: url+"&popup="+popup
        });
          $('#jsTree_mediafile-filename').jstree('deselect_all');
          $('#jsTree_mediafile-filename').jstree('select_node',parent);
          $('#jsTree_mediafile-filename').jstree(true).refresh();
          $('.page-load-id').val(parent);
          $("#filemanager_parent").val(parent);
          $("#fileupload-parent").val(parent);
          $('#mediafile-file-fileupload').fileupload('option', 'url', base_url+'/filemanager/file/upload?parent='+parent+'&popup='+popup);
          $('.create_folder').attr('href',base_url+'/filemanager/file/create-folder?parent='+parent);
          $(".uploaderManager").attr('href',base_url+'/filemanager/file/uploadmanager?parent='+parent);
          $('a[role=mediafile-embed-video]').attr('href',base_url+'/filemanager/file/save-embed-video?parent='+parent);
    });


});
