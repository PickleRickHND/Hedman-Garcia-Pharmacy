$(document).ready(function() {
    $("#searchUser").on("keyup", function() {
        var searchText = $(this).val().toLowerCase();
        $("tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
        });
    });
});

function deleteUser(){
    var validation=confirm("Are you sure you want to Delete this user?")
    return validation;
}

function resetPassword(){
    var validation2=confirm("Are you sure you want to Reset the Password for this user? The New Password will be: P@55W0RD")
    return validation2;
}

$(document).ready(function() {
    $("#searchReceipt").on("keyup", function() {
        var searchText = $(this).val().toLowerCase();
        $("#tabla1 tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
        });
    });
});

function actualDate() {
    var today = new Date();
    today.setDate(today.getDate());

    var day = today.getDate();
    var month = today.getMonth();
    var year = today.getFullYear();

    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();

    // Format the date and time
    var formattedDate = (day < 10 ? "0" : "") + day + "-" + (month < 10 ? "0" : "") + month + "-" + year;
    var formattedTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

    // Combine date and time
    var dateTime = formattedDate + " " + formattedTime;

    document.getElementById("actual-date").value = dateTime;
}

function updateDate() {
    var today = new Date();
    today.setDate(today.getDate() + 1);
    var day = today.getDate();
    var month = today.getMonth() + 1;
    var year = today.getFullYear();
    var formattedDate = (day < 10 ? "0" : "") + day + "-" + (month < 10 ? "0" : "") + month + "-" + year;
    document.getElementById("selected-date").value = formattedDate;
}

$(document).ready(function() {
    $("#searchProductsReceipt").on("keyup", function() {
        var searchText = $(this).val().toLowerCase();

        // Realiza una solicitud AJAX para buscar productos
        $.ajax({
            type: "POST",
            url: "../controllers/product_search.php",
            data: { searchText: searchText },
            dataType: "json",
            success: function(response) {
                // Borra la tabla de resultados actual
                $("#tabla2 tbody").empty();

                // Agrega los nuevos resultados a la tabla
                for (var i = 0; i < response.length; i++) {
                    var producto = response[i];
                    var newRow = $("<tr>");
                    newRow.append("<td style='text-align:center'>" + producto.id_producto + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.nombre_producto + "</td>");
                    newRow.append("<td style='align-items: center' class='align-items-center justify-content-center'><input class='form-control' style='width: 60px;'>" + "</td>");
                    newRow.append("<td style='text-align:center; white-space: nowrap;'>Lps. " + producto.precio + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.presentacion_producto + "</td>");
                    newRow.append("<td style='text-align:center'><div class='d-flex justify-content-center align-items-center'> <a onclick='#' href=" + producto.id_producto + "'billing.php?id_producto=' class='btn btn-primary'> <span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-cart-plus' viewBox='0 0 16 16'><path d='M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z'/><path d='M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z'/></svg></span></a></div></td>");

                    $("#tabla2 tbody").append(newRow);
                }
            }
        });
    });
});

function cleanBlanks() {
    document.getElementById("receiptForm").reset();
}

$(document).ready(function () {
    $('#datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY',
    });
});

$(document).ready(function() {
    $("#searchProduct").on("keyup", function() {
        var searchText = $(this).val().toLowerCase();

        // Realiza una solicitud AJAX para buscar productos
        $.ajax({
            type: "POST",
            url: "../controllers/product_search.php",
            data: { searchText: searchText },
            dataType: "json",
            success: function(response) {
                // Borra la tabla de resultados actual
                $("#tabla1 tbody").empty();

                // Agrega los nuevos resultados a la tabla
                for (var i = 0; i < response.length; i++) {
                    var producto = response[i];
                    var newRow = $("<tr>");

                    newRow.append("<td style='text-align:center'>" + producto.id_producto + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.nombre_producto + "</td>");
                    newRow.append("<td style='text-align:justify'>" + producto.descripcion + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.existencia_producto + "</td>");
                    newRow.append("<td style='text-align:center; white-space: nowrap;'>" + 'Lps. ' + producto.precio + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.presentacion_producto + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.fecha_vencimiento + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.forma_administracion + "</td>");
                    newRow.append("<td style='text-align:center'>" + producto.almacenamiento + "</td>");
                    newRow.append("<td class='fw-bold text-center'><a href=" + producto.id_producto + "'edit_product.php?id_producto=' class='btn btn-small btn-warning btn-block'><span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-pencil-fill me-1' viewBox='0 0 20 20'> <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/> </svg> Editar </span> </a> <br> <a onclick='return eliminar()' href=" + producto.id_producto + "'inventory_control.php?id_producto=' class='btn btn-small btn-danger btn-block'> <span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 20 20'><path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/></svg> Eliminar</span></a> </td>");

                    $("#tabla1 tbody").append(newRow);
                }
            }
        });
    });
});

function showMore(id) {
    var descripcionCorta = document.getElementById("descripcion-corta-" + id);
    var longDescription = document.getElementById("descripcion-completa-" + id);

    descripcionCorta.style.display = "none";
    longDescription.style.display = "inline";
    document.querySelector('button[onclick="showMore(' + id + ')"]').style.display = "none";
    document.querySelector('button[onclick="showLess(' + id + ')"]').style.display = "inline";
}

function showLess(id) {
    var descripcionCorta = document.getElementById("descripcion-corta-" + id);
    var longDescription = document.getElementById("descripcion-completa-" + id);

    descripcionCorta.style.display = "inline";
    longDescription.style.display = "none";
    document.querySelector('button[onclick="showMore(' + id + ')"]').style.display = "inline";
    document.querySelector('button[onclick="showLess(' + id + ')"]').style.display = "none";
}

function deleteProduct(){
    var validation=confirm("Esta Seguro que desea Eliminar este Producto?")
    return validation;
}