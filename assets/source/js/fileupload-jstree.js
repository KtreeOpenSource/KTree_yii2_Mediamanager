var DELAY = 300, clicks = 0, timer = null;
$(document).on('click', '.jstree-anchor', function (e) {
  var element = $(this);
  var parent = $(this).parent().attr('id');
    clicks++;  //count clicks
    if (clicks === 1) {
        timer = setTimeout(function () {
          var popup = $(".popup-visible").val();
          var base_url = window.location.origin;
          var url = base_url+"/filemanager/file/filemanager-view?parent="+parent+"&popup="+popup;

          var pjaxContainerId= '#'+'pjax-grid-filtering';
          $.pjax.reload(pjaxContainerId, {url: url, container: pjaxContainerId, push: false,timeout:false, replace: false});
          $('.page-load-id').val(parent);
          $("#filemanager_parent").val(parent);
          $("#fileupload-parent").val(parent);
          $('#mediafile-file-fileupload').fileupload('option', 'url', base_url+'/filemanager/file/upload?parent='+parent+'&popup='+popup);
          $('.create_folder').attr('href',base_url+'/filemanager/file/create-folder?parent='+parent);
          $(".uploaderManager").attr('href',base_url+'/filemanager/file/uploadmanager?parent='+parent);
          $('a[role=mediafile-embed-video]').attr('href',base_url+'/filemanager/file/save-embed-video?parent='+parent);
            clicks = 0;             //after action performed, reset counter

        }, DELAY);
    } else {

        clearTimeout(timer);    //prevent single-click action
        clicks = 0;             //after action performed, reset counter
    }

})
    .on('dblclick', '.jstree-anchor', function (e) {
      /** For renaming node **/
      var ref = $('#jsTree_mediafile-filename').jstree(true);
      sel = ref.get_selected();
      if(!sel.length) { return false; }
     	sel = sel[0];
     	ref.edit(sel);

    });

    $(document).on("change",".jstree-anchor",function(){
      var id = $(this).parent().attr('id');
      var base_url = window.location.origin;
      $('#jsTree_mediafile-filename').bind("rename_node.jstree",function (event, data) {
        var ref = $('#jsTree_mediafile-filename').jstree(true);
        sel = ref.get_selected();
        $.ajax({
          type: 'POST',
          url: base_url+'/filemanager/file/update-folder-data?id='+id+'&filename='+data.text,

          success: function (json) {
			$("#w6-success").remove();
		    $("#w6-error").remove();
            if(json.success){
                $(this).text(data.text);
                $.pjax.defaults.timeout = false;
                $.pjax.reload({
                   container: '#pjax-grid-filtering'
                });
                $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>"+data.text+" successfully updated</div>");
            }else if(json.error){
                $('body').append("<div id='w6-error' class='alert-danger alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-ban'></i>Failed to update the folder</div>");
            }
            $('li.jstree-node').each(function(){
              var id = $(this).attr('id');
                if(($(this).children('div[class=folder-extra-options]').length) == 0){
                  $('#'+id+' a[id='+id+'_anchor]').after('<div class="folder-extra-options" id="'+id+'"><span class="glyphicon glyphicon-option-horizontal"></span><ul style="display:none;" id="'+id+'_folder_options" class="folder-extra-options-content"><li><a data-id="'+id+'" href="'+base_url+'/filemanager/file/delete?id='+id+'" title="delete" role="mediafile-folder-delete" type="folder" data-pjax="0">Delete</a></li></ul></div>');
                }
            });
          }
        });
      });
    });



$('#jsTree_mediafile-filename').bind('ready.jstree', function(event, data) {
      var base_url = window.location.origin;
    dragAndDrop();
    $('li.jstree-node').each(function(){
      var id = $(this).attr('id');
        if(($(this).children('div[class=folder-extra-options]').length) == 0){
          $('#'+id+' a[id='+id+'_anchor]').after('<div class="folder-extra-options" id="'+id+'"><span class="glyphicon glyphicon-option-horizontal"></span><ul style="display:none;" id="'+id+'_folder_options" class="folder-extra-options-content"><li><a data-id="'+id+'" href="'+base_url+'/filemanager/file/delete?id='+id+'" title="delete" role="mediafile-folder-delete" type="folder" data-pjax="0">Delete</a></li></ul></div>');
        }
    });
    data.instance._open_to($('.page-load-id').val());
    $("#" + $('.page-load-id').val()).find('a[id='+$('.page-load-id').val()+'_anchor]').trigger('click');
});

