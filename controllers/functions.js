// ==========================================
// Security Functions
// ==========================================

/**
 * Escape HTML special characters to prevent XSS attacks
 * @param {string} text - Text to escape
 * @returns {string} - Escaped text safe for HTML insertion
 */
function escapeHtml(text) {
  if (text === null || text === undefined) {
    return '';
  }
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// ==========================================
// Search Functions
// ==========================================

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
    "Are you sure you want to Reset the Password for this user? A new temporary password will be generated."
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

// ==========================================
// Date/Time Functions
// ==========================================

function actualDate() {
  var today = new Date();
  var day = today.getDate();
  var month = today.getMonth() + 1;
  var year = today.getFullYear();
  var hours = today.getHours();
  var minutes = today.getMinutes();
  var seconds = today.getSeconds();

  var formattedDate = (day < 10 ? "0" : "") + day + "-" + (month < 10 ? "0" : "") + month + "-" + year;
  var formattedTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

  document.getElementById("actual-date").value = formattedDate + " " + formattedTime;
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

function cleanBlanks() {
  document.getElementById("receiptForm").reset();
}

$(document).ready(function () {
  $("#datetimepicker").datetimepicker({
    format: "DD-MM-YYYY",
  });
});

// ==========================================
// Product Search with XSS Protection
// ==========================================

$(document).ready(function () {
  $("#searchProduct").on("keyup", function () {
    var searchText = $(this).val().toLowerCase();

    $.ajax({
      type: "POST",
      url: "../controllers/product_search.php",
      data: { searchText: searchText },
      dataType: "json",
      success: function (response) {
        $("#tabla1 tbody").empty();

        for (var i = 0; i < response.length; i++) {
          var producto = response[i];
          var newRow = $("<tr>");
          var productId = parseInt(producto.id_producto, 10);

          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.id_producto) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.nombre_producto) + "</td>");
          newRow.append("<td style='text-align:justify'>" + escapeHtml(producto.descripcion) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.cantidad_producto) + "</td>");
          newRow.append("<td style='text-align:center; white-space: nowrap;'>Lps. " + escapeHtml(producto.precio) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.presentacion_producto) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.fecha_vencimiento) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.forma_administracion) + "</td>");
          newRow.append("<td style='text-align:center'>" + escapeHtml(producto.almacenamiento) + "</td>");
          newRow.append(
            "<td class='fw-bold text-center'><a href='edit_product.php?id_producto=" + productId +
            "' class='btn btn-small btn-warning btn-block'><span class='d-flex align-items-center'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-pencil-fill me-1' viewBox='0 0 20 20'><path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/></svg> Edit</span></a><br><a onclick='return deleteProduct()' href='inventory_control.php?id_producto=" + productId +
            "' class='btn btn-small btn-danger btn-block'><span class='d-flex align-items-center'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 20 20'><path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/></svg> Delete</span></a></td>"
          );
          $("#tabla1 tbody").append(newRow);
        }
      },
    });
  });
});

// ==========================================
// Show More/Less Functions
// ==========================================

function showMore(id) {
  document.getElementById("descripcion-corta-" + id).style.display = "none";
  document.getElementById("descripcion-completa-" + id).style.display = "inline";
  document.querySelector('button[onclick="showMore(' + id + ')"]').style.display = "none";
  document.querySelector('button[onclick="showLess(' + id + ')"]').style.display = "inline";
}

function showLess(id) {
  document.getElementById("descripcion-corta-" + id).style.display = "inline";
  document.getElementById("descripcion-completa-" + id).style.display = "none";
  document.querySelector('button[onclick="showMore(' + id + ')"]').style.display = "inline";
  document.querySelector('button[onclick="showLess(' + id + ')"]').style.display = "none";
}

function deleteProduct() {
  return confirm("Are you sure you want to delete this product?");
}

function goBack() {
  window.history.back();
}

// ==========================================
// Shopping Cart Functions
// ==========================================

function addToCart(productId, productName, price, quantity) {
  if (!quantity || quantity <= 0) {
    alert("Por favor ingrese una cantidad válida");
    return;
  }

  $.ajax({
    type: "POST",
    url: "../controllers/add_product_bill.php",
    data: { id_product: productId, product_name: productName, price_product: price, quantityToAdd: quantity },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al agregar producto al carrito: " + error);
    }
  });
}

