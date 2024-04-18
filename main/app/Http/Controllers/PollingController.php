<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Polling;
use App\Models\PollingDetail;
use App\Models\ProgramStudi;
use App\Models\semester;
use Illuminate\Http\Request;

class PollingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $now = now();

        $activePollings = Polling::where('is_active', 1)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        $mahasiswa = $user->role->nama_role != 'admin' && $user->role->nama_role != 'kaprodi';
        if ($activePollings) {
            $hasVoted = PollingDetail::where('id_user', $user->id_user)
                ->where('id_polling', $activePollings->id_polling)
                ->exists();
        } else {
            $hasVoted = false;
        }

//        if ($hasVoted) {
//            return view('polling.index', [
//                'datas' => null,
//                'mks' => null,
//            ])->with('success', 'Terima kasih sudah melakukan polling');
//        }

        if ($mahasiswa) {
            $mks = MataKuliah::where('id_program_studi', $user->id_program_studi)->get();
        } else {
            $mks = MataKuliah::all();
        }

        return view('polling.index', [
            'datas' => $activePollings,
            'mks' => $mks,
            'now' => $now,
            'hasVoted' => $hasVoted
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('kaprodi');
        return view('polling.create', [
            'data' => Polling::with('pollingDetail')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_polling' => 'required|max:45',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'is_active' => 'required|in:0,1'
        ]);

        if ($validateData['is_active'] == 1) {
            $activePolling = Polling::where('is_active', 1)->first();
            if ($activePolling) {
                return redirect('/dashboard/make-polling')->with('errors',
                    'Tidak bisa membuat polling baru karena masih ada polling yang aktif.');
            }
        }

        Polling::create($validateData);
        return redirect('/dashboard/make-polling')->with('success', 'Polling Has Been Created',);
    }


    /**
     * Display the specified resource.
     */
    public function show(Polling $polling)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Polling $polling)
    {
        $this->authorize('kaprodi');
        return view('polling.edit', [
            'datas' => $polling->load('pollingDetail')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Polling $polling)
    {

        $activePolling = Polling::where('is_active', 1)->first();
        if ($activePolling && $activePolling->id_polling != $polling->id_polling) {
            return redirect('/dashboard/make-polling')->with('errors',
                'Gagal karena masih ada polling yang aktif.');
        }

        $validateData = $request->validate([
            'nama_polling' => 'required|max:45',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'is_active' => 'required|max:2'
        ]);

        $polling->update($validateData);
        return redirect('/dashboard/make-polling')->with('success', 'Polling Has Been Updated');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Polling $polling)
    {
        $this->authorize('kaprodi');
        $check = PollingDetail::where('id_polling', $polling->id_polling)->first();
        if ($check) {
            return redirect('/dashboard/make-polling')->with('errors', 'Tidak bisa menghapus
            polling yang masih terkait dengan polling detail.');
        }

        Polling::destroy($polling->id_polling);
        return redirect('/dashboard/make-polling')->with('success', 'Polling Has Been Deleted',);
    }

    public function hasil()
    {
        return view('polling.hasil', [
            'datas' => Polling::with('pollingDetail')->get(),
        ]);
    }

    public function makePolling()
    {
        return view('polling.make-polling', [
            'datas' => Polling::with('pollingDetail')->get(),
        ]);
    }


}
