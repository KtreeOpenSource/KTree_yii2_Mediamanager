<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-upload fade">
        <span class="preview"></span>

        <p class="name">{%=file.name%}</p>
        <strong class="error text-danger"></strong>

        {% if (!i && !o.options.autoUpload) { %}
            <button class="btn btn-primary start" style="display:none;" disabled >
                <i class="glyphicon glyphicon-upload"></i>
                <span><?= Yii::t('fileupload', 'Start') ?></span>
            </button>
        {% } %}
    </li>
{% } %}
</script>