$('#jsTree_mediafile-filename').bind("open_node.jstree", function (e, data) {
  var base_url = window.location.origin;
  var info = data.node;
  $(info.children).each(function(key,value){
    if(($('#'+value).children('div[class=folder-extra-options]').length) == 0){
      $('#'+value+' a[id='+value+'_anchor]').after('<div class="folder-extra-options" id="'+value+'"><span class="glyphicon glyphicon-option-horizontal"></span><ul style="display:none;" id="'+value+'_folder_options" class="folder-extra-options-content"><li><a data-id="'+value+'" href="'+base_url+'/filemanager/file/delete?id='+value+'" title="delete" role="mediafile-folder-delete" type="folder" data-pjax="0">Delete</a></li></ul></div>');
    }
  });
});

$('#jsTree_mediafile-filename').bind("refresh.jstree", function () {
  var base_url = window.location.origin;
  $('li.jstree-node').each(function(){
    var id = $(this).attr('id');
    if(($(this).children('div[class=folder-extra-options]').length) == 0){
      $('#'+id+' a[id='+id+'_anchor]').after('<div class="folder-extra-options" id="'+id+'" href="#"><span class="glyphicon glyphicon-option-horizontal"></span><ul style="display:none;" id="'+id+'_folder_options" class="folder-extra-options-content"><li><a data-id="'+id+'" href="'+base_url+'/filemanager/file/delete?id='+id+'" title="delete" role="mediafile-folder-delete" type="folder" data-pjax="0">Delete</a></li></ul></div>');
    }
  });
});

$(document).on("mouseover",".folder-extra-options",function(e){
  e.preventDefault();
  var id = $(this).attr('id');
  $("#"+id+"_folder_options").show();
})

$(document).on("mouseleave",".folder-extra-options",function(e){
  e.preventDefault();
  var id = $(this).attr('id');
  $("#"+id+"_folder_options").hide();
})

