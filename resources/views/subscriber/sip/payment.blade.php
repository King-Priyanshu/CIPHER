@extends('subscriber.layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>SIP Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Payment Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>SIP Plan:</strong> {{ $sip->investmentPlan->name }}</p>
                                    <p><strong>Amount:</strong> ₹{{ number_format($paymentSchedule->amount, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Payment Date:</strong> {{ $paymentSchedule->payment_date->format('d M Y') }}</p>
                                    <p><strong>Status:</strong> <span class="badge badge-warning">{{ ucfirst($paymentSchedule->status) }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Payment Options</h5>
                            <div class="alert alert-info">
                                Please select your preferred payment method. You will be redirected to the payment gateway.
                            </div>
                            
                            <form id="payment-form" action="{{ route('subscriber.sip.verify') }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_id" value="{{ $paymentSchedule->id }}">
                                <input type="hidden" name="transaction_id" id="transaction-id">
                                <input type="hidden" name="amount" value="{{ $paymentSchedule->amount }}">
                                
                                <div class="form-group">
                                    <label for="payment-method">Payment Method</label>
                                    <select id="payment-method" class="form-control" required>
                                        <option value="">Select payment method</option>
                                        <option value="razorpay">Razorpay</option>
                                        <option value="stripe">Stripe</option>
                                        <option value="paytm">PayTM</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="card-number">Card Number</label>
                                    <input type="text" id="card-number" class="form-control" placeholder="1234 5678 9010 1112" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="expiry-date">Expiry Date</label>
                                        <input type="text" id="expiry-date" class="form-control" placeholder="MM/YY" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" class="form-control" placeholder="123" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    Pay ₹{{ number_format($paymentSchedule->amount, 2) }}
                                </button>
                            </form>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('subscriber.sip.show', $sip->id) }}" class="btn btn-secondary">
                                Back to SIP Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulate payment gateway integration
            const paymentMethod = document.getElementById('payment-method').value;
            const transactionId = 'txn_' + Math.random().toString(36).substr(2, 9) + Date.now();
            
            // Show loading indicator
            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Processing Payment...';
            submitButton.disabled = true;
            
            // Simulate payment processing delay
            setTimeout(() => {
                document.getElementById('transaction-id').value = transactionId;
                e.target.submit();
            }, 1500);
        });
    </script>
@endsection