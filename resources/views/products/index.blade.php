<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(90deg, rgba(93,12,255,1) 0%, rgba(155,50,255,1) 35%, rgba(253,29,29,1) 100%);
            color: white;
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(90deg, rgba(93,12,255,1) 0%, rgba(155,50,255,1) 35%, rgba(253,29,29,1) 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, rgba(93,12,255,1) 0%, rgba(155,50,255,1) 35%, rgba(253,29,29,1) 100%);
            opacity: 0.8;
        }
        .table th {
            background: linear-gradient(90deg, rgba(93,12,255,1) 0%, rgba(155,50,255,1) 35%, rgba(253,29,29,1) 100%);
            color: white;
        }
        .edit-product {
            background-color: #28a745;
            border: none;
        }
        .edit-product:hover {
            background-color: #218838;
        }
        .delete-product {
            background-color: #dc3545;
            border: none;
        }
        .delete-product:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h1>Product Management</h1>
        </div>
        <div class="card-body">
            <form id="product-form" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="quantity">Quantity in stock</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="price">Price per item</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter price">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>

            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity in stock</th>
                        <th>Price per item</th>
                        <th>Datetime submitted</th>
                        <th>Total value number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    @foreach($products as $product)
                        <tr data-id="{{ $product['id'] }}">
                            <td class="name">{{ $product['name'] }}</td>
                            <td class="quantity">{{ $product['quantity'] }}</td>
                            <td class="price">{{ $product['price'] }}</td>
                            <td>{{ $product['created_at'] }}</td>
                            <td class="total-value">{{ $product['quantity'] * $product['price'] }}</td>
                            <td>
                                <button class="btn btn-sm edit-product text-white">Edit</button>
                                <button class="btn btn-sm bg-success save-product text-white d-none">Save</button>
                                <button class="btn btn-sm delete-product text-white">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"><strong>Total:</strong></td>
                        <td id="total-value">
                            {{ collect($products)->sum(function($product) { return $product['quantity'] * $product['price']; }) }}
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product-form').on('submit', function(e) {
            e.preventDefault();

            let formData = {
                name: $('#name').val(),
                quantity: $('#quantity').val(),
                price: $('#price').val(),
            };

            $.ajax({
                url: '/products',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(product) {
                    $('#product-list').prepend(`
                        <tr data-id="${product.id}">
                            <td class="name">${product.name}</td>
                            <td class="quantity">${product.quantity}</td>
                            <td class="price">${product.price}</td>
                            <td>${product.created_at}</td>
                            <td class="total-value">${product.quantity * product.price}</td>
                            <td>
                                <button class="btn btn-sm edit-product text-white">Edit</button>
                                <button class="btn btn-sm bg-success save-product text-white d-none">Save</button>
                                <button class="btn btn-sm delete-product text-white">Delete</button>
                            </td>
                        </tr>
                    `);
                    updateTotalValue();
                }
            });
        });

        $('#product-list').on('click', '.edit-product', function() {
            let row = $(this).closest('tr');
            row.find('.name').html(`<input type="text" class="form-control" value="${row.find('.name').text()}">`);
            row.find('.quantity').html(`<input type="number" class="form-control" value="${row.find('.quantity').text()}">`);
            row.find('.price').html(`<input type="number" step="0.01" class="form-control" value="${row.find('.price').text()}">`);
            row.find('.edit-product').addClass('d-none');
            row.find('.save-product').removeClass('d-none');
        });

        $('#product-list').on('click', '.save-product', function() {
            let row = $(this).closest('tr');
            let id = row.data('id');
            let formData = {
                name: row.find('.name input').val(),
                quantity: row.find('.quantity input').val(),
                price: row.find('.price input').val(),
            };

            $.ajax({
                url: `/products/${id}`,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(product) {
                    row.find('.name').html(product.name);
                    row.find('.quantity').html(product.quantity);
                    row.find('.price').html(product.price);
                    row.find('.total-value').html(product.quantity * product.price);
                    row.find('.edit-product').removeClass('d-none');
                    row.find('.save-product').addClass('d-none');
                    updateTotalValue();
                }
            });
        });

        $('#product-list').on('click', '.delete-product', function() {
            let row = $(this).closest('tr');
            let id = row.data('id');

            $.ajax({
                url: `/products/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    row.remove();
                    updateTotalValue();
                }
            });
        });

        function updateTotalValue() {
            let total = 0;
            $('#product-list tr').each(function() {
                let value = parseFloat($(this).find('.total-value').text());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            $('#total-value').text(total.toFixed(2));
        }
    });
</script>
</body>
</html>
