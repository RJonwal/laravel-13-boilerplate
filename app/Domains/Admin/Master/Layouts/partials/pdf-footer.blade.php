@php
    $isPrint = request()->query('export_type') === 'print';

    $pdfImage = getSetting('pdf_image');
    $instaImage = getSetting('pdf_insta_image');

    $pdfImageSrc = $isPrint
        ? $pdfImage
        : public_path(parse_url($pdfImage, PHP_URL_PATH) ?? '');

    $instaImageSrc = $isPrint
        ? $instaImage
        : public_path(parse_url($instaImage, PHP_URL_PATH) ?? '');
@endphp

<div style="width:100%; font-size:16px; margin-top:40px;">

    @if(getSetting('pdf_remark'))
        <div style="margin-bottom:10px;">
            <strong>Remark:</strong>
            <span  style="font-weight:bold;">{{ getSetting('pdf_remark') }}</span>
        </div>
    @endif

    @if($pdfImage || $instaImage)
        <div style="margin-top:10px; width:100%;">

            @if($pdfImage && ($isPrint || file_exists($pdfImageSrc)))
                <div style="width: 50%;float: left;">
                    <img src="{{ $pdfImageSrc }}"
                        alt="PDF Image"
                        style="
                            width:160px;
                            height:140px;
                            display:inline-block;
                            vertical-align:middle;
                            margin-right:20px;
                        ">
                </div>
            @endif

            @if($instaImage && ($isPrint || file_exists($instaImageSrc)))
                <div style="width: 50%; float: right;text-align: right;">
                    <img src="{{ $instaImageSrc }}"
                        alt="PDF Insta Image"
                        style="
                            width:160px;
                            height:140px;
                            display:inline-block;
                            vertical-align:middle;
                        ">
                </div>
            @endif

        </div>
    @endif

</div>
