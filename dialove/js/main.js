let overlay = $('.overlay');
let overlayState = (overlay.is(":visible")) ? 1 : 0;
let overlayInner = $('.overlayInner');
let activeTab = 'all';
let summTotal = 0;
let summTotalTran = 0;
let summTran = 0;

getProducts(activeTab);


function toggleOverlay() {
	if (overlayState) {
		overlay.hide();
		overlayState = 0;
	}
	else {
		overlay.css('display', 'flex');
		overlayState = 1;
	}
}

function addProductForm(productId) {
        $.ajax({
            type: "POST",
            url: "../core/_ajaxListener.class.php",
            data: {classFile: "../dialove/controllers/productsController.class", class: "ProductsController", method: "addProductForm",
                id: productId
            }}).done(function (result) {
            var data = JSON.parse(result);
            if (data.result === "Ok") {
                overlayInner.html(data.data);
                toggleOverlay();
            } else {
                console.log(data.descr);
            }
        });
}



function editProductForm(e, productId) {
    if (productId == 'new') {
        overlayInner.html('<input type="text" placeholder="Название товара" id="editProductName"><input type="number" placeholder="Транзит" id="editProductTran"><input type="number" placeholder="Резерв транзита" id="editProductTranRes"><div class="menuRow"><div class="button addButton" onclick="toggleOverlay()">Назад</div><div class="button addButton" onclick="editProduct(\'new\')">Готово</div></div>');
        toggleOverlay();
    }
    else {
        e.stopPropagation();
        $.ajax({
            type: "POST",
            url: "../core/_ajaxListener.class.php",
            data: {classFile: "../dialove/controllers/productsController.class", class: "ProductsController", method: "editProductForm",
                id: productId
            }}).done(function (result) {
            var data = JSON.parse(result);
            if (data.result === "Ok") {
                overlayInner.html(data.data);
                toggleOverlay();
            } else {
                console.log(data.descr);
            }
        });
    }
}

function editProduct(productId) {
	let productName = $('#editProductName').val();
    let productTran = $('#editProductTran').val();
    console.log(productTran);
	$.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/apiClasses/productsApi.class", class: "ProductsApi", method: "editProduct",
            name: productName, id: productId, tran: productTran
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            getProducts();
            toggleOverlay();
        } else {
            console.log(data.descr);
        }
    });
}


function addProduct(productId) {
    let productQuantity = $('#addProductQuantity').val();
    $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/apiClasses/productsApi.class", class: "ProductsApi", method: "addToCart",
            productId: productId, productQuantity: productQuantity
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            console.log(data);
            getProducts();
            toggleOverlay();
        } else {
            console.log(data.descr);
        }
    });
}



function deleteProduct(productId) {
    $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/apiClasses/productsApi.class", class: "ProductsApi", method: "deleteProduct",
            id: productId
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            console.log(data);
            getProducts();
            toggleOverlay();
        } else {
            console.log(data.descr);
        }
    });
}



function deleteProductConfirm(productId) {
    overlayInner.html('Удалить продукт? <div class="button addButton" onclick="deleteProduct(\'' + productId + '\')">Подтвердить</div><div class="button addButton" onclick="toggleOverlay()">Отмена</div>');
        toggleOverlay();
}



function getProducts() {
    $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/controllers/productsController.class", class: "ProductsController", method: "getProducts",
            filter: activeTab
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            $('.productList').html(data.data);
            summQuantity();
            $('#all').click();
        } else {
            console.log(data.descr);
        }
    });
}


function summQuantity() {
    summTotal = 0;
    summTotalTran = 0;
    summTran = 0;
    $('.productListItemCountTotal').each(function() {
        summTotal += parseInt($(this).html());
    });
    $('.productListItemCountTotalTran').each(function() {
        summTotalTran += parseInt($(this).html());
    });
    $('.productListItemCountTran').each(function() {
        summTran += parseInt($(this).html());
    });
    $('.summTotal').html(summTotal);
    $('.summTotalTran').html(summTotalTran);
    $('.summTran').html(summTran);

}

function clearCart() {
     $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/apiClasses/productsApi.class", class: "ProductsApi", method: "clearCart"
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            getProducts();
        } else {
            console.log(data.descr);
        }
    });
}




$('body').on('click', '.tab', function(){
    activeTab = $(this).attr('id');
    if (activeTab == 'cart') {
        $('.addButton').hide();
        $('.allProductList').hide();
        $('.cartProductList').show();
        $('.summ').show();
    }
    else {
        $('.addButton').show();
        $('.allProductList').show();
        $('.cartProductList').hide();
        $('.summ').hide();
    }
    $('.tab').removeClass('activeTab');
            $('.tab.' + activeTab + 'Tab').addClass('activeTab');
})