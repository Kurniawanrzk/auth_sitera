<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Nasabah;
use App\Models\BankSampahUnit;
use App\Models\Perusahaan;
use App\Models\Pemerintah;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:nasabah,bsu,perusahaan,pemerintah',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi tambahan berdasarkan role
        switch ($request->role) {
            case 'bsu':
                $validatorBSU = Validator::make($request->all(), [
                    'nomor_registrasi' => 'required|unique:bank_sampah_unit,nomor_registrasi',
                    'nama_bsu' => 'required',
                ]);
                
                if ($validatorBSU->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi data BSU gagal',
                        'errors' => $validatorBSU->errors()
                    ], 422);
                }
                break;
                
            case 'perusahaan':
                $validatorPerusahaan = Validator::make($request->all(), [
                    // 'nib_npwp' => 'required|unique:perusahaan,nib_npwp',
                    'nama_perusahaan' => 'required',
                    'jenis_perusahaan' => 'required',
                ]);
                
                if ($validatorPerusahaan->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi data perusahaan gagal',
                        'errors' => $validatorPerusahaan->errors()
                    ], 422);
                }
                break;
                
            case 'pemerintah':
                $validatorPemerintah = Validator::make($request->all(), [
                    'kode_instansi' => 'required|unique:pemerintah,kode_instansi',
                    'nama_instansi' => 'required',
                ]);
                
                if ($validatorPemerintah->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi data pemerintah gagal',
                        'errors' => $validatorPemerintah->errors()
                    ], 422);
                }
                break;
                
            // Nasabah tidak melakukan registrasi mandiri, dibuat oleh BSU
            case 'nasabah':
                return response()->json([
                    'status' => false,
                    'message' => 'Registrasi nasabah hanya dapat dilakukan oleh BSU'
                ], 403);
                break;
        }

        // Buat user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Buat profile berdasarkan role
        switch ($request->role) {
            case 'bsu':
                BankSampahUnit::create([
                    'user_id' => $user->id,
                    'nomor_registrasi' => $request->nomor_registrasi,
                    'nama_bsu' => $request->nama_bsu,
                    'kategori' => $request->kategori ?? 'bsu',
                    'alamat' => $request->alamat ?? '',
                    'jalan_dusun' => $request->jalan_dusun ?? '',
                    'rt' => $request->rt ?? '',
                    'rw' => $request->rw ?? '',
                    'desa' => $request->desa ?? '',
                    'kecamatan' => $request->kecamatan ?? '',
                    'tanggal_berdiri' => $request->tanggal_berdiri ?? now(),
                    'nama_pengurus' => $request->nama_pengurus ?? '',
                    'nomor_telepon' => $request->nomor_telepon ?? '',
                ]);
                break;
                
            case 'perusahaan':
                Perusahaan::create([
                    'user_id' => $user->id,
                    'nib_npwp' => $request->nib_npwp,
                    'nama_perusahaan' => $request->nama_perusahaan,
                    'jenis_perusahaan' => $request->jenis_perusahaan,
                    'alamat' => $request->alamat ?? '',
                    'email_bisnis' => $request->email_bisnis ?? $request->email,
                    'nomor_telepon' => $request->nomor_telepon ?? '',
                ]);
                break;
                
            case 'pemerintah':
                Pemerintah::create([
                    'user_id' => $user->id,
                    'kode_instansi' => $request->kode_instansi,
                    'nama_instansi' => $request->nama_instansi,
                    'jabatan' => $request->jabatan ?? '',
                    'nama_pejabat' => $request->nama_pejabat ?? '',
                    'alamat' => $request->alamat ?? '',
                    'email_instansi' => $request->email_instansi ?? $request->email,
                    'nomor_telepon' => $request->nomor_telepon ?? '',
                ]);
                break;
        }

        // Replace Sanctum token with JWT
        $token = auth()->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = auth()->user();
        $profile = $user->profile();

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'profile' => $profile,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
                'profile' => $profile
            ]
        ]);
    }
}