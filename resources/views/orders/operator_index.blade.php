@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container mt-5">
        <h2>Order Products</h2>
        <form action="{{ route('operator.store') }}" method="POST">
            @csrf
            <div id="product-list">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <select name="customer" class="form-control">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-5">
                        <select name="products[0][id]" class="form-control product-select">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - Rp {{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="products[0][quantity]" class="form-control quantity-input" placeholder="Quantity" min="1">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-product">X</button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-product" class="btn btn-success mb-3">Add Product</button>
            
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="text" id="total" name="total" class="form-control" readonly>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit Order</button>
        </form>
    </div>
    
    <div class="container mt-5 mb-5">
        <h2>Order List</h2>
        <table class="table table-bordered table-hover table-striped" id="orders-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Date Order</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:right">Total:</th>
                    <th id="total-footer"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- <div class="container mt-5 mb-5">
        <h2>Total Per Hari Order</h2>
        <table class="table table-bordered table-hover table-striped" id="total-amount">
            <thead>
                <tr>
                    <th colspan="5">Total Amount</th>
                    <th colspan="2">Rp {{ $totalAmountToday }}</th>
                </tr>
            </thead>
        </table>
    </div> --}}

    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("operator") }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'product_category', name: 'product_category' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'price', name: 'price' },
                    { data: 'subtotal', name: 'subtotal' },
                    { data: 'created_at', name: 'created_at' },
                ],
                order: [[7, 'desc']], 
                pageLength: 10,       
                responsive: true,
                searching: true,      
                lengthChange: true, 
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    
                    // Calculate the total for the current page
                    var total = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return a + parseFloat(b.replace(/[\Rp,]/g, '') || 0);
                        }, 0);
                    
                    // Update footer
                    $(api.column(6).footer()).html(
                        'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2 })
                    );
                }
            });
        });

        $(document).ready(function() {
            var productIndex = 1;
    
            function calculateSubtotal(element) {
                var quantity = $(element).closest('.row').find('.quantity-input').val();
                var price = $(element).closest('.row').find('.product-select option:selected').data('price');
                var subtotal = quantity * price;
                $(element).closest('.row').find('.subtotal').val(subtotal);
                calculateTotal();
            }
    
            function calculateTotal() {
                var total = 0;
                $('.subtotal').each(function() {
                    var subtotal = parseFloat($(this).val()) || 0;
                    total += subtotal;
                });
                $('#total').val(total);
            }
    
            $('#product-list').on('change', '.product-select', function() {
                var price = $(this).find('option:selected').data('price');
                console.log(price);
                $(this).closest('.row').find('.quantity-input').attr('data-price', price);
                calculateSubtotal(this);
            });
    
            $('#product-list').on('input', '.quantity-input', function() {
                calculateSubtotal(this);
            });
    
            $('#product-list').on('click', '.remove-product', function() {
                $(this).closest('.row').remove();
                calculateTotal();
            });
    
            $('#add-product').click(function() {
                var newProductRow = `
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <select name="products[${productIndex}][id]" class="form-control product-select">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} - ${{ $product->price }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="products[${productIndex}][quantity]" class="form-control quantity-input" placeholder="Quantity" min="1">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-product">X</button>
                        </div>
                    </div>`
                ;
                $('#product-list').append(newProductRow);
                productIndex++;
            });
        });

    </script>
</div>
@endsection
