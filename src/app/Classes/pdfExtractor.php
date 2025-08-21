<?php

namespace App\Classes;

class pdfExtractor {

    protected static function get_html_names_doc(array $context):string {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
            <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                <tr>
                    <td style="width: 10%; text-align: center"><strong>Номер п/п</strong></td>
                    <td style="width: 20%; text-align: center"><strong>Обозначение документа</strong></td>
                    <td style="width: 55%; text-align: center"><strong>Наименование документа</strong></td>
                    <td style="width: 15%; text-align: center"><strong>Номер последнего изменения (версии)</strong></td>
                </tr>
        ';
        foreach ($context['fileUploadGroup'] as $doc) {
            $str .= '
                <tr>
                    <td style="width: 10%; text-align: center">'. $doc['number'] .'</td>
                    <td style="width: 20%; text-align: center">'. $doc['docShortName'] .'</td>
                    <td style="width: 55%; text-align: center">' . $doc['docName'] . '</td>
                    <td style="width: 15%; text-align: center">'. $doc['docChanges'] .'</td>
                </tr>
            ';
        }
        $str .= '</table>';
        return $str;
    }

    protected static function get_html_md5_doc(array $context):string {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
            <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                <tr>
                    <td style="width: 30%; text-align: center"><strong>CRC32</strong></td>
                    <td style="width: 70%; text-align: center"><strong>Значение CRC32</strong></td>
                </tr>
        ';
        foreach ($context['fileUploadGroup'] as $doc) {
            $str .= '
                <tr>
                    <td style="width: 30%; text-align: center">'. $doc['number'] .'</td>
                    <td style="width: 70%; text-align: center">'. ($doc['fileMD5'] ?? '') .'</td>
                </tr>
            ';
        }
        $str .= '</table>';
        return $str;
    }

    protected static function get_html_fileinfo_doc(array $context, \TCPDF $document) {
        if (empty($context['fileUploadGroup'])) {
            return '';
        }
        $str = '
            <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                <tr>
                    <td style="width: 50%; text-align: center"><strong>Наименование файла</strong></td>
                    <td style="width: 30%; text-align: center"><strong>Дата и время последнего изменения файла</strong></td>
                    <td style="width: 20%; text-align: center"><strong>Размер файла, байт</strong></td>
                </tr>
        ';
        foreach ($context['fileUploadGroup'] as $doc) {
            if (next($context['fileUploadGroup']) !== false) {
                $str .= '
                    <tr>
                        <td style="width: 50%; text-align: center">'. ($doc['fileName'] ?? '') .'</td>
                        <td style="width: 30%; text-align: center">'. ($doc['changeTime'] ?? '') .'</td>
                        <td style="width: 20%; text-align: center">'. ($doc['fileSize'] ?? '') .'</td>
                    </tr>
                ';
            } else {
                /*
                $html2 = '';
                $html2 .= self::get_html_signs($context);
                $html2 .= self::get_html_lists($context, $document);
                $str .= '</table>';
                $document->writeHTML($str, false);
                $remainingSpace = $document->getPageHeight() - $document->getMargins()['bottom'] - $document->GetY();
                var_dump(self::get_html_height($html2));
                var_dump($remainingSpace);
                if (self::get_html_height($html2) > $remainingSpace % 100) {
                */
                $str .= '</table>';
                $newstr = '
                    <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                        <tr>
                            <td style="width: 50%; text-align: center">'. ($doc['fileName'] ?? '') .'</td>
                            <td style="width: 30%; text-align: center">'. ($doc['changeTime'] ?? '') .'</td>
                            <td style="width: 20%; text-align: center">'. ($doc['fileSize'] ?? '') .'</td>
                        </tr>
                    </table>
                ';
                return [$str, $newstr];
            }
        }
        $str .= '</table>';
        return [$str, false];
    }

