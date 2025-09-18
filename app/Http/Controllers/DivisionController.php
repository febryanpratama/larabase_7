<?php

namespace App\Http\Controllers;

use App\Division;
use App\Utils\ResponseCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
{
    /**
     * List all divisions (with pagination optional)
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $divisions = Division::paginate($perPage);

        return ResponseCode::successPaginate($divisions, 'Daftar division berhasil diambil.');
    }

    /**
     * Store a new division
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        $division = Division::create([
            'name' => $request->name,
        ]);

        return ResponseCode::successPost($division, 'Division berhasil ditambahkan.');
    }

    /**
     * Show detail division
     */
    public function show($id)
    {
        $division = Division::find($id);

        if (!$division) {
            return ResponseCode::notFound('Division tidak ditemukan.');
        }

        return ResponseCode::successGet($division);
    }

    /**
     * Update division
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:divisions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        $division = Division::find($id);
        if (!$division) {
            return ResponseCode::notFound('Division tidak ditemukan.');
        }

        if ($request->has('name')) {
            $division->name = $request->name;
        }

        $division->save();

        return ResponseCode::successGet($division, 'Division berhasil diperbarui.');
    }

    /**
     * Delete division
     */
    public function destroy($id)
    {
        $division = Division::find($id);

        if (!$division) {
            return ResponseCode::notFound('Division tidak ditemukan.');
        }

        $division->delete();

        return ResponseCode::successGet(null, 'Division berhasil dihapus.');
    }
}