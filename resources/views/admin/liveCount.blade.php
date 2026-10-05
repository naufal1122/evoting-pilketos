<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Count - Pemilihan Ketua OSIS</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/neo-skeuo.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('/img/logosss.png') }}" type="image/x-icon">

    <style>
        body {
            background-color: #eaf0f7 !important;
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
            padding: 24px;
        }

        .live-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 16px 28px;
            border-radius: 20px;
        }

        .live-dot {
            width: 12px;
            height: 12px;
            background-color: #ef4444;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse-red 1.5s infinite;
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .paslon-card {
            border-radius: 20px;
            padding: 24px;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .paslon-badge {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 24px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 3px 3px 8px rgba(16, 185, 129, 0.35);
        }

        .paslon-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 6px 6px 14px rgba(166, 178, 195, 0.5), -4px -4px 10px rgba(255, 255, 255, 0.9);
            margin: 12px auto;
            display: block;
        }

        .vote-number {
            font-size: 54px;
            font-weight: 800;
            color: #065f46;
            line-height: 1;
            margin: 8px 0;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.9);
        }

        .btn-fullscreen {
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <!-- Header Layar Proyektor -->
    <div class="neo-card live-header mb-4">
        <div class="d-flex align-items-center">
            <img src="/img/logoss.png" width="56" class="mr-3" alt="Logo Sekolah">
            <div>
                <h3 class="mb-0 font-weight-bold" style="letter-spacing: -0.5px;">LIVE COUNT PEMILIHAN KETUA OSIS</h3>
                <p class="mb-0 text-muted" style="font-size: 14px;">Monitor Realtime Pemungutan Suara • Pilketos Digital</p>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <div class="neo-inset py-2 px-3 mr-3 d-flex align-items-center">
                <span class="live-dot"></span>
                <span class="font-weight-bold" style="font-size: 13.5px; color: #1e293b;">LIVE MONITOR</span>
                <span class="text-muted ml-2 mr-2">|</span>
                <span id="currentTime" class="font-weight-bold text-success" style="font-size: 14px;"></span>
            </div>

            <button onclick="toggleFullscreen()" class="neo-btn neo-btn-secondary btn-fullscreen mr-2">
                <i class="fas fa-expand mr-1" id="fsIcon"></i> <span id="fsText">Fullscreen</span>
            </button>

            <a href="/dashboard" class="neo-btn neo-btn-secondary btn-fullscreen">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Ringkasan Suara Masuk -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="neo-card p-4 d-flex align-items-center">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin-right: 18px; box-shadow: 4px 4px 10px rgba(59, 130, 246, 0.35);">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #1e293b;">{{ $totalSiswa }}</h2>
                    <span class="text-muted" style="font-size: 14px; font-weight: 500;">Total Daftar Pemilih (DPT)</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="neo-card p-4 d-flex align-items-center">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin-right: 18px; box-shadow: 4px 4px 10px rgba(16, 185, 129, 0.35);">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold text-success" id="totalSuaraDisplay">{{ $totalSuara }}</h2>
                    <span class="text-muted" style="font-size: 14px; font-weight: 500;">Total Suara Sah Masuk</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="neo-card p-4 d-flex align-items-center">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin-right: 18px; box-shadow: 4px 4px 10px rgba(217, 119, 6, 0.35);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-bold" style="color: #b45309;" id="persentasePartisipasiDisplay">{{ $persentasePartisipasi }}%</h2>
                    <span class="text-muted" style="font-size: 14px; font-weight: 500;">Tingkat Partisipasi Pemilih</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Kandidat Paslon -->
    <div class="row mb-4">
        @foreach($hasilVote as $hv)
        <div class="col-md-{{ 12 / max(1, count($hasilVote)) }} mb-3">
            <div class="neo-card paslon-card">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="paslon-badge">{{ $hv['no_urut_paslon'] }}</div>
                        <span class="neo-badge neo-badge-success" style="font-size: 13px;">Kandidat {{ $hv['no_urut_paslon'] }}</span>
                    </div>

                    <img src="{{ !empty($hv['img_ketua']) ? '/img_ketua/' . $hv['img_ketua'] : 'https://ui-avatars.com/api/?name=' . urlencode($hv['ketua_paslon']) . '&background=10b981&color=fff' }}"
                         class="paslon-img"
                         alt="{{ $hv['ketua_paslon'] }}">

                    <h4 class="text-center font-weight-bold mb-1" style="color: #1e293b;">{{ $hv['ketua_paslon'] }}</h4>
                    <p class="text-center text-muted mb-3" style="font-size: 14px;">
                        Wakil: <span class="font-weight-bold">{{ !empty($hv['wakil_paslon']) ? $hv['wakil_paslon'] : '-' }}</span>
                    </p>
                </div>

                <div class="neo-inset p-3" style="border-radius: 16px;">
                    <div class="text-center text-muted mb-1" style="font-size: 13px; font-weight: 600;">PEROLEHAN SUARA</div>
                    <div class="vote-number" id="voteCount-{{ $hv['no_urut_paslon'] }}">{{ $hv['jumlah_vote'] }}</div>
                    <div class="d-flex justify-content-between align-items-center mb-1 font-weight-bold" style="font-size: 13px;">
                        <span>Persentase</span>
                        <span class="text-success" id="votePct-{{ $hv['no_urut_paslon'] }}">{{ $hv['percentage'] }}%</span>
                    </div>
                    <div class="neo-progress">
                        <div class="neo-progress-bar" id="voteBar-{{ $hv['no_urut_paslon'] }}" style="width: {{ $hv['percentage'] }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Jam Realtime
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        document.getElementById('currentTime').innerText = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Toggle Fullscreen
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                document.getElementById('fsIcon').className = 'fas fa-compress mr-1';
                document.getElementById('fsText').innerText = 'Exit Fullscreen';
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen().then(() => {
                    document.getElementById('fsIcon').className = 'fas fa-expand mr-1';
                    document.getElementById('fsText').innerText = 'Fullscreen';
                });
            }
        }
    }

    const totalDPT = {{ $totalSiswa }};

    // Polling Realtime via AJAX setiap 3 detik
    function pollLiveVotes() {
        $.ajax({
            url: '/user/hasil_vote_ajax',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.hasilVote) {
                    let total = 0;
                    response.hasilVote.forEach(item => {
                        total += parseInt(item.jumlah_vote || 0);
                    });

                    $('#totalSuaraDisplay').text(total);

                    let pctOverall = totalDPT > 0 ? ((total / totalDPT) * 100).toFixed(1) : 0;
                    $('#persentasePartisipasiDisplay').text(pctOverall + '%');

                    response.hasilVote.forEach(item => {
                        const noUrut = item.no_urut_paslon;
                        const count = parseInt(item.jumlah_vote || 0);

                        $(`#voteCount-${noUrut}`).text(count);
                        const pct = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                        $(`#votePct-${noUrut}`).text(pct + '%');
                        $(`#voteBar-${noUrut}`).css('width', pct + '%');
                    });
                }
            }
        });
    }

    setInterval(pollLiveVotes, 3000);
</script>
</body>
</html>
