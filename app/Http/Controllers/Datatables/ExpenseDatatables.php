<?php

namespace App\Http\Controllers\Datatables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Yajra\DataTables\DataTables;

class ExpenseDatatables extends Controller
{
    //
    public function index()
    {
        $expense = Expense::with('requested_by', 'approved_by', 'reject_by')->whereIn('status', [Expense::STATUS_DRAFT, Expense::STATUS_PENDING])->latest('created_at');
        return DataTables::of($expense)
            ->editColumn('expense_number', function ($row){
                if($row->status != Expense::STATUS_PENDING){
                    return "<a href='" . route('expense.show-detail', $row->id) . "'>{$row->expense_number}</a>";
                }

                return "<a href='" . route('expense.edit', $row->id) . "'>{$row->expense_number}</a>";
            })
            ->editColumn('request_by', function ($row){
                return $row->requested_by->name;
            })
            ->editColumn('confirmation', function ($row){
                if($row->status == Expense::STATUS_APPROVED || $row->status == Expense::STATUS_DONE){
                    return $row->approved_by->name;
                }
                elseif($row->status == Expense::STATUS_REJECTED){
                    return $row->reject_by->name;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('status', function ($row){
                return match ($row->status) {
                    Expense::STATUS_APPROVED => '<span class="badge badge-success">Disetujui</span>',
                    Expense::STATUS_PENDING => '<span class="badge badge-dark">Pending</span>',
                    Expense::STATUS_REJECTED => '<span class="badge badge-danger">Ditolak</span>',
                    Expense::STATUS_DONE => '<span class="badge badge-success">Selesai</span>',
                    Expense::STATUS_OUTGOING => '<span class="badge badge-info">Realisasi</span>',
                    Expense::STATUS_DRAFT => '<span class="badge badge-secondary">Draft</span>',
                };
            })
            ->addColumn('action', function (Expense $row) {
                $data = [
                    'edit_url'     => route('expense.edit', ['expense' => $row->id]),
                    'delete_url'   => route('expense.destroy', ['expense' => $row->id]),
                    'redirect_url' => route('expense.index'),
                    'resource'     => 'expense',
                    'custom_links' => [
                        [
                            'label' => 'Detail',
                            'url' => route('expense.show', ['expense' => $row->id]),
                            'name' => 'expense.show'
                        ],
                        [
                            'label' => "Publish",
                            'url' => route('expense.publish', ['expense' => $row->id]),
                            'name' => 'expense.publish' 
                        ]
                    ]
                ];
                if($row->status != Expense::STATUS_DRAFT ){
                    $data['edit_url'] = null;
                    $data['delete_url'] = null;
                }
                if($row->status == Expense::STATUS_PENDING){
                    $data['custom_links'] = null;
                }

                return view('components.datatable-action', $data);

            })
            ->rawColumns(['status', 'action', 'expense_number'])
            ->toJson();
    }
}
