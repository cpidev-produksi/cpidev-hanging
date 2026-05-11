<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ShfiFile;
use App\Models\ShfiFolder;
use App\Models\ShfiRoot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InventoryApiController extends Controller
{
    public function roots()
    {
        return response()->json([
            'data' => ShfiRoot::query()->orderBy('name')->get(),
        ]);
    }

    public function breadcrumbs(Request $request)
    {
        $data = $request->validate([
            'root_id' => ['required','integer', Rule::exists('shfi_roots','id')],
            'folder_id' => ['nullable','integer', Rule::exists('shfi_folders','id')],
        ]);

        $trail = [];
        $folderId = $data['folder_id'] ?? null;

        while ($folderId) {
            $folder = ShfiFolder::query()
                ->where('root_id', $data['root_id'])
                ->where('id', $folderId)
                ->first();

            if (!$folder) break;
            $trail[] = ['id' => $folder->id, 'name' => $folder->name];
            $folderId = $folder->parent_id;
        }

        $trail = array_reverse($trail);

        return response()->json(['data' => $trail]);
    }

    public function list(Request $request)
    {
        $data = $request->validate([
            'root_id' => ['required','integer', Rule::exists('shfi_roots','id')],
            'folder_id' => ['nullable','integer', Rule::exists('shfi_folders','id')],
            'q' => ['nullable','string','max:200'],
            'month' => ['nullable','date_format:Y-m'],
            'from' => ['nullable','date_format:Y-m-d'],
            'to' => ['nullable','date_format:Y-m-d'],
            'sort' => ['nullable', Rule::in(['name','uploaded_at'])],
            'dir' => ['nullable', Rule::in(['asc','desc'])],
        ]);

        $sort = $data['sort'] ?? 'name';
        $dir  = $data['dir'] ?? 'asc';
        $isSearching = !empty($data['q']);

        // ── Folders ──────────────────────────────────────────────────────────────
        $foldersQ = ShfiFolder::query()->where('root_id', $data['root_id']);

        if ($isSearching) {
            // Global: cari di semua folder dalam root ini
            $foldersQ->where('name', 'like', '%'.$data['q'].'%');
        } else {
            // Normal: hanya tampilkan anak langsung dari folder saat ini
            $foldersQ->where('parent_id', $data['folder_id'] ?? null);
        }

        $folders = $foldersQ->orderBy('name', 'asc')->get();

        // ── Files ─────────────────────────────────────────────────────────────────
        $filesQ = ShfiFile::query()->where('root_id', $data['root_id']);

        if ($isSearching) {
            // Global: cari di seluruh root, tanpa filter folder_id
            $filesQ->where('name', 'like', '%'.$data['q'].'%');
        } else {
            $filesQ->where('folder_id', $data['folder_id'] ?? null);
        }

        $tz = 'Asia/Jakarta';

        if (!empty($data['month'])) {
            $start = Carbon::createFromFormat('Y-m', $data['month'], $tz)->startOfMonth()->utc();
            $end   = Carbon::createFromFormat('Y-m', $data['month'], $tz)->endOfMonth()->utc();
            $filesQ->whereBetween('uploaded_at', [$start, $end]);
        } elseif (!empty($data['from']) || !empty($data['to'])) {
            $from = !empty($data['from'])
                ? Carbon::createFromFormat('Y-m-d', $data['from'], $tz)->startOfDay()->utc()
                : Carbon::now($tz)->subYears(50)->startOfDay()->utc();

            $to = !empty($data['to'])
                ? Carbon::createFromFormat('Y-m-d', $data['to'], $tz)->endOfDay()->utc()
                : Carbon::now($tz)->endOfDay()->utc();

            $filesQ->whereBetween('uploaded_at', [$from, $to]);
        }

        if ($sort === 'uploaded_at') $filesQ->orderBy('uploaded_at', $dir);
        else $filesQ->orderBy('name', $dir);

        $files = $filesQ->get()->map(function (ShfiFile $f) use ($isSearching) {
            $result = [
                'id'           => $f->id,
                'name'         => $f->name,
                'mime_type'    => $f->mime_type,
                'size'         => $f->size,
                'uploaded_at'  => optional($f->uploaded_at)->toISOString(),
                'download_url' => route('inventory.api.download', $f->id),
                'preview_url'  => route('inventory.api.preview', $f->id),
            ];

            // Saat global search: sertakan folder_path agar user tahu lokasi file
            if ($isSearching && $f->folder_id) {
                $result['folder_id']   = $f->folder_id;
                $result['folder_name'] = optional(ShfiFolder::find($f->folder_id))->name;
            }

            return $result;
        });

        return response()->json([
            'data' => [
                'folders' => $folders,
                'files' => $files,
                'is_searching' => $isSearching,
            ],
        ]);
    }

    public function createFolder(Request $request)
    {
        $data = $request->validate([
            'root_id' => ['required','integer', Rule::exists('shfi_roots','id')],
            'parent_id' => ['nullable','integer', Rule::exists('shfi_folders','id')],
            'name' => ['required','string','max:150'],
        ]);

        if ($this->folderNameExists($data['root_id'], $data['parent_id'] ?? null, $data['name'])) {
            return response()->json(['message' => 'Nama folder sudah ada di lokasi ini'], 422);
        }

        $folder = ShfiFolder::create([
            'root_id' => $data['root_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['data' => $folder]);
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'root_id' => ['required','integer', Rule::exists('shfi_roots','id')],
            'folder_id' => ['nullable','integer', Rule::exists('shfi_folders','id')],
            'file' => ['required','file'],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        if ($this->fileNameExists($data['root_id'], $data['folder_id'] ?? null, $originalName)) {
            return response()->json(['message' => 'Nama file sudah ada di folder ini'], 422);
        }

        $root = ShfiRoot::findOrFail($data['root_id']);
        $ym = now('Asia/Jakarta')->format('Y/m');

        // stored name random to avoid collisions
        $ext = $file->getClientOriginalExtension();
        $storedName = Str::uuid()->toString().($ext ? '.'.$ext : '');
        $path = $file->storeAs("inventory/{$root->slug}/{$ym}", $storedName, 'public');

        $row = ShfiFile::create([
            'root_id' => $data['root_id'],
            'folder_id' => $data['folder_id'] ?? null,
            'name' => $originalName,
            'disk' => 'public',
            'disk_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize() ?: 0,
            'uploaded_by' => $request->user()->id,
            'uploaded_at' => now(),
        ]);

        return response()->json(['data' => $row]);
    }

    public function download(ShfiFile $file)
    {
        $abs = Storage::disk($file->disk)->path($file->disk_path);
        abort_unless(is_file($abs), 404);

        return response()->download($abs, $file->name);
    }

    public function preview(ShfiFile $file)
    {
        $abs = Storage::disk($file->disk)->path($file->disk_path);
        abort_unless(is_file($abs), 404);

        $mime = $file->mime_type ?? '';

        // image thumbnail 220px (GD)
        if (str_starts_with($mime, 'image/')) {
            return $this->imageThumbnailResponse($abs, 220);
        }

        // PDF thumbnail: butuh imagick/ghostscript. Untuk sekarang fallback icon.
        if ($mime === 'application/pdf') {
            return response()->file(public_path('images/icons/pdf.png'));
        }

        return response()->file(public_path('images/icons/file.png'));
    }

    public function rename(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['file','folder'])],
            'id' => ['required','integer'],
            'new_name' => ['required','string','max:190'],
        ]);

        if ($data['type'] === 'folder') {
            $folder = ShfiFolder::findOrFail($data['id']);

            if ($this->folderNameExists($folder->root_id, $folder->parent_id, $data['new_name'], $folder->id)) {
                return response()->json(['message' => 'Nama folder sudah ada di lokasi ini'], 422);
            }

            $folder->update(['name' => $data['new_name']]);
            return response()->json(['data' => $folder]);
        }

        $file = ShfiFile::findOrFail($data['id']);

        if ($this->fileNameExists($file->root_id, $file->folder_id, $data['new_name'], $file->id)) {
            return response()->json(['message' => 'Nama file sudah ada di folder ini'], 422);
        }

        $file->update(['name' => $data['new_name']]);
        return response()->json(['data' => $file]);
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['file','folder'])],
            'id' => ['required','integer'],
            'target_folder_id' => ['required','integer', Rule::exists('shfi_folders','id')],
        ]);

        if ($data['type'] === 'folder') {
            $folder = ShfiFolder::findOrFail($data['id']);

            // prevent move into itself/descendant
            if ($data['target_folder_id'] && $this->isDescendant($folder->id, $data['target_folder_id'])) {
                return response()->json(['message' => 'Folder tidak boleh dipindah ke dalam dirinya sendiri'], 422);
            }

            if ($this->folderNameExists($folder->root_id, $data['target_folder_id'], $folder->name, $folder->id)) {
                return response()->json(['message' => 'Nama folder sudah ada di tujuan'], 422);
            }

            $folder->update(['parent_id' => $data['target_folder_id'] ?? null]);
            return response()->json(['data' => $folder]);
        }

        $file = ShfiFile::findOrFail($data['id']);

        if ($this->fileNameExists($file->root_id, $data['target_folder_id'] ?? null, $file->name, $file->id)) {
            return response()->json(['message' => 'Nama file sudah ada di tujuan'], 422);
        }

        $file->update(['folder_id' => $data['target_folder_id'] ?? null]);
        return response()->json(['data' => $file]);
    }

    public function copy(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['file','folder'])],
            'id' => ['required','integer'],
            'target_folder_id' => ['required','integer', Rule::exists('shfi_folders','id')],
        ]);

        return DB::transaction(function () use ($data) {
            if ($data['type'] === 'file') {
                $file = ShfiFile::findOrFail($data['id']);

                if ($this->fileNameExists($file->root_id, $data['target_folder_id'] ?? null, $file->name)) {
                    return response()->json(['message' => 'Nama file sudah ada di tujuan'], 422);
                }

                $newDiskPath = $this->duplicatePhysicalFile($file);

                $new = ShfiFile::create([
                    'root_id' => $file->root_id,
                    'folder_id' => $data['target_folder_id'] ?? null,
                    'name' => $file->name,
                    'disk' => $file->disk,
                    'disk_path' => $newDiskPath,
                    'mime_type' => $file->mime_type,
                    'size' => $file->size,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now(),
                ]);

                return response()->json(['data' => $new]);
            }

            // copy folder (duplicate tree)
            $src = ShfiFolder::findOrFail($data['id']);

            if ($this->folderNameExists($src->root_id, $data['target_folder_id'] ?? null, $src->name)) {
                return response()->json(['message' => 'Nama folder sudah ada di tujuan'], 422);
            }

            $newFolder = ShfiFolder::create([
                'root_id' => $src->root_id,
                'parent_id' => $data['target_folder_id'] ?? null,
                'name' => $src->name,
                'created_by' => Auth::id(),
            ]);

            $this->duplicateFolderTree($src->id, $newFolder->id, $src->root_id);

            return response()->json(['data' => $newFolder]);
        });
    }

    public function softDelete(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['file','folder'])],
            'id' => ['required','integer'],
            'confirm' => ['nullable','boolean'],
        ]);

        if ($data['type'] === 'file') {
            $file = ShfiFile::findOrFail($data['id']);
            $file->delete();
            return response()->json(['data' => true]);
        }

        // folder delete: require confirm flag
        if (!($data['confirm'] ?? false)) {
            return response()->json(['message' => 'Konfirmasi penghapusan folder diperlukan', 'code' => 'NEED_CONFIRM'], 422);
        }

        $folder = ShfiFolder::findOrFail($data['id']);

        DB::transaction(function () use ($folder) {
            $this->softDeleteFolderRecursive($folder->id);
        });

        return response()->json(['data' => true]);
    }

    public function trashList(Request $request)
    {
        $data = $request->validate([
            'root_id' => ['required','integer', Rule::exists('shfi_roots','id')],
            'q' => ['nullable','string','max:200'],
            'sort' => ['nullable', Rule::in(['name','uploaded_at'])],
            'dir' => ['nullable', Rule::in(['asc','desc'])],
        ]);

        $sort = $data['sort'] ?? 'name';
        $dir = $data['dir'] ?? 'asc';

        $foldersQ = ShfiFolder::onlyTrashed()->where('root_id', $data['root_id']);
        $filesQ = ShfiFile::onlyTrashed()->where('root_id', $data['root_id']);

        if (!empty($data['q'])) {
            $foldersQ->where('name', 'like', '%'.$data['q'].'%');
            $filesQ->where('name', 'like', '%'.$data['q'].'%');
        }

        $folders = $foldersQ->orderBy('name', 'asc')->get()->map(function (ShfiFolder $f) {
            return [
                'id'         => $f->id,
                'type'       => 'folder',
                'name'       => $f->name,
                'mime_type'  => null,
                'size'       => null,
                'deleted_at' => optional($f->deleted_at)->toISOString(),
            ];
        });

        if ($sort === 'uploaded_at') $filesQ->orderBy('uploaded_at', $dir);
        else $filesQ->orderBy('name', $dir);

        $files = $filesQ->get()->map(function (ShfiFile $f) {
            return [
                'id'         => $f->id,
                'type'       => 'file',
                'name'       => $f->name,
                'mime_type'  => $f->mime_type,
                'size'       => $f->size,
                'deleted_at' => optional($f->deleted_at)->toISOString(),
            ];
        });

        // Merged flat list: folders first, then files
        $merged = $folders->concat($files)->values();

        return response()->json(['data' => $merged]);
    }

    public function folders(Request $request)
    {
        $data = $request->validate([
            'root_id'   => ['required', 'integer', Rule::exists('shfi_roots', 'id')],
            'parent_id' => ['nullable', 'integer', Rule::exists('shfi_folders', 'id')],
        ]);

        $folders = ShfiFolder::query()
            ->where('root_id', $data['root_id'])
            ->where('parent_id', $data['parent_id'] ?? null)
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return response()->json(['data' => $folders]);
    }

    public function purge(Request $request)
    {
        $request->merge($request->query());
        $data = $request->validate([
            'type' => ['required', Rule::in(['file', 'folder'])],
            'id'   => ['required', 'integer'],
        ]);

        if ($data['type'] === 'file') {
            $file = ShfiFile::onlyTrashed()->findOrFail($data['id']);
            Storage::disk($file->disk)->delete($file->disk_path);
            $file->forceDelete();
        } else {
            $folder = ShfiFolder::onlyTrashed()->findOrFail($data['id']);
            DB::transaction(function () use ($folder) {
                $this->purgeFolderRecursive($folder->id);
            });
        }

        return response()->json(['data' => true]);
    }

    public function emptyTrash(Request $request)
    {
        DB::transaction(function () {
            // Purge all trashed files (physical + record)
            ShfiFile::onlyTrashed()->get()->each(function (ShfiFile $f) {
                Storage::disk($f->disk)->delete($f->disk_path);
                $f->forceDelete();
            });

            // Force-delete all trashed folders (records only, files already gone above)
            ShfiFolder::onlyTrashed()->get()->each->forceDelete();
        });

        return response()->json(['data' => true]);
    }

    public function restore(Request $request)
    {
        $request->merge($request->query());
        $data = $request->validate([
            'type' => ['required', Rule::in(['file','folder'])],
            'id' => ['required','integer'],
        ]);

        if ($data['type'] === 'file') {
            $file = ShfiFile::onlyTrashed()->findOrFail($data['id']);

            if ($this->fileNameExists($file->root_id, $file->folder_id, $file->name)) {
                // auto rename
                $file->name = $this->nextAvailableNameForFile($file->root_id, $file->folder_id, $file->name);
            }

            $file->restore();
            $file->save();

            return response()->json(['data' => true]);
        }

        $folder = ShfiFolder::onlyTrashed()->findOrFail($data['id']);

        DB::transaction(function () use ($folder) {
            // restore folder + children
            $this->restoreFolderRecursive($folder->id);
        });

        return response()->json(['data' => true]);
    }

    // ======================
    // Helpers
    // ======================

    private function folderNameExists(int $rootId, $parentId, string $name, ?int $ignoreId = null): bool
    {
        $q = ShfiFolder::query()
            ->where('root_id', $rootId)
            ->where('parent_id', $parentId)
            ->where('name', $name)
            ->whereNull('deleted_at');

        if ($ignoreId) $q->where('id', '!=', $ignoreId);
        return $q->exists();
    }

    private function fileNameExists(int $rootId, $folderId, string $name, ?int $ignoreId = null): bool
    {
        $q = ShfiFile::query()
            ->where('root_id', $rootId)
            ->where('folder_id', $folderId)
            ->where('name', $name)
            ->whereNull('deleted_at');

        if ($ignoreId) $q->where('id', '!=', $ignoreId);
        return $q->exists();
    }

    private function isDescendant(int $folderId, int $targetFolderId): bool
    {
        if ($folderId === $targetFolderId) return true;

        $cur = ShfiFolder::find($targetFolderId);
        while ($cur) {
            if ($cur->parent_id === $folderId) return true;
            $cur = $cur->parent_id ? ShfiFolder::find($cur->parent_id) : null;
        }
        return false;
    }

    private function purgeFolderRecursive(int $folderId): void
    {
        // purge child files from disk + DB
        ShfiFile::onlyTrashed()->where('folder_id', $folderId)->get()->each(function (ShfiFile $f) {
            Storage::disk($f->disk)->delete($f->disk_path);
            $f->forceDelete();
        });

        // also purge any non-trashed files (children of a trashed parent)
        ShfiFile::query()->where('folder_id', $folderId)->get()->each(function (ShfiFile $f) {
            Storage::disk($f->disk)->delete($f->disk_path);
            $f->forceDelete();
        });

        // recurse into sub-folders (trashed or not)
        $allChildren = ShfiFolder::withTrashed()->where('parent_id', $folderId)->get();
        foreach ($allChildren as $child) {
            $this->purgeFolderRecursive($child->id);
        }

        // force-delete self
        $folder = ShfiFolder::withTrashed()->findOrFail($folderId);
        $folder->forceDelete();
    }

    private function softDeleteFolderRecursive(int $folderId): void
    {
        // delete child files
        ShfiFile::query()->where('folder_id', $folderId)->whereNull('deleted_at')->get()->each->delete();

        // delete child folders recursively
        $children = ShfiFolder::query()->where('parent_id', $folderId)->whereNull('deleted_at')->get();
        foreach ($children as $child) {
            $this->softDeleteFolderRecursive($child->id);
        }

        // delete self
        $folder = ShfiFolder::findOrFail($folderId);
        $folder->delete();
    }

    private function restoreFolderRecursive(int $folderId): void
    {
        $folder = ShfiFolder::onlyTrashed()->find($folderId) ?? ShfiFolder::findOrFail($folderId);

        // restore this folder if trashed
        if ($folder->trashed()) {
            // auto rename if needed in parent
            if ($this->folderNameExists($folder->root_id, $folder->parent_id, $folder->name)) {
                $folder->name = $this->nextAvailableNameForFolder($folder->root_id, $folder->parent_id, $folder->name);
            }
            $folder->restore();
            $folder->save();
        }

        // restore direct files
        ShfiFile::onlyTrashed()->where('folder_id', $folder->id)->get()->each(function (ShfiFile $f) {
            if ($this->fileNameExists($f->root_id, $f->folder_id, $f->name)) {
                $f->name = $this->nextAvailableNameForFile($f->root_id, $f->folder_id, $f->name);
            }
            $f->restore();
            $f->save();
        });

        // restore subfolders
        $sub = ShfiFolder::onlyTrashed()->where('parent_id', $folder->id)->get();
        foreach ($sub as $sf) {
            $this->restoreFolderRecursive($sf->id);
        }
    }

    private function nextAvailableNameForFolder(int $rootId, $parentId, string $base): string
    {
        $i = 1;
        $candidate = $base;
        while ($this->folderNameExists($rootId, $parentId, $candidate)) {
            $candidate = $base." ({$i})";
            $i++;
        }
        return $candidate;
    }

    private function nextAvailableNameForFile(int $rootId, $folderId, string $base): string
    {
        $i = 1;
        $candidate = $base;

        $name = pathinfo($base, PATHINFO_FILENAME);
        $ext = pathinfo($base, PATHINFO_EXTENSION);
        $extPart = $ext ? '.'.$ext : '';

        while ($this->fileNameExists($rootId, $folderId, $candidate)) {
            $candidate = $name." ({$i})".$extPart;
            $i++;
        }
        return $candidate;
    }

    private function duplicatePhysicalFile(ShfiFile $file): string
    {
        $disk = Storage::disk($file->disk);

        $srcAbs = $disk->path($file->disk_path);
        abort_unless(is_file($srcAbs), 404);

        // keep same directory
        $dir = Str::beforeLast($file->disk_path, '/');
        $ext = pathinfo($file->disk_path, PATHINFO_EXTENSION);
        $newName = Str::uuid()->toString().($ext ? '.'.$ext : '');
        $newRel = ($dir ? $dir.'/' : '').$newName;

        $disk->put($newRel, file_get_contents($srcAbs));

        return $newRel;
    }

    private function duplicateFolderTree(int $srcFolderId, int $dstFolderId, int $rootId): void
    {
        // copy files in folder
        $files = ShfiFile::query()->where('folder_id', $srcFolderId)->whereNull('deleted_at')->get();
        foreach ($files as $f) {
            // ensure unique name in destination
            $newName = $f->name;
            if ($this->fileNameExists($rootId, $dstFolderId, $newName)) {
                $newName = $this->nextAvailableNameForFile($rootId, $dstFolderId, $newName);
            }

            $newDiskPath = $this->duplicatePhysicalFile($f);

            ShfiFile::create([
                'root_id' => $rootId,
                'folder_id' => $dstFolderId,
                'name' => $newName,
                'disk' => $f->disk,
                'disk_path' => $newDiskPath,
                'mime_type' => $f->mime_type,
                'size' => $f->size,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);
        }

        // copy subfolders
        $subFolders = ShfiFolder::query()->where('parent_id', $srcFolderId)->whereNull('deleted_at')->get();
        foreach ($subFolders as $sf) {
            $newFolderName = $sf->name;
            if ($this->folderNameExists($rootId, $dstFolderId, $newFolderName)) {
                $newFolderName = $this->nextAvailableNameForFolder($rootId, $dstFolderId, $newFolderName);
            }

            $newFolder = ShfiFolder::create([
                'root_id' => $rootId,
                'parent_id' => $dstFolderId,
                'name' => $newFolderName,
                'created_by' => Auth::id(),
            ]);

            $this->duplicateFolderTree($sf->id, $newFolder->id, $rootId);
        }
    }

    private function imageThumbnailResponse(string $absPath, int $maxSize)
    {
        // very small GD thumbnail
        $info = @getimagesize($absPath);
        if (!$info) return response()->file($absPath);

        [$w, $h, $type] = $info;
        if ($w <= 0 || $h <= 0) return response()->file($absPath);

        $scale = min($maxSize / $w, $maxSize / $h, 1);
        $nw = (int) floor($w * $scale);
        $nh = (int) floor($h * $scale);

        switch ($type) {
            case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($absPath); break;
            case IMAGETYPE_PNG:  $src = @imagecreatefrompng($absPath); break;
            case IMAGETYPE_GIF:  $src = @imagecreatefromgif($absPath); break;
            case IMAGETYPE_WEBP: $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absPath) : null; break;
            default: $src = null;
        }
        if (!$src) return response()->file($absPath);

        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $src, 0,0, 0,0, $nw,$nh, $w,$h);

        ob_start();
        imagejpeg($dst, null, 80);
        $bin = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return response($bin, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}