<?php

namespace App\Http\Controllers;

use App\Jobs\PublishMonthlyTuitions;
use App\Models\Student;
use App\Models\Tuition;
use App\Models\Transaction;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublishTuitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Penerbitan Biaya Manual';
        $tuitions = Tuition::query()
            ->whereNotNull('approval_by')
            ->whereHas('tuition_type', function ($query) {
                $query->where('recurring', false);
            })
            ->get();
        return view('pages.publish-tuition.index', compact('title', 'tuitions'));
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
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            if (!$request->tuitions) {
                throw new \Exception('Harus memilih biaya terlebih dahulu');
            }
            $tuitions = $request->tuitions;
            $jobs = [];

            foreach ($tuitions as $tuition) {
                $jobs[] = new PublishMonthlyTuitions(
                    tuition: Tuition::find($tuition),
                    date: now()
                );
            }

            if ($jobs) {
                // Job batching
                Bus::batch($jobs)->then(function (Batch $batch) {
                    // All jobs completed successfully...
                    Log::withContext([
                        'context' => 'Publish tuitions manually',
                        'user' => auth()->user()->name,
                        'period' => now()->addMonth()->startOfMonth(),
                    ])->info("Publish tuitions manually completed successfully...");
                })->catch(function (Batch $batch, \Throwable $e) use ($tuition) {
                    // First batch job failure detected...
                    Log::withContext([
                        'context' => 'Publish tuitions manually',
                        'tuition' => auth()->user()->name,
                        'period' => now()->addMonth()->startOfMonth(),
                        'error' => $e->getMessage()
                    ])->error("Publish tuitions manually failure detected...");
                })->finally(function (Batch $batch) use ($tuition) {
                    // The batch has finished executing...
                    Log::withContext([
                        'context' => 'Publish tuitions manually',
                        'tuition' => auth()->user()->name,
                        'period' => now()->addMonth()->startOfMonth(),
                    ])->info("Publish tuitions manually finished executing...");
                })
                    ->dispatch();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return redirect()->route('publish-tuition.index')->withToastError('Error Terbitkan Biaya! ' . $th->getMessage());
        }

        return redirect()->route('publish-tuition.index')->withToastSuccess('Berhasil Terbitkan Biaya!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
