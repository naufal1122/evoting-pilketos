function previewImage() {
    var fileKetuaEl = document.getElementById('gambarKetua');
    var fileWakilEl = document.getElementById('gambarWakil');

    if (fileKetuaEl && fileKetuaEl.files && fileKetuaEl.files.length > 0) {
        var fileKetua = fileKetuaEl.files[0];
        var fileReaderKetua = new FileReader();

        fileReaderKetua.onload = function (event) {
            var filename = document.getElementById('filenameKetua');
            if (filename) {
                filename.innerHTML = fileKetua.name;
            }

            var previewKetua = document.getElementById('previewKetua');
            if (previewKetua) {
                previewKetua.src = event.target.result;
            }

            var previewContainer = document.getElementById('previewKetuaContainer');
            if (previewContainer) {
                previewContainer.style.display = 'block';
            }
        };

        fileReaderKetua.readAsDataURL(fileKetua);
    }

    if (fileWakilEl && fileWakilEl.files && fileWakilEl.files.length > 0) {
        var fileWakil = fileWakilEl.files[0];
        var fileReaderWakil = new FileReader();

        fileReaderWakil.onload = function (event) {
            var filename = document.getElementById('filenameWakil');
            if (filename) {
                filename.innerHTML = fileWakil.name;
            }

            var previewWakil = document.getElementById('previewWakil');
            if (previewWakil) {
                previewWakil.src = event.target.result;
            }
        };

        fileReaderWakil.readAsDataURL(fileWakil);
    }
}

function previewFile() {

    var fileExcel = document.getElementById('fileExcel').files;
    if( fileExcel.length > 0 ) {

     
        var fileReader = new FileReader();

        fileReader.onload = function (event) {

            var filename = document.getElementById('filenameExcel');
            filename.innerHTML = fileExcel[0].name;

        };

        fileReader.readAsDataURL(fileExcel[0]);

    }

}
