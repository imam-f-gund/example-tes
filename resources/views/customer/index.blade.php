@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Customer List</h1>
    <!-- Button trigger modal -->
    <button class="btn btn-sm btn-primary mb-3" id="addcustomer" data-toggle="modal" data-target="#addcustomerModal">Add Customer</button>

    <table class="table table-bordered table-hover table-striped table-responsive-md mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>
                    
                    <button class="btn btn-sm btn-warning" onclick="edit('{{$customer->id}}|{{$customer->name}}|{{$customer->email}}')" data-val="">Edit</button>

                    <!-- Delete Button trigger modal -->
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deletecustomerModal{{ $customer->id }}">Delete</button>
            
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deletecustomerModal{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="deletecustomerModalLabel{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deletecustomerModalLabel{{ $customer->id }}">Delete Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this customer?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
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
    <div class="modal fade" id="addcustomerModal" tabindex="-1" role="dialog" aria-labelledby="addcustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="addcustomerModalLabel">Add New customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">customer Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
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
    var email = dataArr[2];

    // set data to modal
    $('#name').val(name);
    $('#email').val(email);
    
    // show modal addcustomerModalLabel
    $('#addcustomerModalLabel').text('Edit customer');
    $('#addcustomerModal form').attr('action', 'customers/'+id);
    $('#addcustomerModal form').append('<input type="hidden" name="_method" value="PUT">');
    $('#addcustomerModal').modal('show');
    
};

// addcustomer click
$('#addcustomer').click(function(){
    // set data to modal
    $('#name').val('');
    $('#email').val('');
    
    // show modal addcustomerModalLabel
    $('#addcustomerModalLabel').text('Add New customer');
    $('#addcustomerModal form').attr('action', 'customers');
    $('#addcustomerModal form').find('input[name="_method"]').remove();
    $('#addcustomerModal').modal('show');
});
</script>
@endsection
