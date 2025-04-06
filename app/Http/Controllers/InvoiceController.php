<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Auth::user()->invoices(true);
        // dd($invoices);


        return view('invoices', get_defined_vars());
    }

    public function download($invoiceID)
    {
        return Auth::user()->downloadInvoice($invoiceID);
    }
}