function removeFromCart(productId) {
  if (!confirm("¿Está seguro que desea eliminar este producto del carrito?")) return;

  $.ajax({
    type: "POST",
    url: "../controllers/remove_product_cart.php",
    data: { producto_id: productId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al eliminar producto del carrito: " + error);
    }
  });
}

function updateCartTotal(total) {
  $("#cart-total").text("Lps. " + parseFloat(total).toFixed(2));
}

// ==========================================
// Invoice Functions with XSS Protection
// ==========================================

function viewInvoice(facturaId) {
  $.ajax({
    type: "GET",
    url: "../controllers/view_invoice_details.php",
    data: { factura_id: facturaId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        var invoice = response.invoice;
        var items = response.items;

        var modalContent = '<div class="modal fade" id="invoiceModal" tabindex="-1">' +
          '<div class="modal-dialog modal-lg"><div class="modal-content">' +
          '<div class="modal-header"><h5 class="modal-title">Factura #' + escapeHtml(invoice.id_factura) + '</h5>' +
          '<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
          '<div class="modal-body"><div class="row mb-3"><div class="col-md-6">' +
          '<p><strong>Cliente:</strong> ' + escapeHtml(invoice.cliente) + '</p>' +
          '<p><strong>RTN:</strong> ' + (escapeHtml(invoice.rtn) || 'N/A') + '</p>' +
          '<p><strong>Cajero:</strong> ' + escapeHtml(invoice.cajero) + '</p></div>' +
          '<div class="col-md-6"><p><strong>Fecha:</strong> ' + escapeHtml(invoice.fecha_hora) + '</p>' +
          '<p><strong>Estado:</strong> ' + escapeHtml(invoice.estado) + '</p>' +
          '<p><strong>Método de Pago:</strong> ' + escapeHtml(invoice.metodo_pago) + '</p></div></div>' +
          '<h6>Productos:</h6><table class="table table-sm"><thead><tr>' +
          '<th>Producto</th><th class="text-center">Cantidad</th>' +
          '<th class="text-end">Precio Unit.</th><th class="text-end">Subtotal</th></tr></thead><tbody>';

        items.forEach(function (item) {
          modalContent += '<tr><td>' + escapeHtml(item.nombre_producto) + '</td>' +
            '<td class="text-center">' + escapeHtml(item.cantidad) + '</td>' +
            '<td class="text-end">Lps. ' + parseFloat(item.precio_unitario).toFixed(2) + '</td>' +
            '<td class="text-end">Lps. ' + parseFloat(item.subtotal).toFixed(2) + '</td></tr>';
        });

        modalContent += '</tbody><tfoot><tr class="fw-bold"><td colspan="3" class="text-end">TOTAL:</td>' +
          '<td class="text-end">Lps. ' + parseFloat(invoice.total).toFixed(2) + '</td></tr></tfoot></table></div>' +
          '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>' +
          '</div></div></div>';

        $("#invoiceModal").remove();
        $("body").append(modalContent);
        new bootstrap.Modal(document.getElementById("invoiceModal")).show();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al cargar detalles de factura: " + error);
    }
  });
}

function deleteInvoice(facturaId) {
  if (!confirm("¿Está seguro que desea eliminar la factura #" + facturaId + "? Esta acción restaurará el inventario.")) return;

  $.ajax({
    type: "POST",
    url: "../controllers/delete_invoice.php",
    data: { factura_id: facturaId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert(response.message);
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("Error al eliminar factura: " + error);
    }
  });
}

function validateReceiptForm() {
  var cartTotal = $("#cart-total").text().trim();
  var totalValue = parseFloat(cartTotal.replace("Lps.", "").replace(",", "").trim());

  if (isNaN(totalValue) || totalValue <= 0) {
    alert("El carrito está vacío. Por favor agregue productos antes de generar la factura.");
    return false;
  }

  if (!$("[name='name']").val().trim()) { alert("Por favor ingrese el nombre del cliente"); return false; }
  if (!$("#actual-date").val().trim() || $("#actual-date").val() === "Date & Time") { alert("Por favor seleccione la fecha y hora"); return false; }
  if (!$("[name='cashier']").val().trim()) { alert("Por favor ingrese el nombre del cajero"); return false; }
  if (!$("[name='payment_method']").val() || $("[name='payment_method']").val() === "Payment Method") { alert("Por favor seleccione un método de pago"); return false; }

  return true;
}
