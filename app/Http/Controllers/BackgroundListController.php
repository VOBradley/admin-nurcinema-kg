<?php

namespace App\Http\Controllers;

use App\Models\BackgroundList;
use App\Models\CustomImage;
use App\ResponseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BackgroundListController extends Controller
{
    public function createBackground(Request $request)
    {
        $active = $request->input('active');
        $data = [
          'active' => $active,
          'image' => null,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(storage_path('app/public/page-images'), $filename);
            $data['image'] = $filename;
        }
        $background = BackgroundList::create($data);
        return redirect()->back();
    }

    public function updateBackground(Request $request, $id)
    {
        $background = BackgroundList::findOrFail($id);

        $background->active = $request->input('active');

        if ($request->file('image')) {
            // Удаляем старое изображение, если оно есть
            if ($background->image && file_exists(storage_path('app/public/page-images/' . $background->image))) {
                unlink(storage_path('app/public/page-images/' . $background->image));
            }

            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(storage_path('app/public/page-images'), $filename);
            $background->image = $filename;
        }

        $background->save();

        return redirect()->route('backgrounds.list');
    }

    // Удаление фона
    public function deleteBackground($id)
    {
        $background = BackgroundList::findOrFail($id);

        // Удаляем изображение из файловой системы, если оно существует
        if ($background->image && file_exists(storage_path('app/public/page-images/' . $background->image))) {
            unlink(storage_path('app/public/page-images/' . $background->image));
        }

        $background->delete();

        return redirect()->route('backgrounds.list');
    }

    // Получение списка фонов
    public function getBackgrounds()
    {
        $backgrounds = BackgroundList::all();
        return Inertia::render('Backgrounds', [
            'bgs' => $backgrounds,
        ]);
    }

    public function getBgList()
    {
        $backgrounds = BackgroundList::where('active', true)->get();
        return ResponseService::success($backgrounds);
    }
}
