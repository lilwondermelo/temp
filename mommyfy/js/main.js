function getCode() {
    var phone = $('#code').val() + $('#phone').val();
    $.ajax({
        type: "POST",
        url: "_ajaxListener.class.php",
        data: {classFile: "application.class", class: "Application", method: "saveClientData",
            phone: phone
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            alert('Ваш код: ' + data.data)
        } else {
            alert('Ошибка регистрации: '+data.descr);
        }
    });
}

function authTypeSwitch(el) {
    $('.authMenuItemImg').removeClass('authMenuItemImgActive');
    $(el).addClass('authMenuItemImgActive');
    $('.authBlock').removeClass('authBlockActive');
    $('.' + $(el).attr('id')).addClass('authBlockActive');
}