function dragAndDrop(){

  $(document).on('mousedown', '#kt_media_grid tbody tr',function (e) {
    return $.vakata.dnd.start(e, { 'jstree' : true, 'obj' : $(this), 'nodes' : [{ id : this.id, text: $(this).text() }] }, '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + $(this).text() + '<ins class="jstree-copy" style="display:none;">+</ins></div>');
  });

  $(document).on('mousedown', '#kt_media_grid > .grid-table ul li',function (e) {
    return $.vakata.dnd.start(e, { 'jstree' : true, 'obj' : $(this), 'nodes' : [{ id : this.id, text: $(this).text() }] }, '<div id="jstree-dnd" class="jstree-default"><i class="jstree-icon jstree-er"></i>' + $(this).text() + '<ins class="jstree-copy" style="display:none;">+</ins></div>');
  });

  $(document).on('dnd_move.vakata', function (e, data) {
    data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
    var t = $(data.event.target);
    if(!t.closest('.jstree').length) {
      if(t.closest('.drop').length) {
        data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
     }
     else {
        data.helper.find('.jstree-icon').removeClass('jstree-ok').addClass('jstree-er');
      }
    }

  }).on('dnd_stop.vakata', function (e, data) {
    if(typeof(data.data.obj.context.dataset.key) != 'undefined'){
      var nodeId = data.data.obj.context.dataset.key;
      target_id = parseInt(data.event.target.id);
      var parentId = $("#"+data.event.target.id).parent().attr('id');
      var base_url = window.location.origin;
      $.ajax({
        type: 'POST',
        url: base_url+'/filemanager/file/update-folder-data?id='+nodeId+'&parent='+parentId,
        success: function (json) {
          var textContent = data.data.obj.context.textContent;
          var targetText = data.event.target.text;
		  $("#w6-success").remove();
		  $("#w6-error").remove();
          if(json.success){
              $('#jsTree_mediafile-filename').jstree(true).settings.core.data = json.jstreeData;
              $('#jsTree_mediafile-filename').jstree(true).refresh();
              $('[data-key="' + nodeId + '"]').fadeOut();
              $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>"+ json.filename + " moved successfully into "+ targetText + "</div>");
          }else if(json.error){
              $('body').append("<div id='w6-error' class='alert-danger alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-ban'></i>"+ json.filename + " failed to move into "+ targetText + "</div>");
          }
        }
      });
    }

  });
}

$('#jsTree_mediafile-filename').bind("move_node.jstree",function (event, data) {
  var base_url = window.location.origin;
  $.ajax({
    type: 'POST',
    url: base_url+'/filemanager/file/update-folder-data?id='+data.node.id+'&parent='+data.parent,

    success: function (json) {
	  $("#w6-success").remove();
	  $("#w6-error").remove();
      if(json.success){
          $('body').append("<div id='w6-success' class='alert-success alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-check'></i>"+ json.filename + " successfully moved</div>");
      }else if(json.error){
          $('body').append("<div id='w6-error' class='alert-danger alert fade in'><button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button><i class='icon fa fa-ban'></i>"+ json.filename + " failed to move </div>");
      }
    }
  });
  $('li.jstree-node').each(function(){
    var base_url = window.location.origin;
    var id = $(this).attr('id');
      if(($(this).children('div[class=folder-extra-options]').length) == 0){
        $('#'+id+' a[id='+id+'_anchor]').after('<div class="folder-extra-options" id="'+id+'" href="#"><span class="glyphicon glyphicon-option-horizontal"></span><ul style="display:none;" id="'+id+'_folder_options" class="folder-extra-options-content"><li><a data-id="'+id+'" href="'+base_url+'/filemanager/file/delete?id='+id+'" title="delete" role="mediafile-folder-delete" type="folder" data-pjax="0">Delete</a></li></ul></div>');
      }
  });
});

$(document).ready(function(){

  $(document).on("click",".upload-file",function(event){
    event.preventDefault();
    $(document).find("input[id=media-file-upload]").trigger('click');
  });

  var foldersData = true;
  var base_url = window.location.origin;
    $.ajax({
          url: base_url+"/filemanager/file/autocomplete-data",
          success: function (result) {
              foldersData = autoCompleteFrmTree(result,'children');
              doSomething(foldersData);
          }
      });
    function doSomething(foldersData){
      // Custom autocomplete instance.
      $.widget( "app.autocomplete", $.ui.autocomplete, {
        // Which class get's applied to matched text in the menu items.
        _renderItem: function( ul, item ) {
            // Replace the matched text with a custom span. This
            // span uses the class found in the "highlightClass" option.
            var re = new RegExp( "(" + this.term + ")", "gi" ),
                template = "<b>$1</b>",
                label = item.label.replace( re, template ),
                id = item.id,
                $li = $( "<li/>" ).attr('id',id).appendTo( ul );

            // Create and return the custom menu item content.
            $( "<a/>" ).attr( "href", "#" )
                       .html( label )
                       .appendTo( $li );

            return $li;

        }

    });

    $(".folder-search").autocomplete({
        source:foldersData,
        select: function (event, ui) {
          if(ui.item.parent){
            var parent = ui.item.parent;
            var parentId = parent.split('->');
            $.each(parentId,function(i,value){
              if(value == ui.item.id){
                return false;
              }else if($("li#" + value).hasClass('jstree-closed')){
                  $("#jsTree_mediafile-filename").jstree("open_node", $("#"+value));
              }
            });
          }
          $("li#" + ui.item.id).find('a#'+ui.item.id+'_anchor').trigger('click');
         // ui.item.value = "";
        },
      });
    }
});

$(document).on("click",".remove_folder_search",function(){
  var base_url = window.location.origin;
if($('#folder-search').val()!=''){
$('#folder-search').val('');
$('#jsTree_mediafile-filename').jstree('deselect_node',$('#jsTree_mediafile-filename').jstree('get_selected'));
var element = $(this);
  var parent = '';//$(this).parent().attr('id');
    clicks++;  //count clicks
        timer = setTimeout(function () {
          var popup = $(".popup-visible").val();
          var url = base_url+ "/filemanager/file/filemanager-view?parent="+parent+"&popup="+popup;
          var pjaxContainerId= '#'+'pjax-grid-filtering';
          $.pjax.reload(pjaxContainerId, {url: url, container: pjaxContainerId, push: false,timeout:false, replace: false});
          $('.page-load-id').val(parent);
          $("#filemanager_parent").val(parent);
          $("#fileupload-parent").val(parent);
          $('#mediafile-file-fileupload').fileupload('option', 'url', base_url+'/filemanager/file/upload?parent='+parent+'&popup='+popup);
          $('.create_folder').attr('href',base_url+'/filemanager/file/create-folder?parent='+parent);
          $(".uploaderManager").attr('href',base_url+'/filemanager/file/uploadmanager?parent='+parent);
          $('a[role=mediafile-embed-video]').attr('href',base_url+'/filemanager/file/save-embed-video?parent='+parent);
            clicks = 0;             //after action performed, reset counter

        }, DELAY);
	}
});

/**
 * Creates a autocomplete data form tree.
 *
 * @param tree object
 * @return autocomplete data
 */
function autoCompleteFrmTree(object, childAttribute) {
  var b = [];
  loopObject(object);

  function loopObject(array, parent, parentId) {
      $.each(array, function(k, v) {
          var text = (typeof parent != "undefined") ? parent + '->' + v.text : v.text;
          var parent_id = (typeof parentId != "undefined") ? parentId + '->' + v.id :
              v.id;
          var id = v.id;
          if (typeof parentId != "undefined") {
              b.push({
                  'label': text,
                  'id': id,
                  'parent': parent_id
              });
          } else {
              b.push({
                  'label': text,
                  'id': id
              });
          }

          if (typeof v[childAttribute] != "undefined") {
              loopObject(v[childAttribute], text, parent_id);
          }
      });
  }

  return b;
}
