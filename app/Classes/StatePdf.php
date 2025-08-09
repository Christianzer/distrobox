<?php

namespace App\Classes;
use TCPDF;

class StatePdf extends TCPDF
{
    /**
     * @var
     */

   private $typePoints;
   private $typeDocuments;
   private $titreDocument;



    public function Header(){

    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $pagingtext = "Page N ° " . $this->PageNo() . "/" . $this->getAliasNbPages();

        if ($this->getTypeDocuments() === 1) {
            $codeCt = "<span style='color: #903;font-weight: bolder !important;font-style: italic !important;'></span>";
            $this->WriteHTMLCell(180, 4, 4, 280, $codeCt, 0);
            $this->WriteHTMLCell(50, 10, 190, 290, $pagingtext, 0);
        }

        else{
            $codeCt = "<span style='color: #903 !important;font-weight: bolder !important;font-style: italic !important;'></span>";
            $this->WriteHTMLCell(180,8,6,290,$codeCt,0);
            $this->WriteHTMLCell(50, 10, 190, 290, $pagingtext, 0);
        }

    }




    /**
     * @return mixed
     */
    public function getTypePoints()
    {
        return $this->typePoints;
    }

    /**
     * @param mixed $typePoints
     */
    public function setTypePoints($typePoints): void
    {
        $this->typePoints = $typePoints;
    }

    /**
     * @return mixed
     */
    public function getTypeDocuments()
    {
        return $this->typeDocuments;
    }

    /**
     * @param mixed $typeDocuments
     */
    public function setTypeDocuments($typeDocuments): void
    {
        $this->typeDocuments = $typeDocuments;
    }

    /**
     * @return mixed
     */
    public function getTitreDocument()
    {
        return $this->titreDocument;
    }

    /**
     * @param mixed $titreDocument
     */
    public function setTitreDocument($titreDocument): void
    {
        $this->titreDocument = $titreDocument;
    }



}
