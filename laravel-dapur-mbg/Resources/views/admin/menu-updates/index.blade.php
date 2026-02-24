@extends('layouts.admin')

@section('title', 'Update Menu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Daftar Update Menu</h2>
            <div class="flex space-x-3">
                <input type="text" placeholder="Cari update menu..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-merah">
                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-merah">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">ID</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Judul Update</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Deskripsi</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Supplier</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Status</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody id="allMenuUpdatesTableBody">
                    @forelse($menuUpdates as $update)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $update->id }}</td>
                        <td class="py-3 px-4 font-medium">{{ $update->judul_update }}</td>
                        <td class="py-3 px-4 text-gray-700 max-w-xs">{{ Str::limit($update->deskripsi_update, 50) }}</td>
                        <td class="py-3 px-4">{{ $update->supplier->nama_lengkap ?? $update->supplier->username }}</td>
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($update->tanggal_update)->locale('id_ID')->isoFormat('D MMM YYYY') }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs 
                                @if($update->status == 'approved') bg-green-100 text-green-800
                                @elseif($update->status == 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $update->status == 'approved' ? 'Disetujui' : ($update->status == 'rejected' ? 'Ditolak' : 'Menunggu') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                @if($update->status == 'pending')
                                    <form action="{{ route('admin.menu-updates.approve', $update->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600" 
                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui update menu ini?')">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.menu-updates.reject', $update->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600" 
                                            onclick="return confirm('Apakah Anda yakin ingin menolak update menu ini?')">Tolak</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.menu-updates.show', $update->id) }}" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                            <p>Tidak ada update menu dari Dapur saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-between items-center mt-6">
            <div class="text-gray-600">
                Menampilkan {{ $menuUpdates->firstItem() }}-{{ $menuUpdates->lastItem() }} dari {{ $menuUpdates->total() }} data
            </div>
            <div class="flex space-x-2">
                @if ($menuUpdates->onFirstPage())
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed" disabled>Sebelumnya</button>
                @else
                    <a href="{{ $menuUpdates->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">Sebelumnya</a>
                @endif
                
                @for ($i = 1; $i <= $menuUpdates->lastPage(); $i++)
                    @if ($i == $menuUpdates->currentPage())
                        <span class="px-4 py-2 bg-merah text-white rounded-lg">{{ $i }}</span>
                    @else
                        <a href="{{ $menuUpdates->url($i) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">{{ $i }}</a>
                    @endif
                @endfor
                
                @if ($menuUpdates->hasMorePages())
                    <a href="{{ $menuUpdates->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">Selanjutnya</a>
                @else
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed" disabled>Selanjutnya</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection