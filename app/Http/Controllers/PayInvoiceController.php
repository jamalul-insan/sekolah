<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayInvoiceRequest;
use App\Models\Invoice;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Invoice $invoice)
    {
        // dd($invoice->invoice_details);
        if ($invoice->school_id != session('school_id')) abort(404);

        // cek status dan kembalikan jika statusnya masih DRAFT
        if ($invoice->is_posted == Invoice::POSTED_DRAFT)
        return redirect()->back()->withToastError('Ups! Pembayaran tidak bisa dilakukan. Invoice harus diterbitkan dahulu.');

        // cek pembayaran dan kembalikan jika pembayarannya sudah LUNAS
        if ($invoice->payment_status == Invoice::STATUS_PAID)
        return redirect()->back()->withToastError('Ups! Invoice sudah dinyatakan lunas.');

        // cek harus memiliki invoice_details
        if (count($invoice->invoice_details) == 0)
            return redirect()->back()->withToastError('Ups! Invoice belum memiliki baris data.');

        $data['title'] = "Invoice " . $invoice->invoice_number;
        $data['details'] = $invoice->invoice_details()->orderBy('id')->get();
        $data['invoice'] = $invoice;
        $data['wallets'] = Wallet::all();
        return view('pages.invoices.payment.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PayInvoiceRequest $request, Invoice $invoice)
    {
        if ($invoice->school_id != session('school_id')) abort(404);

        // cek status dan kembalikan jika statusnya bukan PUBLISHED
        if ($invoice->is_posted != Invoice::POSTED_PUBLISHED)
            return redirect()->back()->withToastError('Ups! Pembayaran tidak bisa dilakukan. Invoice harus diterbitkan dahulu.');

        // cek pembayaran dan kembalikan jika pembayarannya sudah LUNAS
        if ($invoice->payment_status == Invoice::STATUS_PAID)
            return redirect()->back()->withToastError('Ups! Invoice sudah dinyatakan lunas');

        DB::beginTransaction();
        try {
            $inv_details = $invoice->invoice_details()->orderBy('id')->get();

            foreach ($inv_details as $key => $inv_detail) {
                $inv_detail->wallet_id = $request->wallet_id[$key];
                $inv_detail->save();
            }

            $invoice->payment_status = Invoice::STATUS_PAID;
            $invoice->save();
            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'action' => 'Publish invoice',
                'user' => auth()->user()->name,
                'invoice' => $invoice
            ]);
            DB::rollback();
            return redirect()->back()->withToastError('Ups, terjadi kesalahan saat publish invoice!');
        }
        return to_route('invoices.index')->withToastSuccess('Invoice berhasil dibayarkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
