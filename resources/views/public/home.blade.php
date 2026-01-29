<x-layouts.guest>
    <div class="bg-white">
        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">A Community That</span>
                                <span class="block text-indigo-600 xl:inline">Grows Together</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Join CIPHER, where we pool resources to fund real projects and share the rewards through trust and transparency.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg">
                                        Join the Community
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg">
                                        Sign In
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- How it Works Section -->
        <div id="how-it-works" class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Flow</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        How it Works
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                        Simple, transparent, and rewarding.
                    </p>
                </div>

                <div class="mt-10">
                    <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-4 md:gap-x-8 md:gap-y-10">
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white text-xl font-bold">1</div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Subscribe</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Join a plan that suits you.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white text-xl font-bold">2</div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Fund Pool</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Contributions are pooled together.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white text-xl font-bold">3</div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Projects</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Funds are allocated to real projects.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white text-xl font-bold">4</div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Rewards</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Value generated is shared back.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Plans Section -->
        <div class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Simple, Transparent Pricing
                    </h2>
                </div>
                <div class="mt-10 grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($plans as $plan)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
                            <div class="px-6 py-8">
                                <h3 class="text-2xl font-bold text-gray-900 text-center">{{ $plan->name }}</h3>
                                <p class="mt-4 text-center text-5xl font-extrabold text-gray-900">${{ number_format($plan->price, 0) }}</p>
                                <p class="mt-1 text-center text-sm text-gray-500">/ {{ $plan->interval }}</p>
                                <p class="mt-6 text-center text-gray-500">{{ $plan->description }}</p>
                            </div>
                            <div class="px-6 py-6 bg-gray-50">
                                <a href="{{ route('register') }}" class="block w-full text-center bg-indigo-600 border border-transparent rounded-md py-3 text-base font-medium text-white hover:bg-indigo-700">
                                    Select {{ $plan->name }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center text-gray-500">
                            No plans currently public.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Trust -->
        <div class="bg-gray-900 py-12">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                 <h2 class="text-3xl font-extrabold text-white">Trust. Transparency. Value.</h2>
                 <p class="mt-4 text-xl text-gray-400">The core pillars of the CIPHER community.</p>
             </div>
        </div>
    </div>
</x-layouts.guest>
