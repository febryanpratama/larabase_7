<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TodoAssign;
use App\TodoEmployee;
use App\User;
use App\Utils\FileUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\ResponseCode;

class TodoController extends Controller
{
    /**
     * Menampilkan semua todo milik user (misal berdasarkan user_id).
     */
    public function index(Request $request)
    {
        $todos = TodoEmployee::with('assigns')->where('user_id', auth()->user()->id)->get();

        return ResponseCode::successGet($todos, 'Daftar todo berhasil diambil.');
    }

    /**
     * Menyimpan todo baru
     */
    public function store(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'title_todo'       => 'required|string|max:255',
            'description_todo' => 'required|string',
            'label'            => 'nullable|in:low,medium,high',
            'status'           => 'nullable|in:done,pending,onprogress',
            'attachment_path'  => 'nullable|string',
            'todo_assign'      => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors());
        }

        $result = FileUploadHelper::uploadBase64File($request->attachment_path, 'file_todo', 'all');

        if (!$result['status']) {
            return ResponseCode::badRequest($result['message']);
        }

        $todo = TodoEmployee::create([
            'user_id'          => $request->user()->id,
            'title_todo'       => $request->title_todo,
            'description_todo' => $request->description_todo,
            'label'            => $request->label ?? 'low',
            'status'           => $request->status ?? 'pending',
            'attachment_path'  => $result['path'],
        ]);

        if ($request->has('todo_assign') && is_array($request->todo_assign)) {
            foreach ($request->todo_assign as $item) {
                $user = User::find($item); // lebih ringkas
                if ($user) {
                    TodoAssign::create([
                        'todo_id' => $todo->id,
                        'user_id' => $item
                    ]);
                }
            }
        }

        return ResponseCode::successPost($todo, 'Todo berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail todo
     */
    public function show($id)
    {
        $todo = TodoEmployee::find($id);

        if (!$todo) {
            return ResponseCode::notFound('Todo tidak ditemukan.');
        }

        return ResponseCode::successGet($todo, 'Detail todo berhasil diambil.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title_todo'       => 'sometimes|required|string|max:255',
            'description_todo' => 'sometimes|required|string',
            'attachment_path'  => 'nullable|string',
            'todo_assign'      => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return ResponseCode::badRequest('Validasi gagal.', $validator->errors()->first());
        }

        $todo = TodoEmployee::find($id);
        if (!$todo) {
            return ResponseCode::notFound('Todo tidak ditemukan.');
        }

        if ($request->has('title_todo')) {
            $todo->title_todo = $request->title_todo;
        }

        if ($request->has('description_todo')) {
            $todo->description_todo = $request->description_todo;
        }

        // Handle attachment baru
        if ($request->filled('attachment_path')) {
            $result = FileUploadHelper::uploadBase64File($request->attachment_path, 'file_todo', 'all');
            if (!$result['status']) {
                return ResponseCode::badRequest($result['message']);
            }

            // Hapus file lama kalau ada
            if ($todo->attachment_path && file_exists(public_path($todo->attachment_path))) {
                @unlink(public_path($todo->attachment_path));
            }

            $todo->attachment_path = $result['path'];
        }

        $todo->save();

        // Handle todo_assign
        if ($request->has('todo_assign') && is_array($request->todo_assign)) {
            // Hapus assign lama
            TodoAssign::where('todo_id', $todo->id)->delete();

            // Insert assign baru
            foreach ($request->todo_assign as $item) {
                $user = User::find($item);
                if ($user) {
                    TodoAssign::create([
                        'todo_id' => $todo->id,
                        'user_id' => $item
                    ]);
                }
            }
        }

        return ResponseCode::successGet($todo->load('assigns'), 'Todo berhasil diperbarui.');
    }

    
    public function destroy($id)
    {
        $todo = TodoEmployee::find($id);

        if (!$todo) {
            return ResponseCode::notFound('Todo tidak ditemukan.');
        }

        // Hapus file attachment kalau ada
        if ($todo->attachment_path && file_exists(public_path($todo->attachment_path))) {
            @unlink(public_path($todo->attachment_path));
        }

        // Hapus assign terkait
        TodoAssign::where('todo_id', $todo->id)->delete();

        // Hapus todo
        $todo->delete();

        return ResponseCode::successGet(null, 'Todo berhasil dihapus.');
    }
}