<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvestmentPlan;

class SipController extends Controller
{
    public function index()
    {
        $sips = auth()->user()->sips()->with('investmentPlan')->get();
        $upcomingPayments = 0;
        $calendarEvents = [];

        foreach ($sips as $sip) {
            foreach ($sip->paymentSchedule as $payment) {
                if ($payment->status === 'pending' && $payment->payment_date->isFuture()) {
                    $upcomingPayments++;
                    $calendarEvents[] = [
                        'title' => '₹' . $payment->amount,
                        'start' => $payment->payment_date->toDateString(),
                        'extendedProps' => [
                            'status' => $payment->status,
                            'amount' => $payment->amount
                        ]
                    ];
                }
            }
        }

        return view('subscriber.sip.index', [
            'sips' => $sips,
            'upcomingPayments' => $upcomingPayments,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    public function create()
    {
        $investmentPlans = InvestmentPlan::active()->get();

        return view('subscriber.sip.create', [
            'investmentPlans' => $investmentPlans,
        ]);
    }

    public function store(Request $request)
    {
        // Validate SIP request
        $validated = $request->validate([
            'investment_plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:100',
            'frequency' => 'required|in:weekly,monthly',
            'start_date' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:3|max:60',
            'auto_pay' => 'boolean',
        ]);

        // Create SIP enrollment
        $sip = auth()->user()->sips()->create([
            'investment_plan_id' => $validated['investment_plan_id'],
            'amount' => $validated['amount'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'duration' => $validated['duration'],
            'auto_pay' => $validated['auto_pay'] ?? false,
            'status' => 'active',
        ]);

        // Generate payment schedule
        $sip->generatePaymentSchedule();

        return redirect()->route('subscriber.sip.show', $sip)
            ->with('success', 'SIP enrollment successful!');
    }

    public function show($id)
    {
        $sip = auth()->user()->sips()->with('investmentPlan', 'paymentSchedule')->findOrFail($id);

        return view('subscriber.sip.show', [
            'sip' => $sip,
        ]);
    }

    public function edit($id)
    {
        $sip = auth()->user()->sips()->findOrFail($id);
        $investmentPlans = InvestmentPlan::active()->get();

        return view('subscriber.sip.edit', [
            'sip' => $sip,
            'investmentPlans' => $investmentPlans,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sip = auth()->user()->sips()->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'frequency' => 'required|in:weekly,monthly',
            'duration' => 'required|integer|min:3|max:60',
            'auto_pay' => 'boolean',
        ]);

        $sip->update($validated);

        return redirect()->route('subscriber.sip.show', $sip)
            ->with('success', 'SIP updated successfully!');
    }

    public function cancel($id)
    {
        $sip = auth()->user()->sips()->findOrFail($id);
        $sip->update(['status' => 'cancelled']);

        return redirect()->route('subscriber.sip.index')
            ->with('success', 'SIP cancelled successfully!');
    }

    public function paymentSchedule($id)
    {
        $sip = auth()->user()->sips()->with('paymentSchedule')->findOrFail($id);

        return view('subscriber.sip.payment-schedule', [
            'sip' => $sip,
        ]);
    }

    public function payment($id)
    {
        $paymentSchedule = \App\Models\SipPaymentSchedule::with('sip.investmentPlan')
            ->whereHas('sip', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        return view('subscriber.sip.payment', [
            'paymentSchedule' => $paymentSchedule,
            'sip' => $paymentSchedule->sip
        ]);
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:sip_payment_schedules,id',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        $paymentSchedule = \App\Models\SipPaymentSchedule::with('sip')
            ->whereHas('sip', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($validated['payment_id']);

        // Verify payment with payment gateway (Razorpay/Stripe)
        // For now, we'll assume payment is successful

        $paymentSchedule->update([
            'status' => 'completed',
            'transaction_id' => $validated['transaction_id'],
            'paid_at' => now()
        ]);

        return redirect()->route('subscriber.sip.show', $paymentSchedule->sip_id)
            ->with('success', 'SIP payment verified successfully!');
    }
}
