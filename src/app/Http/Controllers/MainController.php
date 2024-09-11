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
        self::addFormHistoriesToContext($context);
        return view('main', $context);
    }

    public static function getFormData(Request $request) {
        $context['pageTitle'] = 'Home';
        $context['templateName'] = 'form';

        $context['title'] = $request->post('title');
        $context['infoList'] = $request->post('infoList');
        $context['list'] = $request->post('list');
        $context['allLists'] = $request->post('allLists');

        $files = $request->post('fileMD5');
        foreach ($files as $key => $file) {
            $fileUploadGroup['number'] = $key + 1 . '.';
            $fileUploadGroup['docShortName'] = $request->post('docShortName')[$key];
            $fileUploadGroup['docName'] = $request->post('docName')[$key];
            $fileUploadGroup['docChanges'] = $request->post('docChanges')[$key];
            $fileUploadGroup['fileModifiedTime'] = $request->post('fileModifiedTime')[$key];
            if (!(empty($request->allFiles()['file'][$key]))) {
                $file = $request->allFiles()['file'][$key];
                $fileUploadGroup['fileName'] = $file->getClientOriginalName();
                $fileUploadGroup['fileSize'] = number_format($file->getSize(), 0, '', ' ') . " байт";
                $fileUploadGroup['fileMD5'] = md5_file($file->getRealPath());
                $change_time = \App\Classes\pdfExtractor::get_change_time($file);
                if ($change_time === false) {
                    $fileUploadGroup['changeTimeDescription'] = 'Время изменения: ';
                    $fileUploadGroup['changeTime'] = date('d.m.Y H:i:s', substr($request->post('fileModifiedTime')[$key], 0, 10));
                } else {
                    $fileUploadGroup['changeTimeDescription'] = 'Время изменения из PDF: ';
                    $fileUploadGroup['changeTime'] = $change_time;
                }
            } else {
                $fileUploadGroup['fileName'] = $request->post('fileName')[$key];
                $fileUploadGroup['fileSize'] = $request->post('fileSize')[$key];
                $fileUploadGroup['fileMD5'] = $request->post('fileMD5')[$key];
                $fileUploadGroup['changeTimeDescription'] = $request->post('changeTimeDescription')[$key];
                $fileUploadGroup['changeTime'] = $request->post('changeTime')[$key];
            }
            $context['fileUploadGroup'][$key] = $fileUploadGroup;
        }

        $signFiles = $request->post('signFileName');
        foreach ($signFiles as $key => $file) {
            $signUploadGroup = [];
            $signUploadGroup['number'] = $key + 1 . '.';
            $signUploadGroup['work'] = $request->post('work')[$key];
            $signUploadGroup['family'] = $request->post('family')[$key];
            $signUploadGroup['signDate'] = $request->post('signDate')[$key];
            if (!(empty($request->allFiles()['sign'][$key]))) {
                $file = $request->allFiles()['sign'][$key];
                $type = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $signUploadGroup['base64'] = 'data:image/' . $type . ';base64,' . base64_encode($file->getContent());
                $signUploadGroup['signFileName'] = $file->getClientOriginalName();
            } else {
                $signUploadGroup['base64'] = $request->post('base64')[$key];
                $signUploadGroup['signFileName'] = $request->post('signFileName')[$key];
            }
            $context['signUploadGroup'][$key] = $signUploadGroup;
        }

        self::cleanStorage('public');
        $res = \App\Classes\pdfExtractor::get_pdf_content($context);
        $context['outputFilename'] = !empty($context['infoList']) ? $context['infoList'] . '.pdf' : 'info.pdf';
        Storage::disk('public')->put($context['outputFilename'], $res, 'public');

        $fh = new FormHistory();
        $fh->context = json_encode($context, JSON_UNESCAPED_SLASHES);
        $fh->save();

        self::addFormHistoriesToContext($context);

        return view('main', $context);
    }

    public static function getFormHistory($id, Request $request) {
        $data = FormHistory::findOrFail($id);
        $context = json_decode($data->context, true);

        self::cleanStorage('public');
        $res = \App\Classes\pdfExtractor::get_pdf_content($context);
        $context['outputFilename'] = !empty($context['infoList']) ? $context['infoList'] . '.pdf' : 'info.pdf';
        Storage::disk('public')->put($context['outputFilename'], $res, 'public');

        self::addFormHistoriesToContext($context);

        return view('main', $context);
    }

    public function deleteFormHistory($id, Request $request) {
        $data = FormHistory::findOrFail($id);
        $data->delete();

        $context['pageTitle'] = 'Home';
        $context['templateName'] = 'form';
        $context['fileUploadGroup'] = [];
        $context['signUploadGroup'] = [];

        self::addFormHistoriesToContext($context);
        if (empty($context['formHistories'])) {
            return Redirect::route('home');
        }

        return view('main', $context);
    }

    protected static function cleanStorage(string $disk): void
    {
        $storageFiles = Storage::disk($disk)->allFiles();
        foreach ($storageFiles as $file) {
            if ($file === '.gitignore') {
                continue;
            }
            Storage::disk($disk)->delete($file);
        }
    }

    protected static function addFormHistoriesToContext(array &$context): void
    {
        $allFHs = FormHistory::all();
        foreach ($allFHs as $key => $fh) {
            $context['formHistories'][$key]['id'] = $fh->id;
            $context['formHistories'][$key]['created_at'] = $fh->created_at;
        }
    }
}
