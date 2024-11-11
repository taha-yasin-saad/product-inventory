<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Product List</h2>
        <form id="productForm" class="mb-3">
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" id="productName" name="product_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity in Stock</label>
                <input type="number" id="quantity" name="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">Price per Item</label>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Product</button>
        </form>

        <h3>Products</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>Datetime Submitted</th>
                    <th>Total Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                @foreach($products as $product)
                    <tr data-id="{{ $loop->index }}">
                        <td class="product_name">{{ $product['product_name'] }}</td>
                        <td class="quantity">{{ $product['quantity'] }}</td>
                        <td class="price">{{ $product['price'] }}</td>
                        <td class="datetime">{{ $product['datetime'] }}</td>
                        <td class="total_value">{{ $product['total_value'] }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="{{ $loop->index }}">Edit</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>All Total Values:</strong></td>
                    <td id="totalValueSum" class="font-weight-bold">{{ $totalValue ?? 0 }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="editModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" id="editProductName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editQuantity">Quantity</label>
                        <input type="number" id="editQuantity" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editPrice">Price</label>
                        <input type="number" id="editPrice" class="form-control">
                    </div>
                    <button id="saveChangesBtn" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Include CSRF token in AJAX setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Submit new product
            $('#productForm').submit(function (event) {
                event.preventDefault();
                var productName = $('#productName').val();
                var quantity = $('#quantity').val();
                var price = $('#price').val();

                $.ajax({
                    url: '/store-product',
                    method: 'POST',
                    data: {
                        product_name: productName,
                        quantity: quantity,
                        price: price
                    },
                    success: function (response) {
                        var newRow = `<tr data-id="${response.index}">
                            <td class="product_name">${response.product_name}</td>
                            <td class="quantity">${response.quantity}</td>
                            <td class="price">${response.price}</td>
                            <td class="datetime">${response.datetime}</td>
                            <td class="total_value">${response.total_value}</td>
                            <td><button class="btn btn-warning btn-sm edit-btn" data-id="${response.index}">Edit</button></td>
                        </tr>`;
                        $('#productTable').append(newRow);
                        updateTotalValue();
                    }
                });
            });

            // Edit button click event
            $(document).on('click', '.edit-btn', function () {
                var row = $(this).closest('tr');
                var productId = $(this).data('id');
                var productName = row.find('.product_name').text();
                var quantity = row.find('.quantity').text();
                var price = row.find('.price').text();

                // Fill modal fields with current data
                $('#editProductName').val(productName);
                $('#editQuantity').val(quantity);
                $('#editPrice').val(price);

                $('#saveChangesBtn').data('id', productId);

                // Show modal
                $('#editModal').modal('show');
            });

            // Save changes
            $('#saveChangesBtn').click(function () {
                var productId = $(this).data('id');
                var updatedProductName = $('#editProductName').val();
                var updatedQuantity = $('#editQuantity').val();
                var updatedPrice = $('#editPrice').val();

                $.ajax({
                    url: '/edit-product/' + productId,
                    method: 'POST',
                    data: {
                        product_name: updatedProductName,
                        quantity: updatedQuantity,
                        price: updatedPrice
                    },
                    success: function (response) {
                        // Update the product row with new data
                        var row = $('tr[data-id="' + productId + '"]');
                        row.find('.product_name').text(response.product_name);
                        row.find('.quantity').text(response.quantity);
                        row.find('.price').text(response.price);
                        row.find('.total_value').text(response.total_value);

                        // Update total value sum
                        updateTotalValue();

                        // Hide the modal
                        $('#editModal').modal('hide');
                    }
                });
            });

            // Function to calculate and update the total value sum
            function updateTotalValue() {
                let totalValue = 0;

                // Iterate through all rows and sum the total values
                $('#productTable tr').each(function () {
                    var totalValueCell = $(this).find('.total_value');
                    if (totalValueCell.length) {
                        totalValue += parseFloat(totalValueCell.text()) || 0;
                    }
                });

                // Update the total value sum in the footer
                $('#totalValueSum').text(totalValue.toFixed(2));
            }

            // Initial total value calculation on page load
            updateTotalValue();
        });
    </script>
</body>
</html>
