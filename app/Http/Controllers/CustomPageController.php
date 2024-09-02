<?php

namespace App\Http\Controllers;

use App\Models\CustomBlock;
use App\Models\CustomContent;
use App\Models\CustomGroup;
use App\Models\CustomImage;
use App\ResponseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomPageController extends Controller
{
    public function getAboutPage()
    {
        $page = CustomContent::whereSlug("ABOUT")->first();
        $image = CustomImage::whereSlug("ABOUT")->first();
        return ResponseService::success([
            'title' => $page->title,
            'description' => $page->description,
            'file_path' => $image->image
        ]);
    }

    public function getPage($pageSlug)
    {
        $result = CustomContent::whereSlug(strtoupper($pageSlug))->first();
        $image = CustomImage::whereSlug(strtoupper($pageSlug))->first();
        return Inertia::render('CustomPage', [
            'content' => $result,
            'image' => $image
        ]);
    }

    public function contactPage()
    {
        $result = [];
        $map = CustomBlock::whereSlug("CONTACT-MAP")->first();
        $content = CustomContent::whereSlug("CONTACT")->first();
        $job = CustomGroup::whereSlug('CONTACT-JOB')->with('fields')->first();
        $address = CustomGroup::whereSlug('CONTACT-ADDRESS')->with('fields')->first();
        $phones = CustomGroup::whereSlug('CONTACT-PHONES')->with('fields')->first();
        return Inertia::render('EditContactPage', [
            'contact' => [
                'map' => $map,
                'content' => $content,
                'job' => $job,
                'address' => $address,
                'phones' => $phones
            ]
        ]);
    }

    public function getContactPage()
    {
        $map = CustomBlock::whereSlug("CONTACT-MAP")->first();
        $content = CustomContent::whereSlug("CONTACT")->first();
        $groups = CustomGroup::whereIn('slug', ['CONTACT-JOB', 'CONTACT-ADDRESS', 'CONTACT-PHONES'])
            ->whereIsActive(true)
            ->orderBy('sort_order', 'ASC')
            ->with([
                'fields' => function ($query) {
                    $query->whereIsActive(true)->orderBy('sort_order', 'ASC');
                }
            ])
            ->get()->map(function ($item) {
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'list' => $item->fields->map(function ($item) {
                        return [
                            'title' => $item->title,
                            'href' => $item->href,
                        ];
                    })
                ];
            });

        return ResponseService::success([
            'title' => $content->title,
            'map_link' => $map->content,
            'content' => $groups
        ]);
    }

    public function updateContact(Request $request)
    {
        $contentTitle = $request->content_title;
        $content = CustomContent::whereSlug("CONTACT")->first();
        $content->title = $contentTitle;
        $content->save();

        $mapHref = $request->map_link;
        $map = CustomBlock::whereSlug("CONTACT-MAP")->first();
        $map->content = $mapHref;
        $map->save();

        $this->updateGroups("CONTACT-JOB", $request->job);
        $this->updateGroups("CONTACT-ADDRESS", $request->address);
        $this->updateGroups("CONTACT-PHONES", $request->phones);
    }

    private function updateGroups($slug, $group)
    {
        $current = CustomGroup::whereSlug($slug)->first();
        $current->title = $group['title'];
        $current->is_active = $group['is_active'];
        $current->sort_order = $group['sort_order'];
        collect($group['fields'])->each(function ($item) use (&$current) {
            $current->fields()->updateOrCreate([
                'id' => $item['id'] ?? null
            ], [
                'slug' => $item['slug'],
                'title' => $item['title'],
                'is_active' => $item['is_active'],
                'sort_order' => $item['sort_order'],
                'href' => $item['href'],
            ]);
        });
        $current->save();
    }

    public function updatePage(Request $request, $pageSlug)
    {
        $title = $request->input('title');
        $description = $request->input('description');
        $this->uploadImage($request, $pageSlug);
        CustomContent::whereSlug($pageSlug)->update([
            'title' => $title,
            'description' => $description
        ]);
        return redirect()->back();
    }

    public function uploadImage(Request $request, $pageSlug)
    {
        $data = CustomImage::whereSlug($pageSlug)->first();

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(storage_path('app/public/page-images'), $filename);
            $data['image'] = $filename;
        }
        $data->save();
    }
}
