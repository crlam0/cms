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

/**
 * Description of PDFView
 *
 * @author BooT
 */
class PDFView
{

    private function getHTML(array $row): string
    {
        $content = '<html><head><style>body { font-family: arial; }</style></head>'.
        '<body>';
        $content .= '<h1>' . $row['title'] . '</h1><br />';
        $content .= App::$template->parse('article_view', $row);
        $content .='</body>'.
        '</html>';
        return $content;
    }

    public function get(array $row, bool $stream = false): void
    {
        $row['content'] = replace_base_href($row['content']);
        $content = $this->getHTML($row);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($stream) {
            $dompdf->stream($row['title'] . '.pdf');
            header('Content-Description: File Transfer');
            exit;
        } else {
            header('Content-Type: content/pdf');
            header('Content-Disposition: attachment; filename=' . $row['title'] . '.pdf');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            ob_end_flush();
            echo $dompdf->output();
        }
    }
}
