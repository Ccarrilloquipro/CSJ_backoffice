<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArchivosExportacionRequest;
use App\Http\Requests\UpdateArchivosExportacionRequest;
use App\Models\ArchivosExportacion;

class ArchivosExportacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		$archivos = ArchivosExportacion::all();
		return view('users.lista', compact('archivos'));
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
    public function store(StoreArchivosExportacionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchivosExportacionRequest $request, ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArchivosExportacion $archivosExportacion)
    {
        //
    }
}
