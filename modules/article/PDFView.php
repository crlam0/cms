<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace modules\article;

use classes\App;

if (file_exists(__DIR__ . '/dompdf/autoload.inc.php')) {
    include_once __DIR__ . '/dompdf/autoload.inc.php';
}
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Description of PDFView
 *
 * @author BooT
 */
class PDFView
{

    public function get(array $data, string $template, bool $stream = false): string
    {
        if(!class_exists('Dompdf\Dompdf')) {
            return App::$message->get('error', [], 'Не установлены компоненты для создания PDF');
        }
        $data['content'] = replace_base_href($data['content']);
        
        $tags = [
            'title' => $data['title'],
            'content' => App::$template->parse($template, $data),
        ];

        $content = App::$template->parse('pdf.html.twig', $tags);
        
        $options = new Options();
        $options->setRootDir(App::$DIR . 'modules/article/dompdf');
        $options->setDefaultFont('times');
        $options->isHtml5ParserEnabled(true);
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);        
        
        error_reporting(0);
        
        $content = str_replace('src="', 'src="' . App::$server['REQUEST_SCHEME'] . '://' . App::$server['HTTP_HOST'], $content);
        
        // echo $content;exit;

        $dompdf->loadHtml($content, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($stream) {
            $dompdf->stream($data['title'] . '.pdf');
            header('Content-Description: File Transfer');
            exit;
        } else {
            header('Content-Type: content/pdf');
            header('Content-Disposition: attachment; filename=' . $data['title'] . '.pdf');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            ob_end_flush();
            echo $dompdf->output();
            exit;
        }
    }
}
