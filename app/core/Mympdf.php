<?php

/**
* Mympdf Class
*
* Extends mPDF to create and manipulate pdf files
*
* @author     Mark Solly <mark.solly@3plplus.com.au>
*/
use Mpdf\Mpdf;
 use Symfony\Component\Filesystem\Filesystem,
    Xthiago\PDFVersionConverter\Converter\GhostscriptConverterCommand,
    Xthiago\PDFVersionConverter\Converter\GhostscriptConverter;
class Mympdf extends mPDF {

    function mergePDFFiles(Array $filenames, $outFile)
    {
        if ($filenames)
        {
            $filesTotal = sizeof($filenames);
            //$fileNumber = 1;
            //$this->SetImportUse();
            if (!file_exists($outFile))
            {
                $handle = fopen($outFile, 'w');
                fclose($handle);
            }
            foreach ($filenames as $array)
            {
                if (file_exists($array['file']))
                {
                    //$this->AddPage();

                    //shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=new-file.pdf ".$array['file']);

                    $this->AddPageByArray(array(
    					'orientation' => $array['orientation']
                    ));
                    $pagesInFile = $this->SetSourceFile($array['file']);
                    //die('pages: '.$pagesInFile);
                    for ($i = 1; $i <= $pagesInFile; $i++)
                    {
                        $tplId = $this->ImportPage($i);
                        $this->UseTemplate($tplId);
                        if (($i <  $pagesInFile))
                        {
                            //$this->WriteHTML('<pagebreak />');
                            $this->AddPageByArray(array(
            					'orientation' => $array['orientation']
                            ));
                        }
                    }

                }
                //$fileNumber++;
            }
            //$page_count = $this->page;
            $this->Output();
        }
    }

    function mergePDFFilesToServer(Array $filenames, $outFile, $path = "")
    {
        $c = 1;
        if ($filenames)
        {
            $command = new GhostscriptConverterCommand();
            $filesystem = new Filesystem();
            $filesTotal = sizeof($filenames);
            //$fileNumber = 1;
            //$this->SetImportUse();
            if (!file_exists($path."/".$outFile))
            {
                $handle = fopen($path."/".$outFile, 'w');
                fclose($handle);
            }
            foreach ($filenames as $array)
            {
                if (file_exists($array['file']))
                {
                    /*$this->AddPage();*/
                    $new_file = "new_file_".$c.".pdf";
                    //shell_exec( "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$new_file." ".$array['file']);
                    //new method because OzHosting don't know how to do the above
                    $converter = new GhostscriptConverter($command, $filesystem);
                    $converter->convert($array['file'], '1.4');
                    $this->AddPageByArray(array(
    					'orientation' => $array['orientation']
                    ));
                    //$pagesInFile = $this->SetSourceFile($new_file);
                    $pagesInFile = $this->SetSourceFile($array['file']);
                    for ($i = 1; $i <= $pagesInFile; $i++)
                    {
                        $tplId = $this->ImportPage($i);
                        $this->UseTemplate($tplId);
                        if (($i <  $pagesInFile))
                        {
                            //$this->WriteHTML('<pagebreak />');
                            $this->AddPageByArray(array(
            					'orientation' => $array['orientation']
                            ));
                        }
                    }
                    ++$c;
                }
                //$fileNumber++;
            }
            $this->Output($path."/".$outFile, "F");
        }
    }

}
?>