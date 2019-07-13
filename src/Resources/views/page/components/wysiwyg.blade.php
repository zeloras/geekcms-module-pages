<?php
$name = $name ?? 'wysiwyg-content';
$id = $id ?? 'wysiwyg';
$class = $class ?? 'wysiwyg-edit';
$attributes = $attributes ?? null;
$content = $content ?? null;
$height = $height ?? 600;


$options = (isset($options) && is_array($options))
    ? $options
    : [
        'heightMin' => $height,
        'heightMax' => 900,
    ];
?>

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.5.1//js/froala_editor.pkgd.min.js"></script>
@endpush

@push('css')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.5.1/css/froala_editor.pkgd.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.5.1/css/froala_style.min.css">

    <link rel="stylesheet" href="{{ theme_url('css/editor.css') }}">
@endpush

<div>
    <div class="froala-{{$class}}" id="{{$id}}" {!! $attributes !!}>
        {!! $content !!}
    </div>

    <textarea style="display: none;" class="{{$class}}" name="{{$name}}">{!! $content !!}</textarea>
</div>

@push('script')
    <script>
        // init
        $('div.froala-{{$class}}').froalaEditor({!! json_encode($options, JSON_UNESCAPED_UNICODE) !!});

        // change input
        $('div.froala-{{$class}}').on('froalaEditor.contentChanged', function (e, editor) {
            var content = editor.html.get();
            var txt = $('textarea.' + '{{$class}}' + '[name=' + '{{$name}}' + ']');


            txt.html(content);
        });

        //
        var txtCodeChangeInput{{$class}} = function () {

            var t = setInterval(function () {
                let txtCode = $('div.froala-{{$class}} textarea');

                if (txtCode.length > 0) {
                    clearInterval(t);

                    var c = $(txtCode).val();

                    c = c.replace(/%24/gm, '$');
                    c = c.replace(/%20/gm, ' ');


                    $(txtCode).val(c);

                    txtCode.keyup(function (e) {
                        var content = $(this).val();
                        var txt = $('textarea.' + '{{$class}}' + '[name=' + '{{$name}}' + ']');

                        txt.html(content);
                    });
                }
            }, 500);
        };

        txtCodeChangeInput{{$class}}();

    </script>
@endpush