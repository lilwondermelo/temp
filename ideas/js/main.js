function registration() {
    if (!validateEmail('register')) {
        showSystemMessage('Невалидный email');
        return;
    }
    var email = $('#register').val();
    console.log(email);
    $.ajax({
        type: "POST",
        url: "_ajaxListener.class.php",
        data: {classFile: "application.class", class: "Application", method: "saveUserData",
            email: email
        }}).done(function (result) {

        var data = JSON.parse(result);
        console.log(data);
        if (data.result === "Ok") {
            document.location.href = "./index.php";
        } else {
            showSystemMessage('Ошибка регистрации: ' + data.descr);
        }
    });
}

$('body').on('click', '#registerButton', function () {
        registration();
    });
$('body').on('mouseover', '.cabinetSidebarMenuItem', function () {
    $('.cabinetSidebarMenuItemActive').addClass('cabinetSidebarMenuItemActiveHover');
});
$('body').on('mouseleave', '.cabinetSidebarMenu', function () {
    $('.cabinetSidebarMenuItemActive').removeClass('cabinetSidebarMenuItemActiveHover');
});
$('body').on('click', '.cabinetSidebarMenuItem', function () {
    if (!$(this).hasClass('cabinetSidebarMenuItemActive')) {
        $('.cabinetSidebarMenuItemActive').removeClass('cabinetSidebarMenuItemActive');
        $(this).addClass('cabinetSidebarMenuItemActive');
    }
    
});

$('body').on('click', '.shopMenuItem', function () {
    if (!$(this).hasClass('shopMenuItemActive')) {
        $('.shopMenuItemActive').removeClass('shopMenuItemActive');
        $(this).addClass('shopMenuItemActive');
    }
    
});






// Валидация почты
function validateEmail(type) {
    var email = $("#" + type).val();
    var re = /[a-zА-Яа-я0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zА-Яа-я0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zА-Яа-я0-9](?:[a-zА-Яа-я0-9-]*[a-zА-Яа-я0-9])?\.)+[a-zА-Яа-я0-9](?:[a-zА-Яа-я0-9-]*[a-zА-Яа-я0-9])?/;
    return re.test(String(email).toLowerCase());
}

function showSystemMessage(text = '') {
    $('#systemMessageText').html(text);
    $('.systemMessageOverlay').css("display", "flex");
    $("body").css("overflow", "hidden");
}


function closeSystemMessage() {
    $('.systemMessageOverlay').hide();
    $("body").css("overflow", "");
}


