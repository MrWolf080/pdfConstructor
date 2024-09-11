import $ from "jquery";

$(document).on('change', '.fileInput', function(event) {
    let file = event.target.files[0]; // Получаем выбранный файл
    if (file) {
        $(this).siblings('.fileModifiedTime').val(file.lastModified);
        let replacedFileName = file.name.replace(/\.[^/.]+$/, "")
        $(this).siblings('.docShortName').val(replacedFileName);

        let listName = replacedFileName.replace(/\).*/, ") - УЛ");
        $('#infoList').val(listName);
    }
});

$('#addItems').on('click', function() {
    let $fileUploadGroup = $('.file-upload-group:first').clone();
    let $existsFileInfo = $fileUploadGroup.find('.exists-file-info');
    if ($existsFileInfo.length !== 0) {
        $existsFileInfo.parent().find('.fileInput').css('display', '');
        $existsFileInfo.remove();
    }
    $fileUploadGroup.find('input').val('');
    $fileUploadGroup.find('.exists-file-info').remove();
    $fileUploadGroup.find('.deleteItems').val('Удалить элементы');
    $fileUploadGroup.insertBefore('#addItems');
});

$('#addItemsSign').on('click', function() {
    let $signUploadGroup = $('.sign-upload-group:first').clone();
    let $existsSign = $signUploadGroup.find('.exists-sign');
    if ($existsSign.length !== 0) {
        $existsSign.parent().find('.signInput').css('display', '');
        $existsSign.remove();
    }
    $signUploadGroup.find('input').val('');
    $signUploadGroup.find('.deleteItemsSign').val('Удалить элементы');
    $signUploadGroup.insertBefore('#addItemsSign');
});

$('.replace-file-info').on('click', function() {
    let $existsFileInfo = $(this).closest('.exists-file-info');
    if ($existsFileInfo.length !== 0) {
        $existsFileInfo.parent().find('.fileInput').css('display', '');
        $existsFileInfo.remove();
    }
});

$('.replace-sign').on('click', function() {
    let $existsSign = $(this).closest('.exists-sign');
    if ($existsSign.length !== 0) {
        $existsSign.parent().find('.signInput').css('display', '');
        $existsSign.remove();
    }
});

$(document).on('click', '.deleteItems', function() {
    if ($('.file-upload-group').length > 1) {
        $(this).closest('.file-upload-group').remove();
    }
});

$(document).on('click', '.deleteItemsSign', function() {
    if ($('.sign-upload-group').length > 1) {
        $(this).closest('.sign-upload-group').remove();
    }
});
