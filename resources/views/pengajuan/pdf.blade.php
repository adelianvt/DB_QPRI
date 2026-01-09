<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>QPRI PDF</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111; }
    .page { padding: 18px 22px; }
    .title { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
    .sub { font-size: 12px; color:#555; margin-bottom: 14px; }
    .badge { display:inline-block; padding:6px 10px; border:1px solid #ddd; border-radius:999px; font-size:11px; }
    .section { border:1px solid #e5e5e5; border-radius:10px; overflow:hidden; margin-bottom:14px; }
    .section-h { background:#f6f7fb; padding:10px 12px; font-weight:700; }
    .section-b { padding:12px; }
    .row { margin-bottom: 10px; }
    .label { font-size:11px; color:#333; margin-bottom:4px; font-weight:700; }
    .box { border:1px solid #d9d9d9; border-radius:6px; padding:8px 10px; background:#fff; }
    .box-muted { background:#f5f5f5; }
    .small { font-size:11px; color:#555; }
    table { width:100%; border-collapse:collapse; }
    td { vertical-align:top; }
    .col { width:33.333%; padding-right:10px; }
    ul { margin: 6px 0 0 16px; padding:0; }
    li { margin: 2px 0; }
    .grid2 td { width:50%; padding-right:10px; }
    .grid4 td { width:25%; padding-right:10px; }
  </style>
</head>
<body>
@php
  $p = $pengajuan ?? ($p ?? null);
  $meta = (array)(($p?->meta) ?? []);
  $code = strtolower($p?->status?->code ?? '');

  $steps = (array) data_get($meta, 'decision.steps', []);
  $lastApproveBy = null;
  $lastRejectBy  = null;
  foreach (array_reverse($steps) as $st) {
    if (!$lastApproveBy && ($st['action'] ?? '') === 'approve') $lastApproveBy = ($st['by_role'] ?? null);
    if (!$lastRejectBy  && ($st['action'] ?? '') === 'reject')  $lastRejectBy  = ($st['by_role'] ?? null);
    if ($lastApproveBy && $lastRejectBy) break;
  }

  $kode = data_get($meta, 'rbb_users.kode')
        ?: data_get($meta, 'rbb_it.kode')
        ?: ('QPRI-' . ($p?->id ?? '-'));

  $statusText = match($code){
    'pending_approver1' => 'Waiting by GH CRV',
    'pending_iag'       => 'Waiting by IAG',
    'pending_approver2' => 'Waiting by GH IAG',
    'approved'          => 'Approved' . ($lastApproveBy ? ' by '.$lastApproveBy : ''),
    'rejected'          => 'Rejected' . ($lastRejectBy ? ' by '.$lastRejectBy : ''),
    default             => $p?->status?->label ?? '-',
  };

  $judul = $p?->judul ?? data_get($meta,'judul','-');
  $deskripsi = $p?->deskripsi ?? data_get($meta,'deskripsi','-');
  $divisi = data_get($meta,'divisi','-');
  $tipe = data_get($meta,'tipe','-');

  $g1 = data_get($meta,'group_utama',[]);
  $g2 = data_get($meta,'group_utama_2',[]);

  if (is_string($g1)) $g1 = array_filter(array_map('trim', explode(',', $g1)));
  if (is_string($g2)) $g2 = array_filter(array_map('trim', explode(',', $g2)));

  if (!is_array($g1)) $g1 = [];
  if (!is_array($g2)) $g2 = [];

  $comp = data_get($meta,'compiler_names',[]);
  if (!is_array($comp)) $comp = [];

  $rbbUsers = data_get($meta,'rbb_users',[]);
  if (!is_array($rbbUsers)) $rbbUsers = [];

  $rbbIt = data_get($meta,'rbb_it',[]);
  if (!is_array($rbbIt)) $rbbIt = [];

  // ✅ DATA IAG (Form Tambahan)
  $iag = data_get($meta, 'iag', []);
  if (!is_array($iag)) $iag = [];

  $itag = (array) data_get($iag, 'it_arch_governance', []);
  $itw  = (array) data_get($iag, 'it_technical_writer', []);
  $kar  = (array) data_get($iag, 'karakteristik', []);

  // watermark contact tetap
  $wm = [
    'name' => 'Sani Hardiansa',
    'phone' => '+62 812-2005-343',
    'email' => 'shardiansa@BANKBJB.CO.ID',
  ];
@endphp

<div class="page">
  <div class="title">FORMULIR REGISTRASI PROJECT</div>
  <div class="sub">
    Kode: <b>{{ $kode }}</b> &nbsp; • &nbsp;
    Pengajuan: <b>#{{ $p?->id ?? '-' }}</b> &nbsp; • &nbsp;
    Status: <span class="badge">{{ $statusText }}</span>
  </div>

  {{-- ===================== DATA PROJECT ===================== --}}
  <div class="section">
    <div class="section-h">Data Project</div>
    <div class="section-b">

      <div class="row">
        <div class="label">Pengajuan Nama Project</div>
        <div class="box">{{ $judul ?: '-' }}</div>
      </div>

      <div class="row">
        <div class="label">Deskripsi</div>
        <div class="box">{{ $deskripsi ?: '-' }}</div>
      </div>

      <div class="row">
        <table class="grid2">
          <tr>
            <td>
              <div class="label">Divisi Yang Menginisiasikan</div>
              <div class="box">{{ $divisi ?: '-' }}</div>
            </td>
            <td>
              <div class="label">Tipe Project</div>
              <div class="box">{{ $tipe ?: '-' }}</div>
            </td>
          </tr>
        </table>
      </div>

      <div class="row">
        <div class="label">Group Utama Pengembangan/Pengadaan (1)</div>
        <div class="box">
          @if(count($g1))
            <ul>
              @foreach($g1 as $v)
                <li>{{ $v }}</li>
              @endforeach
            </ul>
          @else
            <span class="small">-</span>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="label">Contact Person (Grup User/Nama Group Penanggungjawab Pekerjaan)</div>
        <div class="box box-muted">
          <table>
            <tr>
              <td class="col">
                <div class="small"><b>Name</b></div>
                <div>{{ $wm['name'] }}</div>
              </td>
              <td class="col">
                <div class="small"><b>Phone</b></div>
                <div>{{ $wm['phone'] }}</div>
              </td>
              <td class="col" style="padding-right:0;">
                <div class="small"><b>Email</b></div>
                <div>{{ $wm['email'] }}</div>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="label">Group Utama Pengembangan/Pengadaan (2)</div>
        <div class="box">
          @if(count($g2))
            <ul>
              @foreach($g2 as $v)
                <li>{{ $v }}</li>
              @endforeach
            </ul>
          @else
            <span class="small">-</span>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="label">Nama Lengkap Penyusun Dokumen Pengembangan/Pengadaan</div>
        <div class="box">
          @if(count($comp))
            <ul>
              @foreach($comp as $v)
                <li>{{ $v }}</li>
              @endforeach
            </ul>
          @else
            <span class="small">-</span>
          @endif
        </div>
      </div>

    </div>
  </div>

  {{-- ===================== RBB USERS ===================== --}}
  <div class="section">
    <div class="section-h">Program Kerja & Anggaran di RBB Divisi Users</div>
    <div class="section-b">
      <table>
        <tr>
          <td class="col">
            <div class="label">Kode RBB</div>
            <div class="box">{{ data_get($rbbUsers,'kode','-') ?: '-' }}</div>
          </td>
          <td class="col">
            <div class="label">Nama Program Kerja</div>
            <div class="box">{{ data_get($rbbUsers,'nama','-') ?: '-' }}</div>
          </td>
          <td class="col" style="padding-right:0;">
            <div class="label">Anggaran</div>
            <div class="box">{{ data_get($rbbUsers,'anggaran','-') ?: '-' }}</div>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- ===================== RBB IT ===================== --}}
  <div class="section">
    <div class="section-h">Program Kerja & Anggaran di RBB Divisi Information Technology</div>
    <div class="section-b">
      <table class="grid4">
        <tr>
          <td>
            <div class="label">Kode RBB</div>
            <div class="box">{{ data_get($rbbIt,'kode','-') ?: '-' }}</div>
          </td>
          <td>
            <div class="label">Nama Program Kerja</div>
            <div class="box">{{ data_get($rbbIt,'nama','-') ?: '-' }}</div>
          </td>
          <td>
            <div class="label">Bundling Anggaran</div>
            <div class="box">{{ data_get($rbbIt,'bundling','-') ?: '-' }}</div>
          </td>
          <td style="padding-right:0;">
            <div class="label">Anggaran</div>
            <div class="box">{{ data_get($rbbIt,'anggaran','-') ?: '-' }}</div>
          </td>
        </tr>
      </table>
    </div>
  </div>

  {{-- ===================== FORM TAMBAHAN IAG ===================== --}}
  @if(
    data_get($iag,'kode_project') ||
    data_get($iag,'nama_project') ||
    count($itag) ||
    count($itw) ||
    count($kar)
  )
    <div class="section">
      <div class="section-h">Form Tambahan IAG</div>
      <div class="section-b">

        <div class="row">
          <table class="grid2">
            <tr>
              <td>
                <div class="label">Kode Project</div>
                <div class="box">{{ data_get($iag,'kode_project','-') ?: '-' }}</div>
              </td>
              <td>
                <div class="label">Nama Project</div>
                <div class="box">{{ data_get($iag,'nama_project','-') ?: '-' }}</div>
              </td>
            </tr>
          </table>
        </div>

        <div class="row">
          <div class="label">IT Architecture Governance</div>
          <div class="box">
            @if(count($itag))
              <ul>
                @foreach($itag as $v)
                  <li>{{ $v }}</li>
                @endforeach
              </ul>
            @else
              <span class="small">-</span>
            @endif
          </div>
        </div>

        <div class="row">
          <div class="label">IT Technical Writer</div>
          <div class="box">
            @if(count($itw))
              <ul>
                @foreach($itw as $v)
                  <li>{{ $v }}</li>
                @endforeach
              </ul>
            @else
              <span class="small">-</span>
            @endif
          </div>
        </div>

        <div class="row">
          <div class="label">Karakteristik</div>
          <div class="box">
            @if(count($kar))
              <ul>
                @foreach($kar as $v)
                  <li>{{ $v }}</li>
                @endforeach
              </ul>
            @else
              <span class="small">-</span>
            @endif
          </div>
        </div>

      </div>
    </div>
  @endif

  {{-- ===================== ALASAN REJECT ===================== --}}
  @if(!empty($p?->rejection_reason))
    <div class="section">
      <div class="section-h">Alasan Reject</div>
      <div class="section-b">
        <div class="box">{{ $p->rejection_reason }}</div>
      </div>
    </div>
  @endif

</div>
</body>
</html>