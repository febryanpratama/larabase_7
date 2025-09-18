<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Employee;
use App\User;
use App\Division;
use App\Utils\ResponseCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * List employees with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $employees = Employee::with(['user', 'division'])
            ->paginate($perPage);

        return ResponseCode::successPaginate($employees, 'Daftar employee berhasil diambil.');
    }

    /**
     * Store new employee
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:6',
            'nip'         => 'nullable|string|max:50',
            'nik'         => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        // buat user
        $user = User::create([
            'role_id'   => 2, // role employee
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
        ]);

        // buat employee
        $employee = Employee::create([
            'user_id'     => $user->id,
            'division_id' => $request->division_id,
            'name'        => $request->name,
            'nip'         => $request->nip,
            'nik'         => $request->nik,
        ]);

        return ResponseCode::successPost($employee->load(['user', 'division']), 'Employee berhasil ditambahkan.');
    }

    /**
     * Show employee detail
     */
    public function show($id)
    {
        $employee = Employee::with(['user', 'division'])->find($id);

        if (!$employee) {
            return ResponseCode::notFound('Employee tidak ditemukan.');
        }

        return ResponseCode::successGet($employee);
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::with('user')->find($id);

        if (!$employee) {
            return ResponseCode::notFound('Employee tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'division_id' => 'sometimes|required|exists:divisions,id',
            'name'        => 'sometimes|required|string|max:255',
            'email'       => 'sometimes|required|email|unique:users,email,' . $employee->user_id,
            'phone'       => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:6',
            'nip'         => 'nullable|string|max:50',
            'nik'         => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        // update user
        if ($request->has('name')) $employee->user->name = $request->name;
        if ($request->has('email')) $employee->user->email = $request->email;
        if ($request->has('phone')) $employee->user->phone = $request->phone;
        if ($request->filled('password')) $employee->user->password = Hash::make($request->password);
        $employee->user->save();

        // update employee
        if ($request->has('division_id')) $employee->division_id = $request->division_id;
        if ($request->has('name')) $employee->name = $request->name;
        if ($request->has('nip')) $employee->nip = $request->nip;
        if ($request->has('nik')) $employee->nik = $request->nik;
        $employee->save();

        return ResponseCode::successGet($employee->load(['user', 'division']), 'Employee berhasil diperbarui.');
    }

    /**
     * Delete employee (soft delete)
     */
    public function destroy($id)
    {
        $employee = Employee::with('user')->find($id);

        if (!$employee) {
            return ResponseCode::notFound('Employee tidak ditemukan.');
        }

        // hapus employee
        $employee->delete();

        // hapus user juga (optional, bisa soft delete)
        $employee->user->delete();

        return ResponseCode::successGet(null, 'Employee berhasil dihapus.');
    }
}