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
    $fileUploadGroup.find('input').val('');
    $fileUploadGroup.find('.deleteItems').val('Удалить элементы');
    removeExistsFileInfo($fileUploadGroup.find('.exists-file-info'));
    $fileUploadGroup.insertBefore('#addItems');
});

$('#addItemsSign').on('click', function() {
    let $signUploadGroup = $('.sign-upload-group:first').clone();
    $signUploadGroup.find('input').val('');
    $signUploadGroup.find('.deleteItemsSign').val('Удалить элементы');
    removeExistsSign($signUploadGroup.find('.exists-sign'));
    $signUploadGroup.insertBefore('#addItemsSign');
});

$('.replace-file-info').on('click', function() {
    let $existsFileInfo = $(this).closest('.exists-file-info');
    removeExistsFileInfo($existsFileInfo);
});

$('.replace-sign').on('click', function() {
    let $existsSign = $(this).closest('.exists-sign');
    removeExistsSign($existsSign);
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

$(document).on('click', '.file-upload-group-down', function() {
    let $file_upload_group = $(this).closest('.file-upload-group');
    swapFileUploadGroups($file_upload_group, false);
});

$(document).on('click', '.file-upload-group-up', function() {
    let $file_upload_group = $(this).closest('.file-upload-group');
    swapFileUploadGroups($file_upload_group, true);
});

$(document).on('click', '.sign-upload-group-down', function() {
    let $sign_upload_group = $(this).closest('.sign-upload-group');
    swapSignUploadGroups($sign_upload_group, false);
});

$(document).on('click', '.sign-upload-group-up', function() {
    let $sign_upload_group = $(this).closest('.sign-upload-group');
    swapSignUploadGroups($sign_upload_group, true);
});

var removeExistsFileInfo = function($existsFileInfo) {
    if ($existsFileInfo.length !== 0) {
        $existsFileInfo.parent().find('.fileInput').css('display', '');
        $existsFileInfo.remove();
    }
}

var removeExistsSign = function($existsSign) {
    if ($existsSign.length !== 0) {
        $existsSign.parent().find('.signInput').css('display', '');
        $existsSign.remove();
    }
}

var createExistsFileInfo = function($file_upload_group) {
    let fileName = $file_upload_group.find('.fileName').val();
    if (typeof fileName === 'string' && fileName !== '' && $file_upload_group.find('.exists-file-info').length === 0) {
        $file_upload_group.find('.fileInput').css('display', 'none');
        $file_upload_group.find('.fileInput').before('' +
            '<div class="exists-file-info">' +
                '<div class="d-flex gap-2 align-items-center">' +
                    '<div class="exist-file-name">Загружен файл: '+ fileName +'</div>' +
                    '<input type="button" class="btn btn-secondary replace-file-info" value="Заменить файл">' +
                '</div>' +
            '</div>'
        );
        $file_upload_group.find('.replace-file-info').on('click', function() {
            let $existsFileInfo = $(this).closest('.exists-file-info');
            removeExistsFileInfo($existsFileInfo);
        });
    }
}

var createExistsSign = function($sign_group) {
    let fileName = $sign_group.find('.signFileName').val();
    if (typeof fileName === 'string' && fileName !== '' && $sign_group.find('.exists-sign').length === 0) {
        $sign_group.find('.signInput').css('display', 'none');
        $sign_group.find('.signInput').before('' +
            '<div class="exists-sign">' +
                '<div class="d-flex gap-2 align-items-center">' +
                    '<div class="exist-sign-file-name">Загружен файл: '+ fileName +'</div>' +
                    '<input type="button" class="btn btn-secondary replace-sign" value="Заменить файл">' +
                '</div>' +
            '</div>'
        );
        $sign_group.find('.replace-sign').on('click', function() {
            let $existsSign = $(this).closest('.exists-sign');
            removeExistsSign($existsSign);
        });
    }
}

var swapFileUploadGroups = function($file_upload_group, up) {
    let $another_file_upload_group;
    if (up) {
        $another_file_upload_group = $file_upload_group.prev('.file-upload-group');
    } else {
        $another_file_upload_group = $file_upload_group.next('.file-upload-group');
    }

    if ($another_file_upload_group.length === 0) {
        return;
    }

    let tmpfileModifiedTime = $another_file_upload_group.find('.fileModifiedTime').val();
    let $tmpfileInput = $another_file_upload_group.find('.fileInput').clone();
    let tmpfileName = $another_file_upload_group.find('.fileName').val();
    let tmpfileSize = $another_file_upload_group.find('.fileSize').val();
    let tmpfileMD5 = $another_file_upload_group.find('.fileMD5').val();
    let tmpchangeTimeDescription = $another_file_upload_group.find('.changeTimeDescription').val();
    let tmpdocShortName = $another_file_upload_group.find('.docShortName').val();
    let tmpdocName = $another_file_upload_group.find('.docName').val();
    let tmpdocChanges = $another_file_upload_group.find('.docChanges').val();

    $another_file_upload_group.find('.fileInput').remove();
    $another_file_upload_group.find('.fileModifiedTime').after($file_upload_group.find('.fileInput'));

    $another_file_upload_group.find('.fileModifiedTime').val($file_upload_group.find('.fileModifiedTime').val());
    $another_file_upload_group.find('.fileName').val($file_upload_group.find('.fileName').val());
    $another_file_upload_group.find('.fileSize').val($file_upload_group.find('.fileSize').val());
    $another_file_upload_group.find('.fileMD5').val($file_upload_group.find('.fileMD5').val());
    $another_file_upload_group.find('.changeTimeDescription').val($file_upload_group.find('.changeTimeDescription').val());
    $another_file_upload_group.find('.docShortName').val($file_upload_group.find('.docShortName').val());
    $another_file_upload_group.find('.docName').val($file_upload_group.find('.docName').val());
    $another_file_upload_group.find('.docChanges').val($file_upload_group.find('.docChanges').val());
    $another_file_upload_group.find('.exist-file-name').empty().append('Загружен файл: ' + $file_upload_group.find('.fileName').val());

    $file_upload_group.find('.fileInput').remove();
    $file_upload_group.find('.fileModifiedTime').after($tmpfileInput);

    $file_upload_group.find('.fileModifiedTime').val(tmpfileModifiedTime);
    $file_upload_group.find('.fileName').val(tmpfileName);
    $file_upload_group.find('.fileSize').val(tmpfileSize);
    $file_upload_group.find('.fileMD5').val(tmpfileMD5);
    $file_upload_group.find('.changeTimeDescription').val(tmpchangeTimeDescription);
    $file_upload_group.find('.docShortName').val(tmpdocShortName);
    $file_upload_group.find('.docName').val(tmpdocName);
    $file_upload_group.find('.docChanges').val(tmpdocChanges);
    $file_upload_group.find('.exist-file-name').empty().append('Загружен файл: ' + tmpfileName);

    let hasFileUploadGroupFileExists = $file_upload_group.find('.exists-file-info').length > 0;
    let hasAnotherFileUploadGroupFileExists = $another_file_upload_group.find('.exists-file-info').length > 0;
    if (!hasFileUploadGroupFileExists) {
        if (hasAnotherFileUploadGroupFileExists) {
            createExistsFileInfo($file_upload_group);
            removeExistsFileInfo($another_file_upload_group.find('.exists-file-info'));
        }
    }

    if (hasFileUploadGroupFileExists) {
        if (!hasAnotherFileUploadGroupFileExists) {
            createExistsFileInfo($another_file_upload_group);
            removeExistsFileInfo($file_upload_group.find('.exists-file-info'));
        }
    }
}

var swapSignUploadGroups = function($sign_upload_group, up) {
    let $another_sign_upload_group;
    if (up) {
        $another_sign_upload_group = $sign_upload_group.prev('.sign-upload-group');
    } else {
        $another_sign_upload_group = $sign_upload_group.next('.sign-upload-group');
    }

    if ($another_sign_upload_group.length === 0) {
        return;
    }

    let tmpbase64 = $another_sign_upload_group.find('.base64').val();
    let $tmpsignInput = $another_sign_upload_group.find('.signInput').clone();
    let tmpsignFileName = $another_sign_upload_group.find('.signFileName').val();
    let tmpwork = $another_sign_upload_group.find('.work').val();
    let tmpfamily = $another_sign_upload_group.find('.family').val();
    let tmpsignDate = $another_sign_upload_group.find('.signDate').val();

    $another_sign_upload_group.find('.signInput').remove();
    $another_sign_upload_group.find('.base64').before($sign_upload_group.find('.signInput'));

    $another_sign_upload_group.find('.base64').val($sign_upload_group.find('.base64').val());
    $another_sign_upload_group.find('.signFileName').val($sign_upload_group.find('.signFileName').val());
    $another_sign_upload_group.find('.work').val($sign_upload_group.find('.work').val());
    $another_sign_upload_group.find('.family').val($sign_upload_group.find('.family').val());
    $another_sign_upload_group.find('.signDate').val($sign_upload_group.find('.signDate').val());
    $another_sign_upload_group.find('.exist-sign-file-name').empty().append('Загружен файл: ' + $sign_upload_group.find('.signFileName').val());

    $sign_upload_group.find('.signInput').remove();
    $sign_upload_group.find('.base64').before($tmpsignInput);

    $sign_upload_group.find('.base64').val(tmpbase64);
    $sign_upload_group.find('.signFileName').val(tmpsignFileName);
    $sign_upload_group.find('.work').val(tmpwork);
    $sign_upload_group.find('.family').val(tmpfamily);
    $sign_upload_group.find('.signDate').val(tmpsignDate);
    $sign_upload_group.find('.exist-sign-file-name').empty().append('Загружен файл: ' + tmpsignFileName);

    let hasSignUploadGroupFileExists = $sign_upload_group.find('.exists-sign').length > 0;
    let hasAnotherSignUploadGroupFileExists = $another_sign_upload_group.find('.exists-sign').length > 0;
    if (!hasSignUploadGroupFileExists) {
        if (hasAnotherSignUploadGroupFileExists) {
            createExistsSign($sign_upload_group);
            removeExistsSign($another_sign_upload_group.find('.exists-sign'));
        }
    }

    if (hasSignUploadGroupFileExists) {
        if (!hasAnotherSignUploadGroupFileExists) {
            createExistsSign($another_sign_upload_group);
            removeExistsSign($sign_upload_group.find('.exists-sign'));
        }
    }
}
