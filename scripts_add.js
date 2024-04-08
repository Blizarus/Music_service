$(document).ready(function () {
    function handleFileSelection(buttonId, inputFileId, displayElementId) {
        // Обработчик события клика на кнопке
        $('#' + buttonId).click(function () {
            // Триггер события клика на скрытом инпуте
            $('#' + inputFileId).click();
        });

        $('#' + inputFileId).change(function () {
            // Получаем имя файла из пути
            var fileName = $(this).val().split('\\').pop();

            // Обновляем текст элемента для отображения выбранного файла
            $('#' + displayElementId).text('Выбран файл: ' + fileName);

            $('#' + buttonId).text('Файл выбран');

            // Ваш код обработки изменения значения инпута, если необходимо
            console.log('Выбран файл:', fileName);
            if (buttonId !== 'selectCoverButton') {
                var audioPlayer = document.getElementById('audioPlayer');
                if (audioPlayer) {
                    audioPlayer.style.display = 'block';
                    audioPlayer.src = decodeURIComponent(URL.createObjectURL(this.files[0]));
                }
            }
            else {
                // Если выбрана кнопка selectGenreButton
                var cover = document.getElementById('coverImage');
                if (cover) {
                    // Показываем элемент cover
                    cover.style.display = 'block';

                    // Устанавливаем источник изображения
                    cover.src = URL.createObjectURL(this.files[0]);
                }
            }
        });
    }
    $('#selectMusicButton').click(function () {
        // Передаем идентификаторы соответствующих элементов
        handleFileSelection('selectMusicButton', 'music_file', 'file');
    });

    $('#selectCoverButton').click(function () {
        // Передаем идентификаторы соответствующих элементов
        handleFileSelection('selectCoverButton', 'cover_file', 'cover');
    });
});

function showTable(index) {

    for (var i = 1; i <= 4; i++) {
        var table = document.getElementById('table' + i);
        if (table) {
            table.style.display = 'none';
        }
    }

    var tableToDisplay = document.getElementById('table' + index);
    if (tableToDisplay) {
        tableToDisplay.style.display = 'block';
    }
}

