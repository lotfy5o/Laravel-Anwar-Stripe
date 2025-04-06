<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Cancel / Resume') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Controll Billing Details and Subscriptions') }}
        </p>
    </header>


    <a href="{{ route('cancel') }}" class="btn btn-sm btn-danger">Cancel</a>
    <a href="{{ route('cancel.now') }}" class="btn btn-sm btn-danger">Cancel Now</a>
    <a href="{{ route('resume') }}" class="btn btn-sm btn-success">Resume</a>


</section>
