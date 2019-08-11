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

                    <form class="box" method= "post" action="" enctype="multipart/form-data">
                        <div class="box-input">
                            <label for="inputFile">
                                <input type="file" id="inputFile" name="file" class="hide" />
                                <img src="{{URL::asset('/images/icons8-upload-100 (5).png')}}" width="100" height="100" class="icon-position" alt="">  
                                <p class="second-text center-text"> Letakan file pdf disini atau klik area dalam kotak ini</p>
                                <div class="btn-wrapper btn-position">
                                    <button type="submit" class="btn btn-primary btn-style">Cek Dokumen</button>
                                </div>
                            </label>                           
                        </div>
                        <div class="pdfUpload"> Upload File&hellip;</div>
                    </form>

                    <div class="bottom-section"></div>
                </div>

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
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        });

        var isAdvancedUpload = function() {
            var div = document.createElement('div');
            return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
        }();

        var $form = $('.box');
        var $pdf_url = false;
        var docText = [];
        
        if (isAdvancedUpload) {
            $form.addClass('textbox-outer');
        }

        if (isAdvancedUpload) {

            var droppedFile = false;
            var $input = $form.find('input[type="file"]'),
                $label = $form.find('p'),
                showFile = function(file) {
                    $label.text(file[0].name)
                },

                fileName = function(file) {
                    return file[0].name;
                },

                getFile = function(file) {
                    return file[0];
                }
            
            $form.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            })
            .on('dragover dragenter', function() {
                $form.addClass('is-dragover');
            })
            .on('dragleave dragend drop', function() {
                $form.removeClass('is-dragover');
            })
            .on('drop', function(e) {
                droppedFile = e.originalEvent.dataTransfer.files;
                var val = fileName(droppedFile),
                regex = new RegExp("(.*?)\.(docx|doc|pdf)$");

                if (!(regex.test(val))) {
                    $(this).val('');
                    alert('Ekstensi dokumen harus pdf atau docx / doc');
                }
                else {
                    showFile(droppedFile);
                    $pdf_url = URL.createObjectURL(getFile(droppedFile));
                    return $pdf_url;
                }
            });

            $input.on('change',function(e) {
                var val = $(this).val().toLowerCase(),
                regex = new RegExp("(.*?)\.(docx|doc|pdf)$");
                if (!(regex.test(val))) {
                    $(this).val('');
                    alert('Ekstensi dokumen harus pdf atau docx / doc');
                }
                else {
                    showFile(e.target.files);
                    $pdf_url = URL.createObjectURL($("#inputFile").get(0).files[0]);
                    return $pdf_url;
                }
            });

        }

        $form.on('submit', function(e) {
            if ($form.hasClass('is-uploading')) return false;

                $form.addClass('is-uploading').removeClass('is-error');

            if (isAdvancedUpload) {
                // ajax for modern browsers
                e.preventDefault();
                
                var temp = {url: $pdf_url}
               

                var test = pdfjsLib.getDocument(temp).then(function(pdf) {
                    var pdfDocument = pdf;
                    var pagesPromises = [];

                    for(var i = 0; i <  pdf.numPages; i++) {
                        (function (pageNumber) {
                            pagesPromises.push(getPageText(pageNumber, pdfDocument));                            
                        }) (i+1);
                    }
                    
                    return Promise.all(pagesPromises).then((pageText) => {
                        return pageText
                    })

                }).catch(err => {
                    console.log(err.response);
                })
                
                test.then(response => {
                    $.ajax({
                        type: "POST",
                        url: "http://localhost/input/check-words",
                        data: '{"text":"'+response[0]+'"}',
                        contentType: 'application/json; charset=utf-8',
                        datatype: "json",
                        success: function(data) {
                            var dataTemp = data['results'].result;
                            console.log(dataTemp);
                        },
                        error: function() {
                            alert("Terjadi kesalahan");
                        }
                    })
                    
                })

                // var ajaxData = new FormData($form.get(0));
                // $.ajax({
                //     url: $form.attr('action'),
                //     type: $form.attr('method'),
                //     data: ajaxData,
                //     dataType: 'json',
                //     cache: false,
                //     contentType: false,
                //     processData: false,
                //     complete: function() {
                //         $form.removeClass('is-uploading');
                //     },
                //     success: function(data) {
                //         $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                //         if (!data.success) $errorMsg.text(data.error);
                //     },
                //     error: function() {
                //     // Log the error, show an alert, whatever works for you
                //     }
                // });
            } else {
                // ajax for legacy browsers
                var iframeName  = 'uploadiframe' + new Date().getTime();
                $iframe   = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

                $('body').append($iframe);
                $form.attr('target', iframeName);

                $iframe.one('load', function() {
                    var data = JSON.parse($iframe.contents().find('body' ).text());
                    $form
                        .removeClass('is-uploading')
                        .addClass(data.success == true ? 'is-success' : 'is-error')
                        .removeAttr('target');
                    if (!data.success) $errorMsg.text(data.error);
                    $form.removeAttr('target');
                    $iframe.remove();
                });
            }
        });

        function getPageText(pageNum, pdfDoc) {
            return new Promise(function (resolve, reject){
                pdfDoc.getPage(pageNum).then(function(pdfPage) {
                    pdfPage.getTextContent().then(function (textContent) {
                        var textItems = textContent.items;
                        var finalString = "";

                        for(var i = 0; i < textItems.length; i++) {
                            var item = textItems[i];
                            finalString += item.str
                            $("#pdf").append(finalString);
                            // str_pdf += o.str;
                            // str_pdf = str_pdf.replace("  "," ");
                        }

                        resolve(finalString);
                    });
                });
            });
        }

        // async function getPdfText(pdf_url) {
        //     await pdfjsLib.getDocument({url: pdf_url})
        //         .then(function(pdf){
        //             for(var i = 0; i < pdf.numPages; i++) {
        //                 return pdf.getPage(i+1)
        //             }
        //         })
        //         .then(function(page) { 
        //             getText = page.getTextContent()
        //             .then(function(textContent){
                        
        //                 textContent.items.forEach(function(o) {
        //                     str_pdf += o.str;
        //                     str_pdf = str_pdf.replace("  "," ");
        //                 })
        //                 // $("#pdf").append(str_pdf);
        //                 // console.log(str_pdf);
        //                 temp = str_pdf;
        //                 // console.log(temp);
        //                 return temp;    
        //             }); 
        //         })
                
        //     }

        // function getPdfText(pdf_url) {
        //     $("pdfUpload").show();
        //     var pdf = pdfjsLib.getDocument({url: pdf_url});

        //     pdf.then(getPages)
        // }

        // function getPages(pdf) {
        //     for(var i = 0; i <  pdf.numPages; i++) {
        //         pdf.getPage(i+1).then(getPageText);
        //     }
        // }

        // function getPageText(page) {
        //     var str_pdf = '';
        //     $("pdfUpload").hide();
        //     page.getTextContent().then(function(textContent){
        //         textContent.items.forEach(function(o) {
        //             $("#pdf").append(o.str + '');
        //             str_pdf += o.str; 
        //         });
        //         console.log(str_pdf);
        //     });
            
        // }

        // $("#btnUpload").on('click', function() {
        //     $("#inputFile").trigger('click');
        // });

        // $("#inputFile").on('change', function() {
        //     if(['application/pdf'].indexOf($("#inputFile").get(0).files[0].type) == -1 ) {
        //         alert('Error: Not a PDF');
        //         return;
        //     }
        //     getPdfText(URL.createObjectURL($("#inputFile").get(0).files[0]));
        //     // console.log(str_pdf);
        //     // console.log(result);
        //     // var test = document.getElementById("pdf");
        //     // var test2 = $("#pdf").val();
        //     // console.log(test);
        // });

        

    </script>

</html>



