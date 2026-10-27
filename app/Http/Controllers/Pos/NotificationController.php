<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function feed(Request $request): JsonResponse
    {
        $query = PosNotification::query()->where('user_id', $request->user()->id);

        return response()->json([
            'total' => (clone $query)->count(),
            'unread' => (clone $query)->whereNull('read_at')->count(),
            'items' => $query->latest('id')->limit(5)->get()->map(fn (PosNotification $item): array => [
                'title' => $item->title,
                'message' => $item->message,
                'type' => $item->type,
                'unread' => $item->read_at === null,
                'read_url' => route('pos.notifications.read', $item),
                'mark_url' => route('pos.notifications.mark', $item),
                'delete_url' => route('pos.notifications.destroy', $item),
            ])->all(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:read,unread']]);
        $notifications = PosNotification::query()->where('user_id', $request->user()->id)
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('title', 'like', '%'.$q.'%')->orWhere('message', 'like', '%'.$q.'%')))
            ->when($filters['status'] ?? null, fn ($query, $status) => $status === 'read' ? $query->whereNotNull('read_at') : $query->whereNull('read_at'))
            ->latest('id')->paginate(20)->withQueryString();

        return view('pos.notifications', compact('notifications'));
    }

    public function read(Request $request, PosNotification $notification): RedirectResponse
    {
        $this->ensureOwner($request, $notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return redirect($notification->url ?: route('pos.notifications.index'));
    }

    public function mark(Request $request, PosNotification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($request, $notification);
        $notification->update(['read_at' => $notification->read_at ?? now()]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Notifikasi ditandai dibaca.'])
            : back()->with('status', 'Notifikasi ditandai dibaca.');
    }

    public function readAll(Request $request): JsonResponse|RedirectResponse
    {
        PosNotification::query()->where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Semua notifikasi ditandai dibaca.'])
            : back()->with('status', 'Semua notifikasi ditandai dibaca.');
    }

    public function destroy(Request $request, PosNotification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($request, $notification);
        $notification->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Notifikasi dihapus.'])
            : back()->with('status', 'Notifikasi dihapus.');
    }

    public function destroyAll(Request $request): JsonResponse|RedirectResponse
    {
        PosNotification::query()->where('user_id', $request->user()->id)->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Semua notifikasi dihapus.'])
            : back()->with('status', 'Semua notifikasi dihapus.');
    }

    private function ensureOwner(Request $request, PosNotification $notification): void
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
    }
}
