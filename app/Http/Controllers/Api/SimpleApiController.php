<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SimpleApiController extends Controller
{
    private function fileUrl(?string $path)
    {
        if (!$path) return null;
        return url(Storage::url($path));
    }

    public function index()
    {
        try {
            $siswas = Siswa::select('id', 'nis', 'nama', 'rombel', 'rayon', 'medsos', 'portofolio', 'certifikat', 'created_at')
                          ->orderBy('nama', 'asc')
                          ->get()
                          ->map(function ($s) {
                              return [
                                  'id' => $s->id,
                                  'nis' => $s->nis,
                                  'nama' => $s->nama,
                                  'rombel' => $s->rombel,
                                  'rayon' => $s->rayon,
                                  'medsos' => $s->medsos,
                                  'portofolio' => $this->fileUrl($s->portofolio),
                                  'certifikat' => $this->fileUrl($s->certifikat),
                                  'created_at' => $s->created_at,
                              ];
                          });

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diambil',
                'data' => $siswas,
                'total' => $siswas->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make(array_merge($request->all(), $request->files->all()), [
                'nis' => 'required|string|unique:siswas,nis',
                'nama' => 'required|string|max:255',
                'rombel' => 'required|string|max:255',
                'rayon' => 'required|string|max:255',
                'password' => 'required|string|min:6',
                'medsos' => 'nullable|string|max:255',
                'portofolio' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
                'certifikat' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'nis' => $request->nis,
                'nama' => $request->nama,
                'rombel' => $request->rombel,
                'rayon' => $request->rayon,
                'password' => Hash::make($request->password),
                'medsos' => $request->medsos,
            ];

            if ($request->hasFile('portofolio')) {
                $data['portofolio'] = $request->file('portofolio')->store('portofolios', 'public');
            }

            if ($request->hasFile('certifikat')) {
                $data['certifikat'] = $request->file('certifikat')->store('certifikats', 'public');
            }

            $siswa = Siswa::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->nama,
                    'rombel' => $siswa->rombel,
                    'rayon' => $siswa->rayon,
                    'medsos' => $siswa->medsos,
                    'portofolio' => $this->fileUrl($siswa->portofolio),
                    'certifikat' => $this->fileUrl($siswa->certifikat),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $s = Siswa::find($id);

            if (!$s) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            $siswa = [
                'id' => $s->id,
                'nis' => $s->nis,
                'nama' => $s->nama,
                'rombel' => $s->rombel,
                'rayon' => $s->rayon,
                'medsos' => $s->medsos,
                'portofolio' => $this->fileUrl($s->portofolio),
                'certifikat' => $this->fileUrl($s->certifikat),
                'created_at' => $s->created_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diambil',
                'data' => $siswa
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'required|string|min:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $keyword = $request->keyword;

            $siswas = Siswa::where('nama', 'LIKE', "%{$keyword}%")
                          ->orWhere('nis', 'LIKE', "%{$keyword}%")
                          ->orWhere('rombel', 'LIKE', "%{$keyword}%")
                          ->orWhere('rayon', 'LIKE', "%{$keyword}%")
                          ->select('id', 'nis', 'nama', 'rombel', 'rayon', 'medsos', 'portofolio', 'certifikat', 'created_at')
                          ->orderBy('nama', 'asc')
                          ->get()
                          ->map(function ($s) {
                              return [
                                  'id' => $s->id,
                                  'nis' => $s->nis,
                                  'nama' => $s->nama,
                                  'rombel' => $s->rombel,
                                  'rayon' => $s->rayon,
                                  'medsos' => $s->medsos,
                                  'portofolio' => $this->fileUrl($s->portofolio),
                                  'certifikat' => $this->fileUrl($s->certifikat),
                                  'created_at' => $s->created_at,
                              ];
                          });

            return response()->json([
                'success' => true,
                'message' => 'Hasil pencarian berhasil diambil',
                'data' => $siswas,
                'total' => $siswas->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nis' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $siswa = Siswa::where('nis', $request->nis)->first();

            if (!$siswa || !Hash::check($request->password, $siswa->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIS atau password salah'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->nama,
                    'rombel' => $siswa->rombel,
                    'rayon' => $siswa->rayon,
                    'medsos' => $siswa->medsos,
                    'portofolio' => $this->fileUrl($siswa->portofolio),
                    'certifikat' => $this->fileUrl($siswa->certifikat),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}