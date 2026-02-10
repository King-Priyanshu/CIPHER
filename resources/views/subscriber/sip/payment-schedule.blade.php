@extends('subscriber.layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>SIP Payment Schedule</h4>
                            <a href="{{ route('subscriber.sip.show', $sip->id) }}" class="btn btn-sm btn-secondary">
                                Back to SIP Details
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>SIP Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Investment Plan:</strong> {{ $sip->investmentPlan->name }}</p>
                                    <p><strong>Amount per Installment:</strong> ₹{{ number_format($sip->amount, 2) }}</p>
                                    <p><strong>Frequency:</strong> {{ ucfirst($sip->frequency) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Start Date:</strong> {{ $sip->start_date->format('d M Y') }}</p>
                                    <p><strong>Duration:</strong> {{ $sip->duration }} months</p>
                                    <p><strong>Status:</strong> <span class="badge badge-{{ $sip->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($sip->status) }}
                                    </span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Payment Schedule</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Installment</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sip->paymentSchedule as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $payment->status === 'completed' ? 'success' : 
                                                    $payment->status === 'pending' ? 'warning' : 'danger'
                                                }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment->status === 'pending' && $payment->payment_date->isPast())
                                                    <span class="text-muted">Overdue</span>
                                                @elseif($payment->status === 'pending' && $payment->payment_date->isFuture())
                                                    <span class="text-muted">Upcoming</span>
                                                @elseif($payment->status === 'pending')
                                                    <a href="{{ route('subscriber.sip.payment', $payment->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        Pay Now
                                                    </a>
                                                @elseif($payment->status === 'completed')
                                                    <a href="#" class="btn btn-sm btn-success disabled">
                                                        Paid
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($sip->auto_pay)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Auto-pay is enabled for this SIP. Payments will be automatically debited from your registered account.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Auto-pay is disabled. You will need to manually pay each installment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
    </style>
@endsection