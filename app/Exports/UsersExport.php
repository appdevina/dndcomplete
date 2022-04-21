<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('approval', 'area', 'role', 'divisi')->withTrashed()->orderBy('nama_lengkap')->get();
    }

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'username',
            'role',
            'area',
            'divisi',
            'wn',
            'wr',
            'mn',
            'mr',
            'approval'
        ];
    }

    public function map($user): array
    {
        return [
            $user->nama_lengkap,
            $user->username,
            $user->role->name,
            $user->area->name,
            $user->divisi->name,
            $user->wn ? 'YES' : 'NO',
            $user->wr ? 'YES' : 'NO',
            $user->mn ? 'YES' : 'NO',
            $user->mr ? 'YES' : 'NO',
            $user->approval->nama_lengkap,
        ];
    }
}
