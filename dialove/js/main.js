let overlay = $('.overlay');
let overlayState = (overlay.is(":visible")) ? 1 : 0;
let overlayInner = $('.overlayInner');

getProducts('all');
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



function editProductForm(productId) {
    if (productId == 'new') {
        overlayInner.html('<input type="text" placeholder="Название товара" id="editProductName"><div class="button addButton" onclick="editProduct(\'new\')">Готово</div>');
        toggleOverlay();
    }
    else {
        $.ajax({
            type: "POST",
            url: "../core/_ajaxListener.class.php",
            data: {classFile: "../dialove/classes/productsController.class", class: "ProductsController", method: "getProduct",
                id: productId
            }}).done(function (result) {
            var data = JSON.parse(result);
            if (data.result === "Ok") {
                let productJSON = JSON.parse(data.data);
                overlayInner.html('<input type="text" placeholder="Название товара" id="editProductName" value="' + productJSON['name'] + '"><div class="button addButton" onclick="editProduct(\'' + productId + '\')">Готово</div>');
                toggleOverlay();
            } else {
                console.log(data.descr);
            }
        });
    }
}

function editProduct(productId) {
	let productName = $('#editProductName').val();
	$.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/classes/productsController.class", class: "ProductsController", method: "editProduct",
            name: productName, id: productId
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            console.log(data);
            getProducts('all');
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
        data: {classFile: "../dialove/classes/productsController.class", class: "ProductsController", method: "deleteProduct",
            id: productId
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            console.log(data);
            getProducts('all');
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


function getProducts(filter) {
    $.ajax({
        type: "POST",
        url: "../core/_ajaxListener.class.php",
        data: {classFile: "../dialove/classes/productsController.class", class: "ProductsController", method: "getProducts",
            filter: filter
        }}).done(function (result) {
        var data = JSON.parse(result);
        if (data.result === "Ok") {
            $('.productList').html(data.data);
        } else {
            console.log(data.descr);
        }
    });
}