tinymce.PluginManager.add('addmedia', function(editor) {

    function monitorNodeChange(e) {
        var self = e.target.$el;
        var id = $(tinyMCE.activeEditor.getElement()).attr('id') + '-btn';
        self.html('<button role="editor-launch" class="btn btn-default" id="' + id +
            '"><span class="mce-txt">Add Media</span></button>');
    }

    editor.addButton('imagebutton', {
        icon: false,
        tooltip: 'Add Media',
        onpostrender: monitorNodeChange
    });
});