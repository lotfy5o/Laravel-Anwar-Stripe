<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 row">

            @if (count($invoices) > 0)
                <table class="table">
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td> {{ $invoice->date()->toFormattedDateString() }} </td>
                            <td> {{ $invoice->total() }} </td>


                            <td> <a href="{{ route('download', ['invoiceID' => $invoice->id]) }}">
                                    Download
                                </a> </td>


                            {{-- <td> <a href="{{ $invoice->invoice_pdf }}"> Download
                                </a> </td> --}}


                            {{-- <td> <a href="{{ $invoice->hosted_invoice_url }}"> Download
                                </a> </td> --}}
                        </tr>
                    @endforeach
                </table>
            @endif




        </div>
    </div>
</x-app-layout>
