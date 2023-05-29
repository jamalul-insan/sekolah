<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Tuition;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TuitionDatatables extends Controller
{
    //

    public function index()
    {
        $tuition = Tuition::with('tuition_type', 'academic_year', 'grade', 'requested_by', 'approved_by');
        return DataTables::of($tuition)
            ->filterColumn('academic_year', function ($query, $keyword) {
                $query->whereHas('academic_year', function ($q) use ($keyword) {
                    $q->where('academic_year_name', 'like', "%$keyword%");
                });
            })
            ->filterColumn('grade', function ($query, $keyword) {
                $query->whereHas('grade', function ($q) use ($keyword) {
                    $q->where('grade_name', 'like', "%$keyword%");
                });
            })
            ->editColumn('tuition_type', function ($row) {
                return $row->tuition_type ? $row->tuition_type->name : '-';
            })
            ->editColumn('academic_year', function ($row) {
                return $row->academic_year ? $row->academic_year->academic_year_name : '-';
            })
            ->editColumn('grade', function ($row) {
                return $row->grade ? $row->grade->grade_name : '-';
            })
            ->editColumn('price', function ($row) {
                return 'Rp. ' . number_format($row->price, 0, ',', '.');
            })
            ->editColumn('request_by', function ($row) {
                return $row->requested_by->name;
            })
            ->editColumn('approval_by', function ($row) {
                return $row->approved_by ? $row->approved_by->name : '-';
            })
            ->addColumn('action', function (Tuition $row) {
                $data = [
                    'edit_url'     => route('tuition.edit', ['tuition' => $row->id]),
                    'delete_url'   => route('tuition.destroy', ['tuition' => $row->id]),
                    'redirect_url' => route('tuition.index'),
                    'resource' => 'tuition'
                ];
                return view('components.datatable-action', $data);
            })->toJson();
    }
}
