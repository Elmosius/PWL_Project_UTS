<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\PollingDetail;
use App\Models\ProgramStudi;
use App\Models\semester;
use App\Models\User;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('matakuliah.index', [
            'data' => MataKuliah::paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('kaprodi');
        return view('matakuliah.create', [
            'ps' => ProgramStudi::all(),
            'kurikulum' => Kurikulum::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('kaprodi');
        $validateData = $request->validate([
            'id_mataKuliah' => 'required|max:10|unique:mata_kuliah',
            'nama_mataKuliah' => 'required|max:45',
            'sks' => 'required|min:1|max:4',
            'id_program_studi' => 'required',
            'id_kurikulum' => 'required'
        ]);

        MataKuliah::create($validateData);
        return redirect('/dashboard/mata-kuliah')->with('success', 'Mata kuliah Has Been Created',);
    }

    /**
     * Display the specified resource.
     */
    public function show(MataKuliah $mataKuliah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataKuliah $mataKuliah)
    {
        $this->authorize('kaprodi');
        return view('matakuliah.edit', [
            'mk' => $mataKuliah,
            'kps' => ProgramStudi::all(),
            'kurikulum' => Kurikulum::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $this->authorize('kaprodi');
        $validateData = $request->validate([
            'nama_mataKuliah' => 'required|max:45',
            'sks' => 'required|min:1|max:4',
            'id_program_studi' => 'required',
            'id_kurikulum' => 'required'
        ]);

        $mataKuliah->update($validateData);
        return redirect('/dashboard/mata-kuliah')->with('success', 'Mata kuliah Has Been Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        $this->authorize('kaprodi');

        $check = PollingDetail::where('id_mataKuliah', $mataKuliah->id_mataKuliah)->first();
        if ($check) {
            return redirect('/dashboard/mata-kuliah')->with('errors', 'Tidak bisa menghapus
            mata kuliah yang masih terkait dengan polling detail.');
        }

        MataKuliah::destroy($mataKuliah->id_mataKuliah);
        return redirect('/dashboard/mata-kuliah')->with('success', 'Mata kuliah Has Been Deleted',);
    }
}
