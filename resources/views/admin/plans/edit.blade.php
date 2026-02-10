@extends('components.layouts.admin')

@section('page_title', 'Edit Investment Plan')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Edit Investment Plan</h3>
            
            <form action="{{ route('admin.plans.update', $investmentPlan) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-navy mb-1.5">Project</label>
                        <select name="project_id" id="project_id" required class="input-field">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $investmentPlan->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-navy mb-1.5">Plan Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $investmentPlan->name) }}" required
                            class="input-field" placeholder="Plan Name">
                        @error('name')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-navy mb-1.5">Plan Type</label>
                        <select name="type" id="type" required class="input-field">
                            <option value="onetime" {{ $investmentPlan->type == 'onetime' ? 'selected' : '' }}>One-time Investment</option>
                            <option value="sip" {{ $investmentPlan->type == 'sip' ? 'selected' : '' }}>SIP (Systematic Investment Plan)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="frequency" class="block text-sm font-medium text-navy mb-1.5">Payment Frequency</label>
                        <select name="frequency" id="frequency" class="input-field">
                            <option value="monthly" {{ $investmentPlan->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ $investmentPlan->frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="yearly" {{ $investmentPlan->frequency == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('frequency')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="min_investment" class="block text-sm font-medium text-navy mb-1.5">Minimum Investment (₹)</label>
                        <input type="number" name="min_investment" id="min_investment" value="{{ old('min_investment', $investmentPlan->min_investment) }}" step="0.01" min="0" required
                            class="input-field font-numbers" placeholder="0.00">
                        @error('min_investment')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_investment" class="block text-sm font-medium text-navy mb-1.5">Maximum Investment (₹)</label>
                        <input type="number" name="max_investment" id="max_investment" value="{{ old('max_investment', $investmentPlan->max_investment) }}" step="0.01" min="0"
                            class="input-field font-numbers" placeholder="0.00">
                        @error('max_investment')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_months" class="block text-sm font-medium text-navy mb-1.5">Duration (Months)</label>
                        <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months', $investmentPlan->duration_months) }}" min="1"
                            class="input-field font-numbers">
                        @error('duration_months')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expected_return_percentage" class="block text-sm font-medium text-navy mb-1.5">Expected Return (%)</label>
                        <input type="number" name="expected_return_percentage" id="expected_return_percentage" value="{{ old('expected_return_percentage', $investmentPlan->expected_return_percentage) }}" step="0.1" min="0"
                            class="input-field font-numbers" placeholder="0.0">
                        @error('expected_return_percentage')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="refund_rule" class="block text-sm font-medium text-navy mb-1.5">Refund Policy</label>
                        <select name="refund_rule" id="refund_rule" required class="input-field">
                            <option value="full" {{ $investmentPlan->refund_rule == 'full' ? 'selected' : '' }}>Full Refund</option>
                            <option value="partial" {{ $investmentPlan->refund_rule == 'partial' ? 'selected' : '' }}>Partial Refund</option>
                            <option value="none" {{ $investmentPlan->refund_rule == 'none' ? 'selected' : '' }}>No Refund</option>
                        </select>
                        @error('refund_rule')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $investmentPlan->is_active ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-teal focus:ring-teal">
                        <label for="is_active" class="ml-2 block text-sm text-navy">Active</label>
                    </div>
                </div>

                <!-- Tier Configuration -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-navy mb-4">Tier Configuration</h4>
                    <div id="tiers-container" class="space-y-4">
                        @foreach($investmentPlan->tiers as $index => $tier)
                        <div class="tier-item bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-navy mb-1">Tier Name</label>
                                    <input type="text" name="tiers[{{ $index }}][name]" value="{{ old('tiers.'.$index.'.name', $tier['name']) }}"
                                           class="input-field">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-navy mb-1">Min Amount (₹)</label>
                                    <input type="number" name="tiers[{{ $index }}][min_amount]" value="{{ old('tiers.'.$index.'.min_amount', $tier['min_amount']) }}"
                                           class="input-field font-numbers">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-navy mb-1">Max Amount (₹)</label>
                                    <input type="text" name="tiers[{{ $index }}][max_amount]" value="{{ old('tiers.'.$index.'.max_amount', $tier['max_amount']) }}"
                                           class="input-field" placeholder="Unlimited">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-navy mb-1">ROI (%)</label>
                                    <input type="number" name="tiers[{{ $index }}][roi]" value="{{ old('tiers.'.$index.'.roi', $tier['roi']) }}"
                                           class="input-field font-numbers">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-navy mb-1">Benefits</label>
                                <div class="space-y-2">
                                    @foreach($tier['benefits'] as $benefitIndex => $benefit)
                                    <div class="flex items-center">
                                        <input type="text" name="tiers[{{ $index }}][benefits][{{ $benefitIndex }}]" value="{{ old('tiers.'.$index.'.benefits.'.$benefitIndex, $benefit) }}"
                                               class="input-field flex-1 mr-2">
                                        <button type="button" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                    <button type="button" class="add-benefit text-blue-500 hover:text-blue-700 text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Benefit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="button" id="add-tier" class="text-blue-500 hover:text-blue-700 text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Tier
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-navy mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="input-field">{{ old('description', $investmentPlan->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.plans.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tiersContainer = document.getElementById('tiers-container');
    const addTierButton = document.getElementById('add-tier');
    let tierIndex = {{ count($investmentPlan->tiers) }};

    // Add benefit button functionality
    document.querySelectorAll('.add-benefit').forEach(button => {
        button.addEventListener('click', function() {
            const tierItem = this.closest('.tier-item');
            const benefitsContainer = tierItem.querySelector('.space-y-2');
            const tierIndex = Array.from(tiersContainer.children).indexOf(tierItem);
            const benefitsIndex = benefitsContainer.querySelectorAll('input[type="text"]').length;

            const newBenefit = document.createElement('div');
            newBenefit.className = 'flex items-center';
            newBenefit.innerHTML = `
                <input type="text" name="tiers[${tierIndex}][benefits][${benefitsIndex}]" value=""
                       class="input-field flex-1 mr-2">
                <button type="button" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            benefitsContainer.insertBefore(newBenefit, this);

            // Add delete button functionality
            newBenefit.querySelector('button').addEventListener('click', function() {
                newBenefit.remove();
            });
        });
    });

    // Add tier button functionality
    addTierButton.addEventListener('click', function() {
        const newTier = document.createElement('div');
        newTier.className = 'tier-item bg-gray-50 p-4 rounded-lg';
        newTier.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy mb-1">Tier Name</label>
                    <input type="text" name="tiers[${tierIndex}][name]" value=""
                           class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy mb-1">Min Amount (₹)</label>
                    <input type="number" name="tiers[${tierIndex}][min_amount]" value=""
                           class="input-field font-numbers">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy mb-1">Max Amount (₹)</label>
                    <input type="text" name="tiers[${tierIndex}][max_amount]" value=""
                           class="input-field" placeholder="Unlimited">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy mb-1">ROI (%)</label>
                    <input type="number" name="tiers[${tierIndex}][roi]" value=""
                           class="input-field font-numbers">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-navy mb-1">Benefits</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="text" name="tiers[${tierIndex}][benefits][0]" value=""
                               class="input-field flex-1 mr-2">
                        <button type="button" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="add-benefit text-blue-500 hover:text-blue-700 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Benefit
                    </button>
                </div>
            </div>
        `;
        tiersContainer.appendChild(newTier);

        // Add event listeners to new benefit buttons
        newTier.querySelector('.add-benefit').addEventListener('click', function() {
            const benefitsContainer = newTier.querySelector('.space-y-2');
            const benefitsIndex = benefitsContainer.querySelectorAll('input[type="text"]').length;

            const newBenefit = document.createElement('div');
            newBenefit.className = 'flex items-center';
            newBenefit.innerHTML = `
                <input type="text" name="tiers[${tierIndex}][benefits][${benefitsIndex}]" value=""
                       class="input-field flex-1 mr-2">
                <button type="button" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            benefitsContainer.insertBefore(newBenefit, this);

            newBenefit.querySelector('button').addEventListener('click', function() {
                newBenefit.remove();
            });
        });

        // Add delete button functionality to existing benefits
        newTier.querySelectorAll('.text-red-500').forEach(button => {
            button.addEventListener('click', function() {
                const benefitItem = this.closest('.flex.items-center');
                benefitItem.remove();
            });
        });

        tierIndex++;
    });

    // Delete benefit buttons functionality
    document.querySelectorAll('.text-red-500').forEach(button => {
        button.addEventListener('click', function() {
            const benefitItem = this.closest('.flex.items-center');
            benefitItem.remove();
        });
    });
});
</script>
@endpush
