@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Operator Report</h2>

    <form action="{{ route('report') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}">
            </div>
            <div class="col-md-4">
                <label for="type" class="form-label">Report Type:</label>
                <select id="type" name="type" class="form-control">
                    <option value="day" {{ request('type') == 'day' ? 'selected' : '' }}>Daily</option>
                    <option value="week" {{ request('type') == 'week' ? 'selected' : '' }}>Weekly</option>
                    <option value="month" {{ request('type') == 'month' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mt-2">Generate Report</button>
            </div>
        </div>
    </form>

    <div class="report-section">
        <h3>Report for {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</h3>
        <ul>
            <li>Total : Rp {{ number_format($totalAmount->total, 2) }}</li>
        </ul>
    </div>

    <div class="report-section mt-4">
        <h3>Items Sold</h3>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemsSold as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->quantity_sold }}</td>
                    <td>Rp {{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>{{ $totalQuantity }}</th>
                    <th>Rp {{ number_format($totalPrice, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
