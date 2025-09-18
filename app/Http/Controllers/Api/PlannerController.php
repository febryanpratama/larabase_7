<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plan;
use App\Utils\ResponseCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlannerController extends Controller
{
    /**
     * List semua plan milik user login
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20); // default 20
        $plans = Plan::with('user')
            ->where('user_id', auth()->id())
            ->paginate($perPage);

        return ResponseCode::successPaginate($plans, 'Daftar plan berhasil diambil.');
    }

    /**
     * Simpan plan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'category'    => 'nullable|in:Work,Personal,Important',
            'date_start'  => 'required|date',
            'date_end'    => 'required|date|after_or_equal:date_start',
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        $plan = Plan::create([
            'user_id'     => $request->user()->id,
            'description' => $request->description,
            'category'    => $request->category ?? 'Work',
            'date_start'  => $request->date_start,
            'date_end'    => $request->date_end,
        ]);

        return ResponseCode::successPost($plan, 'Plan berhasil ditambahkan.');
    }

    /**
     * Detail plan
     */
    public function show($id)
    {
        $plan = Plan::where('user_id', auth()->id())->find($id);

        if (!$plan) {
            return ResponseCode::notFound('Plan tidak ditemukan.');
        }

        return ResponseCode::successGet($plan);
    }

    /**
     * Update plan
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string|max:255',
            'category'    => 'nullable|in:Work,Personal,Important',
            'date_start'  => 'sometimes|required|date',
            'date_end'    => 'sometimes|required|date|after_or_equal:date_start',
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        $plan = Plan::where('user_id', auth()->id())->find($id);

        if (!$plan) {
            return ResponseCode::notFound('Plan tidak ditemukan.');
        }

        $plan->update($request->only(['description', 'category', 'date_start', 'date_end']));

        return ResponseCode::successGet($plan, 'Plan berhasil diperbarui.');
    }

    /**
     * Hapus plan
     */
    public function destroy($id)
    {
        $plan = Plan::where('user_id', auth()->id())->find($id);

        if (!$plan) {
            return ResponseCode::notFound('Plan tidak ditemukan.');
        }

        $plan->delete();

        return ResponseCode::successGet(null, 'Plan berhasil dihapus.');
    }
}