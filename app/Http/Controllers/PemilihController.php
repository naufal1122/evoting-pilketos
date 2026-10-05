<?php

namespace App\Http\Controllers;

use App\Services\VotingService;
use App\Paslon;
use App\Setting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class PemilihController extends Controller
{
    protected $votingService;

    public function __construct(VotingService $votingService)
    {
        $this->middleware('auth');
        $this->votingService = $votingService;
    }

    /**
     * Menampilkan beranda pemilihan siswa.
     */
    public function index()
    {
        $data = Paslon::all();
        $votingSchedule = Setting::getVotingStatus();
        return view('siswa.home', compact('data', 'votingSchedule'));
    }

    /**
     * Menampilkan detail calon ketua.
     */
    public function detail($id)
    {
        $data = Paslon::findOrFail($id);
        return view('siswa.detail', ['data' => $data]);
    }

    /**
     * Memberikan suara kepada paslon.
     */
    public function pilihPaslon($id, Request $request)
    {
        $idUser = auth()->id();

        try {
            $result = $this->votingService->prosesVote((int) $id, (int) $idUser);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($result);
            }

            Alert::success('Success', $result['message']);
            return redirect('/home')->with('auto_logout', true);
        } catch (\DomainException $de) {
            $msg = $de->getMessage();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            Alert::warning('Peringatan', $msg);
            return redirect('/home');
        } catch (\InvalidArgumentException $iae) {
            $msg = $iae->getMessage();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            Alert::error('Error', $msg);
            return redirect('/home');
        } catch (\Throwable $e) {
            Log::error('Vote error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan voting: ' . $e->getMessage()], 500);
            }
            Alert::error('Error', 'Gagal menyimpan voting: ' . $e->getMessage());
            return redirect('/home');
        }
    }
}
