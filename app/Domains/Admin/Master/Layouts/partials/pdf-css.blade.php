<style>
    @page {
        margin: 10mm 10mm 10mm 10mm; /* top, right, bottom, left */
    }
    @media print {
        /* body {
            margin: 20mm 15mm 20mm 15mm;
        } */
        *{
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }
    
    /* --- Noto Sans Devanagari --- */
    @font-face {
        font-family: 'NotoSansDevanagari';
        src: url('{{ asset("fonts/Noto_Sans_Devanagari/static/NotoSansDevanagari-Regular.ttf") }}') format('truetype');
        font-weight: 400;
    }
    @font-face {
        font-family: 'NotoSansDevanagari';
        src: url('{{ asset("fonts/Noto_Sans_Devanagari/NotoSansDevanagari-Bold.ttf") }}') format('truetype');
        font-weight: 700;
    }
    @font-face {
        font-family: 'NotoSansDevanagari';
        src: url('{{ asset("fonts/Noto_Sans_Devanagari/NotoSansDevanagari-ExtraBold.ttf") }}') format('truetype');
        font-weight: 800;
    }

    /* --- Noto Sans (Latin) --- */
    @font-face {
        font-family: 'NotoSans';
        src: url('{{ asset("fonts/Noto_Sans/static/NotoSans-Regular.ttf") }}') format('truetype');
        font-weight: 400;
    }
    @font-face {
        font-family: 'NotoSans';
        src: url('{{ asset("fonts/Noto_Sans/static/NotoSans-Bold.ttf") }}') format('truetype');
        font-weight: 700;
    }
    @font-face {
        font-family: 'NotoSans';
        src: url('{{ asset("fonts/Noto_Sans/static/NotoSans-ExtraBold.ttf") }}') format('truetype');
        font-weight: 800;
    }

    body, p, h1, h2, h3, h4, h5, h6, span, div, strong {
        font-family: 'NotoSansDevanagari', 'NotoSans', sans-serif;
    }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 3px solid #ccc; padding: 8px; text-align: left;font-weight: bold;}
    .w-120px{width: 120px;}
    /* th { background: #f6f6f6; }  */
</style>