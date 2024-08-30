@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product List</h1>
    <!-- Button trigger modal -->
    <button class="btn btn-sm btn-primary mb-3" id="addProduct" data-toggle="modal" data-target="#addProductModal">Add Product</button>

    <table class="table table-bordered table-hover table-striped table-responsive-md mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>Rp {{ $product->price }}</td>
                <td>{{ $product->category }}</td>
                <td>
                    
                    <button class="btn btn-sm btn-warning" onclick="edit('{{$product->id}}|{{$product->name}}|{{$product->description}}|{{$product->price}}|{{$product->category}}')" data-val="">Edit</button>

                    <!-- Delete Button trigger modal -->
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteProductModal{{ $product->id }}">Delete</button>
            
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel{{ $product->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteProductModalLabel{{ $product->id }}">Delete Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this product?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                            </div>
                        </div>
                        </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
            </div>
        </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function() {
    $('.table').DataTable();
});
// edit click
function edit(val) {
    // get data value
    var data = val;
    var dataArr = data.split('|');
    var id = dataArr[0];
    var name = dataArr[1];
    var description = dataArr[2];
    var price = dataArr[3];
    var category = dataArr[4];

    // set data to modal
    $('#name').val(name);
    $('#description').val(description);
    $('#price').val(price);
    $('#category').val(category);
    
    // show modal addProductModalLabel
    $('#addProductModalLabel').text('Edit Product');
    $('#addProductModal form').attr('action', 'products/'+id);
    $('#addProductModal form').append('<input type="hidden" name="_method" value="PUT">');
    $('#addProductModal').modal('show');
    
};

// addProduct click
$('#addProduct').click(function(){
    // set data to modal
    $('#name').val('');
    $('#description').val('');
    $('#price').val('');
    $('#category').val('');
    
    // show modal addProductModalLabel
    $('#addProductModalLabel').text('Add New Product');
    $('#addProductModal form').attr('action', 'products');
    $('#addProductModal form').find('input[name="_method"]').remove();
    $('#addProductModal').modal('show');
});
</script>
@endsection
