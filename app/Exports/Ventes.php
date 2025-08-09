<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Ventes implements FromView,ShouldAutoSize
{
    use Exportable;

    protected $mois;
    protected $annee;
    protected $com;

    public function __construct($mois,$annee,$com)
    {
        $this->mois = $mois;
        $this->annee = $annee;
        $this->com = $com;
    }

    public function view() : View
    {
        $commercial = explode (";", $this->com);
        $comid = $commercial[0];
        $comnom = $commercial[1];

        $lesmois = "janvier;février;mars;avril;mai;juin;juillet;aout;septembre;octobre;novembre;décembre";

        $type = intval(1);
        $anneed = intval($this->annee);
        $moisd = intval($this->mois);


        switch ((int)$moisd) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                $finmois = 31;
                break;
            case 2:
                if (checkdate($moisd, 29, $anneed)) {
                    $finmois = 29;
                } else {
                    $finmois = 28;
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                $finmois = 30;
                break;
            default:
                $finmois = 31;
        }


        $datdeb = $anneed . "-" . $moisd . "-01";
        $datfin = $anneed . "-" . $moisd . "-" . $finmois;

        $mois = explode(";", $lesmois);
        $moisaff = $mois[$moisd - 1];

        $titre = " RAPPORT DETAILLE DES COMMISIONS DE VENTE DES COMMERCIAUX PERIODE DE : $moisaff $anneed";
        $titre1 = "RECAPITULATIF DES COMMISSIONS ET PRIMES DE LA DIRECTION COMMERCIALE PERIODE DE : $moisaff $anneed  ";
        $titre2 = "RECAPITULATIF DES VENTES PAR OPERATION PERIODE DE : $moisaff $anneed  ";
        $sql = "select res_id,cli_id, res_cession,  res_dat2,pai_date,pai_montant,pai_total,lot_new,lot_id, pai_date , pai_resid,";
        $sql .= " com_nom,cli_nom,cli_employeur,lot_designation, ilo_designation, ope_designation,ope_grand, lot_prixm2,lot_superficie,ope_acompte";
        $sql .= " from reservation, commercial, lot, ilot, operation,client, paiement ";
        $sql .= " where res_del='A'  and pai_del ='A' and pai_date >='$datdeb'  and pai_id != '40268' and pai_id != '40436'";
        $sql .= " and (lot_etat='1' OR  res_dat2>='2022-01-01')";
        $sql2 = " select * from desistement where statut_desist=2 ";
        $sql2 .= " and datecrea_desist>='2024-01-01' ";
        $sql .= " and pai_date<='$datfin' ";
        $sql2 .= " and datecrea_desist<='2024-07-05' ";

        if ($comid != 0):

            $titre = "RAPPORT DETAILLE DES COMMISIONS DE VENTE DE $comnom PERIODE DE : $moisaff $anneed ";

            $titre2 = "RAPPORT DETAILLE DES DESISTEMENTS DES CLIENTS DE $comnom PERIODE DE : $moisaff $anneed ";
        endif;

        $sql .= " and res_lotid=lot_id and res_comid=com_id and res_cliid=cli_id";
        $sql .= " and lot_iloid=ilo_id and ilo_opeid=ope_id ";
        $sql .= " and res_id = pai_resid" ;
        //$sql .= " and pai_date between '2015/03/01' and '2015/03/31' ";
        if ($comid != 0) {
            $sql .= " and com_id=$comid ";
        }
        //$sql .= " group by  pai_date";
        $sql .= " order by com_nom";

        $respas = DB::select($sql);

        if ($comid != 0) {
            $sql2 .= " and commercial= '$comnom' ";
        }

        //$sql2 .= " order by commercial";
        //echo $sql;
        $respas2 = DB::select($sql2);
        return view('export.ventes',
            ['titre'=>$titre,'respas'=>$respas,'respas2'=>$respas2,'titre2'=>$titre2,'titre1'=>$titre1]
        );
    }
}
