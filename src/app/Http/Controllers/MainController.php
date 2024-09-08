<?php

namespace App\Http\Controllers;

use App\Models\FormHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public static function index(Request $request) {
        $context['pageTitle'] = 'Home';
        $context['templateName'] = 'form';
        $context['fileUploadGroup'] = [];
        $context['signUploadGroup'] = [];
        $allFHs = FormHistory::all();
        foreach ($allFHs as $key => $fh) {
            $context['formHistories'][$key]['id'] = $fh->id;
            $context['formHistories'][$key]['created_at'] = $fh->created_at;
        }
        return view('main', $context);
    }

    public static function getFormData(Request $request) {
        $context['pageTitle'] = 'Home';
        $context['templateName'] = 'form';

        $context['title'] = $request->post('title');
        $context['infoList'] = $request->post('infoList');
        $context['list'] = $request->post('list');
        $context['allLists'] = $request->post('allLists');

        $allFiles = $request->allFiles();
        $files = !empty($allFiles['file']) ? $allFiles['file'] : [];
        foreach ($files as $key => $file) {
            $fileUploadGroup['number'] = $key + 1 . '.';
            $fileUploadGroup['docShortName'] = $request->post('docShortName')[$key];
            $fileUploadGroup['docName'] = $request->post('docName')[$key];
            $fileUploadGroup['docChanges'] = $request->post('docChanges')[$key];
            $fileUploadGroup['fileModifiedTime'] = $request->post('fileModifiedTime')[$key];
            $context['fileUploadGroup'][$key] = $fileUploadGroup;

            $context['fileInfo'][$key]['fileName'] = $file->getClientOriginalName();
            $context['fileInfo'][$key]['fileSize'] = number_format($file->getSize(), 0, '', ' ') . " байт";
            $context['fileInfo'][$key]['fileMD5'] = md5_file($file->getRealPath());

            $change_time = \App\Classes\pdfExtractor::get_change_time($file);
            if ($change_time === false) {
                $context['fileInfo'][$key]['changeTimeDescription'] = 'Время изменения: ';
                $context['fileInfo'][$key]['changeTime'] = $request->post('fileModifiedTime')[$key];
            } else {
                $context['fileInfo'][$key]['changeTimeDescription'] = 'Время изменения из PDF: ';
                $context['fileInfo'][$key]['changeTime'] = $change_time;
            }
        }

        $signFiles = !empty($allFiles['sign']) ? $allFiles['sign'] : [];
        foreach ($signFiles as $key => $file) {
            $signUploadGroup['number'] = $key + 1 . '.';
            $signUploadGroup['work'] = $request->post('work')[$key];
            $signUploadGroup['family'] = $request->post('family')[$key];
            $signUploadGroup['signDate'] = $request->post('signDate')[$key];
            if (!empty($file)) {
                $type = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $signUploadGroup['sign'] = 'data:image/' . $type . ';base64,' . base64_encode($file->getContent());
            }
            $context['signUploadGroup'][$key] = $signUploadGroup;
        }

        $res = \App\Classes\pdfExtractor::get_pdf_content($context);
        Storage::disk('public')->put('info.pdf', $res, 'public');

        $copy_context = $context;
        unset($copy_context['fileInfo']);
        $fh = new FormHistory();
        $fh->context = json_encode($copy_context, JSON_UNESCAPED_SLASHES);
        $fh->save();

        $allFHs = FormHistory::all();
        foreach ($allFHs as $key => $fh) {
            $context['formHistories'][$key]['id'] = $fh->id;
            $context['formHistories'][$key]['created_at'] = $fh->created_at;
        }

        return view('main', $context);
    }

    public static function getFormHistory($id, Request $request) {
        $data = FormHistory::findOrFail($id);
        $context = json_decode($data->context, true);

        $allFHs = FormHistory::all();
        foreach ($allFHs as $key => $fh) {
            $context['formHistories'][$key]['id'] = $fh->id;
            $context['formHistories'][$key]['created_at'] = $fh->created_at;
        }

        return view('main', $context);
    }

    public function deleteFormHistory($id, Request $request) {
        $data = FormHistory::findOrFail($id);
        $data->delete();

        $context['pageTitle'] = 'Home';
        $context['templateName'] = 'form';
        $context['fileUploadGroup'] = [];
        $context['signUploadGroup'] = [];

        $allFHs = FormHistory::all();
        if ($allFHs->count() === 0) {
            return Redirect::route('home');
        }
        foreach ($allFHs as $key => $fh) {
            $context['formHistories'][$key]['id'] = $fh->id;
            $context['formHistories'][$key]['created_at'] = $fh->created_at;
        }

        return view('main', $context);
    }
}
