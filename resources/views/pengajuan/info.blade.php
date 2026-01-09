@extends('layouts.app')

@section('content')
<style>
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height .3s ease-out; }
  .accordion-content.active { max-height: 500px; transition: max-height .3s ease-in; }
  .rotate-180 { transform: rotate(180deg); }
  .chevron-transition { transition: transform .3s ease; }
</style>

<div class="max-w-5xl">
  <div class="flex items-center justify-between mb-8">
    <h1 class="text-3xl font-semibold text-gray-900">FAQ</h1>

    <div class="relative w-80">
      <input
        id="faqSearch"
        type="search"
        placeholder="Search"
        class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
      >
      <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <path d="m21 21-4.35-4.35"></path>
      </svg>
    </div>
  </div>

  <div id="faqList" class="space-y-4">
    @php
      $faqs = [
        ['q' => 'Bagaimana jika saya tidak memiliki semua detail anggaran saat ini?', 'a' => 'Anda dapat memasukkan estimasi awal atau rentang anggaran. Catat di kolom Catatan/Keterangan bahwa ini adalah data estimasi yang akan diverifikasi pada tahap berikutnya (misalnya, tahap persetujuan).'],
        ['q' => 'Apakah saya bisa menyimpan draft formulir dan melanjutkannya nanti?', 'a' => 'Ya, sistem memungkinkan Anda untuk menyimpan draft formulir. Anda dapat kembali kapan saja untuk melanjutkan pengisian dan mengirimkan formulir ketika sudah lengkap.'],
        ['q' => 'Informasi apa saja yang wajib saya sediakan?', 'a' => 'Informasi wajib meliputi nama proyek, tipe proyek, contact person, group bidang pengguna, program kerja & anggaran (RKAP), sumber anggaran, dan informasi anggota/reviewer. Semua field yang ditandai dengan tanda bintang (*) merah wajib diisi.'],
        ['q' => 'Bagaimana cara mengakses formulir registrasi?', 'a' => 'Anda dapat mengakses formulir registrasi melalui menu "Pengajuan" di sidebar navigasi.'],
        ['q' => 'Berapa lama waktu yang dibutuhkan untuk mengisi seluruh formulir?', 'a' => 'Waktu pengisian formulir bervariasi, namun rata-rata 15-30 menit.'],
        ['q' => 'Siapa yang harus menggunakan formulir ini?', 'a' => 'Formulir ini digunakan oleh semua pihak yang ingin mengajukan proyek baru.'],
        ['q' => 'Apa fungsi utama dari aplikasi formulir registrasi proyek ini?', 'a' => 'Untuk mengelola proses registrasi proyek secara digital dan memudahkan tracking status approval.'],
        ['q' => 'Bagaimana saya tahu bahwa registrasi proyek saya sudah berhasil diterima?', 'a' => 'Setelah submit, Anda dapat melihat status proyek di dashboard.'],
        ['q' => 'Berapa lama proses peninjauan dan persetujuan proyek?', 'a' => 'Biasanya 3-5 hari kerja, tergantung kompleksitas & ketersediaan reviewer.'],
      ];
    @endphp

    @foreach($faqs as $i => $f)
      @php $id = $i+1; @endphp
      <div class="faq-item bg-white border border-gray-200 rounded-lg" data-q="{{ strtolower($f['q'].' '.$f['a']) }}">
        <button class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition" onclick="toggleAccordion({{ $id }})">
          <span class="text-base font-medium text-gray-900">{{ $f['q'] }}</span>
          <svg id="chevron-{{ $id }}" class="w-5 h-5 text-gray-500 chevron-transition" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>

        <div id="content-{{ $id }}" class="accordion-content {{ $id === 1 ? 'active' : '' }}">
          <div class="px-6 pb-4 text-sm text-gray-600 leading-relaxed">
            {{ $f['a'] }}
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-12 bg-indigo-600 rounded-lg p-8">
    <h2 class="text-white text-lg font-semibold mb-6">Contact Us :</h2>
    <div class="space-y-4">
      <div class="flex items-center gap-3 text-white">
        <span>+62812345678</span>
      </div>
      <div class="flex items-center gap-3 text-white">
        <span>email@gmail.com</span>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleAccordion(id) {
    const content = document.getElementById(`content-${id}`);
    const chevron = document.getElementById(`chevron-${id}`);

    for (let i = 1; i <= 9; i++) {
      if (i !== id) {
        document.getElementById(`content-${i}`)?.classList.remove('active');
        document.getElementById(`chevron-${i}`)?.classList.remove('rotate-180');
      }
    }

    content?.classList.toggle('active');
    chevron?.classList.toggle('rotate-180');
  }

  const input = document.getElementById('faqSearch');
  const items = () => Array.from(document.querySelectorAll('.faq-item'));

  input?.addEventListener('input', function () {
    const q = (this.value || '').toLowerCase().trim();

    items().forEach(el => {
      const hay = el.getAttribute('data-q') || '';
      el.classList.toggle('hidden', q && !hay.includes(q));
    });
  });
</script>
@endsection