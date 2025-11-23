$(document).ready(function () {
  $("#searchUser").on("keyup", function () {
    var searchText = $(this).val().toLowerCase();
    $("tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
    });
  });
});

function deleteUser() {
  var validation = confirm("Are you sure you want to Delete this user?");
  return validation;
}

function resetPassword() {
  var validation2 = confirm(
    "Are you sure you want to Reset the Password for this user? The New Password will be: P@55W0RD"
  );
  return validation2;
}

$(document).ready(function () {
  $("#searchReceipt").on("keyup", function () {
    var searchText = $(this).val().toLowerCase();
    $("#tabla1 tbody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
    });
  });
});

function actualDate() {
  var today = new Date();
  today.setDate(today.getDate());

  var day = today.getDate();
  var month = today.getMonth() + 1; // FIX: JavaScript months are 0-indexed, so add 1
  var year = today.getFullYear();

  var hours = today.getHours();
  var minutes = today.getMinutes();
  var seconds = today.getSeconds();

  // Format the date and time
  var formattedDate =
    (day < 10 ? "0" : "") +
    day +
    "-" +
    (month < 10 ? "0" : "") +
    month +
    "-" +
    year;
  var formattedTime =
    (hours < 10 ? "0" : "") +
    hours +
    ":" +
    (minutes < 10 ? "0" : "") +
    minutes +
    ":" +
    (seconds < 10 ? "0" : "") +
    seconds;

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
  var formattedDate =
    (day < 10 ? "0" : "") +
    day +
    "-" +
    (month < 10 ? "0" : "") +
    month +
    "-" +
    year;
  document.getElementById("selected-date").value = formattedDate;
}

function cleanBlanks() {
  document.getElementById("receiptForm").reset();
}

$(document).ready(function () {
  $("#datetimepicker").datetimepicker({
    format: "DD-MM-YYYY",
  });
});

$(document).ready(function () {
  $("#searchProduct").on("keyup", function () {
    var searchText = $(this).val().toLowerCase();

    // Realiza una solicitud AJAX para buscar productos
    $.ajax({
      type: "POST",
      url: "../controllers/product_search.php",
      data: { searchText: searchText },
      dataType: "json",
      success: function (response) {
        // Borra la tabla de resultados actual
        $("#tabla1 tbody").empty();

        // Agrega los nuevos resultados a la tabla
        for (var i = 0; i < response.length; i++) {
          var producto = response[i];
          var newRow = $("<tr>");

          newRow.append(
            "<td style='text-align:center'>" + producto.id_producto + "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" +
              producto.nombre_producto +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:justify'>" + producto.descripcion + "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" +
              producto.cantidad_producto +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:center; white-space: nowrap;'>" +
              "Lps. " +
              producto.precio +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" +
              producto.presentacion_producto +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" +
              producto.fecha_vencimiento +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" +
              producto.forma_administracion +
              "</td>"
          );
          newRow.append(
            "<td style='text-align:center'>" + producto.almacenamiento + "</td>"
          );
          newRow.append(
            "<td class='fw-bold text-center'><a href='edit_product.php?id_producto=" +
              producto.id_producto +
              "' class='btn btn-small btn-warning btn-block'><span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-pencil-fill me-1' viewBox='0 0 20 20'> <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/> </svg> Edit </span> </a> <br> <a onclick='return deleteProduct()' href='inventory_control.php?id_producto=" +
              producto.id_producto +
              "' class='btn btn-small btn-danger btn-block'> <span class='d-flex align-items-center'> <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 20 20'><path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/></svg> Delete</span></a> </td>"
          );
          $("#tabla1 tbody").append(newRow);
        }
      },
    });
  });
});

function showMore(id) {
  var descripcionCorta = document.getElementById("descripcion-corta-" + id);
  var longDescription = document.getElementById("descripcion-completa-" + id);

  descripcionCorta.style.display = "none";
  longDescription.style.display = "inline";
  document.querySelector(
    'button[onclick="showMore(' + id + ')"]'
  ).style.display = "none";
  document.querySelector(
    'button[onclick="showLess(' + id + ')"]'
  ).style.display = "inline";
}

function showLess(id) {
  var descripcionCorta = document.getElementById("descripcion-corta-" + id);
  var longDescription = document.getElementById("descripcion-completa-" + id);

  descripcionCorta.style.display = "inline";
  longDescription.style.display = "none";
  document.querySelector(
    'button[onclick="showMore(' + id + ')"]'
  ).style.display = "inline";
  document.querySelector(
    'button[onclick="showLess(' + id + ')"]'
  ).style.display = "none";
}

function deleteProduct() {
  var validation = confirm("Are you sure you want to delete this product?");
  return validation;
}

function goBack() {
  window.history.back();
}

// ==========================================
// Shopping Cart Functions
// ==========================================

/**
 * Add product to shopping cart
 * @param {number} productId - Product ID
 * @param {string} productName - Product name
 * @param {number} price - Product price
 * @param {number} quantity - Quantity to add
 */
