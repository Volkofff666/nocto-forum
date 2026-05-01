<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $model = match ($type) {
            'article' => Article::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default   => abort(404),
        };

        try {
            Report::create([
                'user_id'         => auth()->id(),
                'reportable_type' => get_class($model),
                'reportable_id'   => $id,
                'reason'          => $request->reason,
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->with('error', 'Вы уже отправили жалобу на этот материал.');
        }

        return back()->with('success', 'Жалоба отправлена. Спасибо!');
    }
}
