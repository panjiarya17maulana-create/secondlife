@extends('layouts.app')
@section('title', 'Chat Transaksi — SecondLife')
@section('content')

<div class="detail-trx-wrap">
  <div style="margin-bottom:1rem">
    <a href="{{ route('transaksi.detail', $transaksi->id_transaksi) }}" style="color:var(--accent);font-size:13px">← Kembali ke Detail Transaksi</a>
  </div>

  <div class="trx-detail-grid" style="grid-template-columns:1fr 1fr">
    <div>
      <div class="info-card" style="margin-bottom:1rem">
        <div class="info-card-title">💬 Chat Transaksi #{{ $transaksi->id_transaksi }}</div>
        <p style="font-size:13px;color:var(--text2)">
          {{ $transaksi->pembeli->nama }} ↔ {{ $transaksi->penjual->nama }}
        </p>
      </div>
    </div>

    <div class="chat-box">
      <div class="chat-header">
        💬 Chat dengan {{ $user->id_user === $transaksi->id_pembeli ? $transaksi->penjual->nama : $transaksi->pembeli->nama }}
      </div>

      <div class="chat-messages" id="chatMessages">
        @forelse($transaksi->chat as $c)
          @php $mine = $c->id_pengirim === $user->id_user; @endphp
          <div class="chat-bubble-wrap {{ $mine ? 'self' : 'other' }}">
            @if(!$mine)
              <div class="chat-avatar">{{ strtoupper(substr($c->pengirim->nama, 0, 2)) }}</div>
            @endif
            <div>
              @if(!$mine)<div class="chat-sender">{{ $c->pengirim->nama }}</div>@endif
              <div class="chat-bubble {{ $mine ? 'bubble-self' : 'bubble-other' }}">{{ $c->pesan }}</div>
              <div class="chat-time">{{ $c->created_at->diffForHumans() }}</div>
            </div>
          </div>
        @empty
          <p style="text-align:center;color:var(--text2);font-size:13px;margin:auto">Belum ada pesan. Mulai chat!</p>
        @endforelse
      </div>

      <form method="POST" action="{{ route('chat.store', $transaksi->id_transaksi) }}" class="chat-input-wrap">
        @csrf
        <textarea name="pesan" class="chat-input" rows="1" placeholder="Tulis pesan..." required></textarea>
        <button type="submit" class="chat-send-btn">➤</button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const msgs = document.getElementById('chatMessages');
  if (msgs) msgs.scrollTop = msgs.scrollHeight;
</script>
@endpush

@endsection