    protected static function get_html_signs(array $context):string {
        if (empty($context['signUploadGroup'])) {
            return '';
        }
        $str = '
            <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                <tr>
                    <td style="width: 30%; text-align: center"><strong>Характер работы</strong></td>
                    <td style="width: 20%; text-align: center"><strong>Фамилия</strong></td>
                    <td style="width: 30%; text-align: center"><strong>Подпись</strong></td>
                    <td style="width: 20%; text-align: center"><strong>Дата подписания</strong></td>
                </tr>
        ';
        foreach ($context['signUploadGroup'] as $sign) {
            $str .= '
                <tr>
                    <td style="width: 30%; text-align: center;"><div style="vertical-align: middle;"></div>'. ($sign['work'] ?? '') .'</td>
                    <td style="width: 20%; text-align: center;"><div style="vertical-align: middle;"></div>' . ($sign['family'] ?? '') . '</td>
                    <td style="width: 30%; text-align: center;"><img width="100" height="50" src="' . ($sign['base64'] ?? '') .'"></td>
                    <td style="width: 20%; text-align: center;"><div style="vertical-align: middle;"></div>' . ($sign['signDate'] ?? '') .'</td>
                </tr>
            ';
        }
        $str .= '</table>';
        return $str;
    }

    protected static function get_html_lists(array $context, \TCPDF $doc):string {
        $str = '
            <table cellspacing="0" cellpadding="1" border="1px" style="width: 100%;">
                <tr>
                    <td style="width: 40%; text-align: center" rowspan="2"><strong>Информационно-удостоверяющий лист</strong></td>
                    <td style="width: 40%; text-align: center" rowspan="2">'. $context['infoList'] .'</td>
                    <td style="width: 10%; text-align: center" colspan="1"><strong>Лист</strong></td>
                    <td style="width: 10%; text-align: center" colspan="1"><strong>Листов</strong></td>
                </tr>
                <tr>
                    <td style="text-align: center">'. $doc->getNumPages() .'</td>
                    <td style="text-align: center">'. $doc->getNumPages() .'</td>
                </tr>
            </table>
        ';
        return $str;
    }

    protected static function get_html_height(string $html):float {
        $doc = new \TCPDF();
        $doc->SetFont('freeserif', '', 12);
        $doc->setPrintHeader(false);
        $doc->setPrintFooter(false);
        $doc->AddPage();
        $doc->WriteHTML($html);
        return $doc->GetY();
    }

    public static function get_change_time(\Illuminate\Http\UploadedFile $file):false|string {
        $content = $file->getContent();
        preg_match_all('/<xmp:ModifyDate>(.*)<\/xmp:ModifyDate>/', $content, $matches1);
        preg_match_all('/\/ModDate\s?\(D:(.[^)]*)\)/', $content, $matches2);
        if (!empty($matches2[1])) {
            $times = [];
            foreach ($matches2[1] as $match) {
                $times[] = strtotime(preg_replace('/\+.*/', '', $match));
            }
            return date('d.m.Y H:i:s', max($times));
        } else if (!empty($matches1[1])) {
            $times = [];
            foreach ($matches1[1] as $match) {
                $times[] = strtotime(preg_replace('/\+.*/', '', $match));
            }
            return date('d.m.Y H:i:s', max($times));
        }
        return false;
    }

    public static function get_pdf_content(array $context):string {
        $doc = new \TCPDF();
        $doc->SetFont('freeserif', '', 12);
        $doc->setPrintHeader(false);
        $doc->setPrintFooter(false);
        $doc->AddPage();
        if (!empty($context['title'])) {
            $html = '<h4 style="text-align: center">' . $context['title'] . '</h4><br><br>';
            $doc->WriteHTML($html);
        }

        $html = self::get_html_names_doc($context);
        $html .= self::get_html_md5_doc($context);
        [$doc_html, $newstr] = self::get_html_fileinfo_doc($context, $doc);
        $html .= $doc_html;
        $doc->WriteHTML($html, false);

        $html2 = '';
        $html2 .= self::get_html_signs($context);
        $html2 .= self::get_html_lists($context, $doc);
        $remainingSpace = $doc->getPageHeight() - $doc->getMargins()['bottom'] - $doc->GetY();
        if ($remainingSpace < self::get_html_height($html2)) {
            $doc->AddPage();
        }
        $doc->WriteHTML($newstr, false);
        $doc->WriteHTML($html2, false);

        return $doc->Output('info.pdf', 'S');
    }
}
