<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Polling;
use App\Models\PollingDetail;
use App\Models\semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Carbon;

class PollingDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $jumVoted = "SELECT id_user
                     FROM polling_detail
                     GROUP BY id_user";

        $jumPolling = "SELECT id_polling
                       FROM polling";

        $dataHistoryDetail = "";
        $dataHistoryMK = "";

        if ( $user->id_role == 3 ) {

            $historyDetail = "SELECT p.id_polling, p.nama_polling, p.start_at, p.end_at
                            FROM polling p, polling_detail pd
                            WHERE pd.id_polling = p.id_polling
                            AND pd.id_user = $user->id_user
                            GROUP BY p.id_polling, p.nama_polling, p.start_at, p.end_at
                            ORDER BY p.id_polling";

            $dataHistoryDetail = DB::select($historyDetail);
        }

        $jumHasilVoted = DB::select($jumVoted);
        $jumHasilPolling = DB::select($jumPolling);

        $endedPollings = Polling::where('end_at', '<', now())->get();

        return view('dashboard.index', [
            'datas' => Polling::where('is_active', 1)->get(),
            'jumlah' => Polling::where('is_active', 1)->count(),
            'hasilVoted' => $jumHasilVoted,
            'hasilPolling' => $jumHasilPolling,
            'periodEnd' => $endedPollings,
            'histories' => $dataHistoryDetail
        ]);
    }

    public function getChart()
    {
        $arrayJumlahMk = [];
        $arrayLabelMk = [];
        $index = 0;

        $dataPollingDetail = PollingDetail::select("id_mataKuliah")->groupBy("id_mataKuliah")->pluck("id_mataKuliah")->toArray();

        $sqlJumlahMk = "SELECT mk.nama_mataKuliah, count(pd.id_mataKuliah) jumlah_mataKuliah
                        FROM polling p, polling_detail pd, mata_kuliah mk
                        WHERE pd.id_polling = p.id_polling
                            AND pd.id_mataKuliah = mk.id_mataKuliah
                        GROUP BY pd.id_mataKuliah, mk.nama_mataKuliah";

        $datajumlahMk = DB::select($sqlJumlahMk);

        foreach ($dataPollingDetail as $pd) {
            $arrayJumlahMk[$index] = $datajumlahMk[$index]->jumlah_mataKuliah;
            $arrayLabelMk[$index] = $datajumlahMk[$index]->nama_mataKuliah;
            $index += 1;
        }

        $dataa = [
            'nama' => $arrayLabelMk,
            'jumlah' => $arrayJumlahMk
        ];
        return $dataa;
    }

    public function getPollingMataKuliah(Request $request)
    {
        $user = auth()->user();
        $historyMatkul = "SELECT pd.id_polling, pd.id_user, mk.nama_mataKuliah, mk.sks
                          FROM polling_detail pd, mata_kuliah mk
                          WHERE pd.id_mataKuliah = mk.id_mataKuliah
                            AND pd.id_polling = ".$request->idPolling."
                            AND pd.id_user = ".$user->id_user;

        $dataHistoryMatkul = DB::select($historyMatkul);

        // $dataPollingDetail = PollingDetail::where('id_polling', $request->idPolling)->where('id_user', $user->id_user)->get();

        return response()->json($dataHistoryMatkul);
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
        $validateData = $request->validate([
            'id_polling' => 'required',
            'id_mataKuliah' => 'required|array',
        ]);

        $totalSKS = 0;
        foreach ($validateData['id_mataKuliah'] as $id_mataKuliah) {
            $mk = MataKuliah::find($id_mataKuliah);
            $totalSKS += $mk->sks;
        }

        if ($totalSKS > 9) {
            return redirect('/dashboard/polling')->with('errors', 'Total SKS tidak boleh lebih dari 9');
        }

        $validateData['id_user'] = auth()->user()->id_user;
        foreach ($validateData['id_mataKuliah'] as $id_mataKuliah) {
            PollingDetail::create([
                'id_polling' => $validateData['id_polling'],
                'id_user' => $validateData['id_user'],
                'id_mataKuliah' => $id_mataKuliah,
            ]);
        }
        return redirect('/dashboard/polling')->with('success', 'Polling Berhasil!',);
    }

    public function detailHasil($id_polling, $id_mataKuliah)
    {
        $this->authorize('adminorkaprodi');
        $pollingDetail = PollingDetail::where('id_polling', $id_polling)
            ->where('id_mataKuliah', $id_mataKuliah)
            ->get();

        return view('polling.hasil-detail', [
            'datas' => $pollingDetail,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(PollingDetail $pollingDetail)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PollingDetail $pollingDetail)
    {
        $user = auth()->user();
        $now = now();
        $mahasiswa = $user->role->nama_role != 'admin' && $user->role->nama_role != 'kaprodi';

        $activePollings = Polling::where('is_active', 1)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        if ($activePollings) {
            $hasVoted = $pollingDetail->where('id_user', $user->id_user)
                ->where('id_polling', $activePollings->id_polling)
                ->exists();
        } else {
            $hasVoted = false;
        }

        if ($mahasiswa) {
            $mks = MataKuliah::where('id_program_studi', $user->id_program_studi)->get();
        } else {
            $mks = MataKuliah::all();
        }

        if (!$hasVoted) {
            return redirect('dashboard/polling')->with('errors', 'Anda tidak dapat mengedit
            polling ini karena periode
            polling telah berakhir atau polling tidak aktif.');
        }

        return view('polling.edit-detail', [
            'datas' => $activePollings,
            'mks' => $mks,
            'hasVoted' => $hasVoted
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PollingDetail $pollingDetail)
    {
        $validateData = $request->validate([
            'id_polling' => 'required',
            'id_mataKuliah' => 'required|array',
        ]);

        $totalSKS = 0;
        foreach ($validateData['id_mataKuliah'] as $id_mataKuliah) {
            $mk = MataKuliah::find($id_mataKuliah);
            $totalSKS += $mk->sks;
        }

        if ($totalSKS > 9) {
            return redirect('/dashboard/polling')->with('errors', 'Total SKS tidak boleh lebih dari 9');
        }

        $validateData['id_user'] = auth()->user()->id_user;

        PollingDetail::where('id_user', $validateData['id_user'])
            ->where('id_polling', $validateData['id_polling'])
            ->delete();

        foreach ($validateData['id_mataKuliah'] as $id_mataKuliah) {
            PollingDetail::create([
                'id_polling' => $validateData['id_polling'],
                'id_user' => $validateData['id_user'],
                'id_mataKuliah' => $id_mataKuliah,
            ]);
        }
        return redirect('/dashboard/polling')->with('success', 'Update polling berhasil!',);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PollingDetail $pollingDetail)
    {
        //
    }


}
