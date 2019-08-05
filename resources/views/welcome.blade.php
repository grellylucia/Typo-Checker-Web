<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
        
        <title>TypoChecker</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Archivo+Black|Work+Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>

    <body>
        <div id="app">
            <header class="header">  
                <nav class="navbar navbar-light nav-style content">
                    <a class="navbar-brand logo" href="#">
                        <img src="{{URL::asset('/images/icons8-double-tick-60.png')}}" width="40" height="40" class="d-inline-block align-top" alt="">
                        TYPOCHECK
                    </a>
                </nav>
            </header>

            <!-- <div class="position-ref full-height"> -->
                <div class="content content-center out-wrapper top-separator">
                    <h1 class="title center-text">Identifikasi Typo</h1>
                    <h2 class="description-section center-text">Cek kesalahan penulisan kata pada dokumen anda</h2>

                    <div class="textbox-outer upload-box">
                        <label for="inputFile">
                            <img src="{{URL::asset('/images/icons8-upload-100 (5).png')}}" width="100" height="100" class="icon-position" alt="">  
                            <p class="second-text center-text">Letakan file pdf disini atau klik tombol di bawah</p>
                            <div class="btn-wrapper btn-position">
                                <button type="button" id="btnUpload" class="btn btn-primary btn-style">Pilih Dokumen</button>
                            </div>
                        </label>
                        <form id="uploadDoc" class="hide">
                            <input type="file" id="inputFile" accept="application/pdf" />
                        </form>
                    </div> 

                    <div class="bottom-section"></div>
                </div>

                <div id="pdfUpload"> Upload File... </div>
                <div id="pdf"></div>
            <!-- </div> -->
            
            <footer class="footer">
                <div class="content">
                    <div class="col-md-6">
                        <a class="navbar-brand logo" href="#">
                            <img src="{{URL::asset('/images/icons8-double-tick-60.png')}}" width="40" height="40" class="d-inline-block align-top" alt="">
                            TYPOCHECK
                        </a>
                        <p class="desc">Typocheck adalah aplikasi online yang membantu untuk melakukan pengecekan kesalah kata yang terdapat pada dokumen</p>
                    </div>
                </div>
            </footer>
         
        </div>
    </body>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script type="text/javascript" src="https://mozilla.github.io/pdf.js/build/pdf.worker.js"></script>
   
    <script id="script" tpye="text/javascript">

    
    // getPdfText = function (pdf_url) {
    //     $("#pdfUpload").show();
    //     return pdfjsLib.getDocument({url: pdf_url})
    //         .then(function(pdf){
    //             for(var i = 0; i < pdf.numPages; i++) {
    //                 return pdf.getPage(i+1)
    //             }
    //         }).then(function(page) {
    //             $("#pdfUpload").hide();
    //             getText = page.getTextContent().then(function(textContent){
    //                 var str_pdf = '';
    //                 textContent.items.forEach(function(o) {
    //                     str_pdf += o.str;
    //                     str_pdf = str_pdf.replace("  "," ");
    //                 })
    //                 // $("#pdf").append(str_pdf);
    //                 console.log(str_pdf);
    //                 return str_pdf;
    //             });
    //             // return getText; 
    //         })
    //     }

        function getPdfText(pdf_url) {
            $("#pdfUpload").show();
            var pdf = pdfjsLib.getDocument({url: pdf_url});

            pdf.then(getPages)
        }

        function getPages(pdf) {
            for(var i = 0; i <  pdf.numPages; i++) {
                pdf.getPage(i+1).then(getPageText);
            }
        }

        function getPageText(page) {
            var str_pdf = '';
            $("#pdfUpload").hide();
            page.getTextContent().then(function(textContent){
                textContent.items.forEach(function(o) {
                    $("#pdf").append(o.str + '');
                    str_pdf += o.str; 
                });
                console.log(str_pdf);
            });
            
        }

        $("#btnUpload").on('click', function() {
            $("#inputFile").trigger('click');
        });

        $("#inputFile").on('change', function() {
            if(['application/pdf'].indexOf($("#inputFile").get(0).files[0].type) == -1 ) {
                alert('Error: Not a PDF');
                return;
            }
            getPdfText(URL.createObjectURL($("#inputFile").get(0).files[0]));
            // console.log(str_pdf);
            // console.log(result);
            // var test = document.getElementById("pdf");
            // var test2 = $("#pdf").val();
            // console.log(test);
        });

        

    </script>

</html>



