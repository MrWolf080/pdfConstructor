<?php

namespace App\Classes;

class pdfExtractor {
    protected static function get_html_names_doc(array $context) {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
        <tr>
            <td style="width: 10%; text-align: center"><strong>Номер п/п</strong></td>
            <td style="width: 20%; text-align: center"><strong>Обозначение документа</strong></td>
            <td style="width: 55%; text-align: center"><strong>Наименование документа</strong></td>
            <td style="width: 15%; text-align: center"><strong>Номер последнего изменения (версии)</strong></td>
        </tr>';
        foreach ($context['fileUploadGroup'] as $doc) {
            $str .= '
            <tr>
                <td style="width: 10%; text-align: center">'. $doc['number'] .'</td>
                <td style="width: 20%; text-align: center">'. $doc['docShortName'] .'</td>
                <td style="width: 55%; text-align: center">' . $doc['docName'] . '</td>
                <td style="width: 15%; text-align: center">'. $doc['docChanges'] .'</td>
            </tr>';
        }
        return $str;
    }

    protected static function get_html_md5_doc(array $context) {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
        <tr>
            <td style="width: 30%; text-align: center"><strong>MD5</strong></td>
            <td style="width: 70%; text-align: center"><strong>Значение MD5</strong></td>
        </tr>';
        foreach ($context['fileUploadGroup'] as $doc) {
            $str .= '<tr>
            <td style="width: 30%; text-align: center">'. $doc['number'] .'</td>
            <td style="width: 70%; text-align: center">'. ($doc['fileMD5'] ?? '') .'</td>
        </tr>';
        }
        return $str;
    }

    protected static function get_html_fileinfo_doc(array $context) {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
        <tr>
            <td style="width: 50%; text-align: center"><strong>Наименование файла</strong></td>
            <td style="width: 30%; text-align: center"><strong>Дата и время последнего изменения файла</strong></td>
            <td style="width: 20%; text-align: center"><strong>Размер файла, байт</strong></td>
        </tr>';
        foreach ($context['fileUploadGroup'] as $doc) {
            $str .= '
        <tr>
            <td style="width: 50%; text-align: center">'. ($doc['fileName'] ?? '') .'</td>
            <td style="width: 30%; text-align: center">'. ($doc['changeTime'] ?? '') .'</td>
            <td style="width: 20%; text-align: center">'. ($doc['fileSize'] ?? '') .'</td>
        </tr>';
        }
        return $str;
    }

    protected static function get_html_signs(array $context) {
        if (empty($context['signUploadGroup'])) {
            return '';
        }
        $str = '<tr>
        <td style="width: 30%; text-align: center"><strong>Характер работы</strong></td>
        <td style="width: 20%; text-align: center"><strong>Фамилия</strong></td>
        <td style="width: 30%; text-align: center"><strong>Подпись</strong></td>
        <td style="width: 20%; text-align: center"><strong>Дата подписания</strong></td>
    </tr>';
        foreach ($context['signUploadGroup'] as $sign) {
            $str .= '<tr>
            <td style="width: 30%; text-align: center;"><div style="vertical-align: middle;"></div>'. ($sign['work'] ?? '') .'</td>
            <td style="width: 20%; text-align: center;"><div style="vertical-align: middle;"></div>' . ($sign['family'] ?? '') . '</td>
            <td style="width: 30%; text-align: center;"><img width="100" height="50" src="' . ($sign['base64'] ?? '') .'"></td>
            <td style="width: 20%; text-align: center;"><div style="vertical-align: middle;"></div>' . ($sign['signDate'] ?? '') .'</td>
        </tr>';
        }
        return $str;
    }

    protected static function get_html_lists(array $context) {
        $str = '<tr>
        <td style="width: 40%; text-align: center" rowspan="2"><strong>Информационно-удостоверяющий лист</strong></td>
        <td style="width: 40%; text-align: center" rowspan="2">'. $context['infoList'] .'</td>
        <td style="width: 10%; text-align: center" colspan="1"><strong>Лист</strong></td>
        <td style="width: 10%; text-align: center" colspan="1"><strong>Листов</strong></td>
    </tr>
    <tr>
        <td style="text-align: center">'. $context['list'] .'</td>
        <td style="text-align: center">'. $context['allLists'] .'</td>
    </tr>';
        return $str;
    }

    public static function get_change_time(\Illuminate\Http\UploadedFile $file) {
        $content = $file->getContent();
        preg_match_all('/<xmp:ModifyDate>(.*)<\/xmp:ModifyDate>/', $content, $matches1);
        preg_match_all('/\/ModDate\s?\(D:(.[^)]*)\)/', $content, $matches2);
        if (!empty($matches2[1])) {
            $date = $matches2[1][count($matches2[1]) - 1];
            $date = preg_replace('/\+.*/', '', $date);
            return date('d.m.Y H:i:s', strtotime($date));
        } else if (!empty($matches1[1])) {
            $date = $matches1[1][count($matches1[1]) - 1];
            $date = preg_replace('/\+.*/', '', $date);
            return date('d.m.Y H:i:s', strtotime($date));
        }
        return false;
    }

    public static function get_pdf_content(array $context) {
        $doc = new \TCPDF();
        $doc->SetFont('freeserif', '', 12);
        $doc->setPrintHeader(false);
        $doc->setPrintFooter(false);
        $doc->AddPage();
        if (!empty($context['title'])) {
            $html = '<h4 style="text-align: center">' . $context['title'] . '</h4><br><br>';
            $doc->WriteHTML($html);
        }
        $html = '
        <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%">
    ';
        $html .= self::get_html_names_doc($context);
        $html .= self::get_html_md5_doc($context);
        $html .= self::get_html_fileinfo_doc($context);
        $html .= self::get_html_signs($context);
        $html .= self::get_html_lists($context);
        $html .= '</table>';
        $doc->WriteHTML($html);
        return $doc->Output('info.pdf', 'S');
    }
}