function addToCart(productId, productName, price, quantity) {
  if (!quantity || quantity <= 0) {
    alert("Por favor ingrese una cantidad válida");
    return;
  }

  $.ajax({
    type: "POST",
    url: "../controllers/add_product_bill.php",
    data: {
      id_product: productId,
      product_name: productName,
      price_product: price,
      quantityToAdd: quantity
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        updateCartTotal(response.cart_total);
        // Reload the page to show updated cart
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al agregar producto al carrito: " + error);
      console.error(xhr.responseText);
    }
  });
}

/**
 * Remove product from shopping cart
 * @param {number} productId - Product ID to remove
 */
function removeFromCart(productId) {
  if (!confirm("¿Está seguro que desea eliminar este producto del carrito?")) {
    return;
  }

  $.ajax({
    type: "POST",
    url: "../controllers/remove_product_cart.php",
    data: {
      producto_id: productId
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        updateCartTotal(response.cart_total);
        // Reload the page to show updated cart
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al eliminar producto del carrito: " + error);
      console.error(xhr.responseText);
    }
  });
}

/**
 * Update cart total display
 * @param {number} total - New cart total
 */
function updateCartTotal(total) {
  var formatted = "Lps. " + parseFloat(total).toFixed(2);
  $("#cart-total").text(formatted);
}

/**
 * View invoice details
 * @param {number} facturaId - Invoice ID
 */
function viewInvoice(facturaId) {
  $.ajax({
    type: "GET",
    url: "../controllers/view_invoice_details.php",
    data: {
      factura_id: facturaId
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Build modal content
        var invoice = response.invoice;
        var items = response.items;

        var modalContent = `
          <div class="modal fade" id="invoiceModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Factura #${invoice.id_factura}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p><strong>Cliente:</strong> ${invoice.cliente}</p>
                      <p><strong>RTN:</strong> ${invoice.rtn || 'N/A'}</p>
                      <p><strong>Cajero:</strong> ${invoice.cajero}</p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Fecha:</strong> ${invoice.fecha_hora}</p>
                      <p><strong>Estado:</strong> ${invoice.estado}</p>
                      <p><strong>Método de Pago:</strong> ${invoice.metodo_pago}</p>
                    </div>
                  </div>
                  <h6>Productos:</h6>
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach(function (item) {
          modalContent += `
                      <tr>
                        <td>${item.nombre_producto}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">Lps. ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td class="text-end">Lps. ${parseFloat(item.subtotal).toFixed(2)}</td>
                      </tr>
          `;
        });

        modalContent += `
                    </tbody>
                    <tfoot>
                      <tr class="fw-bold">
                        <td colspan="3" class="text-end">TOTAL:</td>
                        <td class="text-end">Lps. ${parseFloat(invoice.total).toFixed(2)}</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        `;

        // Remove existing modal if any
        $("#invoiceModal").remove();
        // Append and show new modal
        $("body").append(modalContent);
        var modal = new bootstrap.Modal(document.getElementById("invoiceModal"));
        modal.show();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al cargar detalles de factura: " + error);
      console.error(xhr.responseText);
    }
  });
}

/**
 * Delete invoice
 * @param {number} facturaId - Invoice ID to delete
 */
function deleteInvoice(facturaId) {
  if (!confirm("¿Está seguro que desea eliminar la factura #" + facturaId + "? Esta acción no se puede deshacer y restaurará el inventario.")) {
    return;
  }

  $.ajax({
    type: "POST",
    url: "../controllers/delete_invoice.php",
    data: {
      factura_id: facturaId
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        // Reload the page to show updated list
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al eliminar factura: " + error);
      console.error(xhr.responseText);
    }
  });
}

/**
 * Validate receipt form before submission
 */
function validateReceiptForm() {
  // Check if cart has items
  var cartTotal = $("#cart-total").text().trim();
  var totalValue = parseFloat(cartTotal.replace("Lps.", "").replace(",", "").trim());

  if (isNaN(totalValue) || totalValue <= 0) {
    alert("El carrito está vacío. Por favor agregue productos antes de generar la factura.");
    return false;
  }

  var customerName = $("[name='name']").val().trim();
  var dateTime = $("#actual-date").val().trim();
  var cashier = $("[name='cashier']").val().trim();
  var paymentMethod = $("[name='payment_method']").val();

  if (!customerName) {
    alert("Por favor ingrese el nombre del cliente");
    $("[name='name']").focus();
    return false;
  }

  if (!dateTime || dateTime === "Date & Time") {
    alert("Por favor seleccione la fecha y hora");
    $("#actual-date").focus();
    return false;
  }

  if (!cashier) {
    alert("Por favor ingrese el nombre del cajero");
    $("[name='cashier']").focus();
    return false;
  }

  if (!paymentMethod || paymentMethod === "Payment Method") {
    alert("Por favor seleccione un método de pago");
    $("[name='payment_method']").focus();
    return false;
  }

  return true;
}
