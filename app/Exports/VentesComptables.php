<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VentesComptables implements FromView,ShouldAutoSize
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

        $sql = "select res_id, res_cession,  res_dat2, ";
        $sql .= " com_nom,cli_nom,cli_employeur,lot_designation, ilo_designation, ope_designation,ope_grand, lot_prixm2,lot_superficie,ope_acompte";
        $sql .= " from reservation, commercial, lot, ilot, operation,client ";
        $sql .= " where res_del='A' and res_dat2>='$datdeb' ";
        $sql2 = " select * from desistement where statut_desist=2 ";
        $sql2 .= " and datecrea_desist>='$datdeb' ";

        if ($type === 1):
            $sql .= " and res_dat2<='$datfin' ";
            $titre = "RAPPORT DETAILLE DES COMMISIONS DE VENTE DE $comnom PERIODE DE : $moisaff $anneed ";
            $sql2 .= " and datecrea_desist<='$datfin' ";
            $titre2 = "RAPPORT DETAILLE DES DESISTEMENTS DES CLIENTS DE $comnom PERIODE DE : $moisaff $anneed ";

        endif;

        $sql .= " and res_lotid=lot_id and res_comid=com_id and res_cliid=cli_id";
        $sql .= " and lot_iloid=ilo_id and ilo_opeid=ope_id ";
        //$sql .= " and pai_date between '2015/03/01' and '2015/03/31' ";
        if ($comid != 0) {
            $sql .= " and com_id=$comid ";
        }
        $sql .= " group by res_id";
        $sql .= " order by com_nom";

        $respas = DB::select($sql);

        $sql2 .= " order by commercial";
        //$sql2 .= " order by commercial";
        //echo $sql;
        $respas2 = DB::select($sql2);

        return view('export.ventes_compt',
            ['titre'=>$titre,'respas'=>$respas,'respas2'=>$respas2,'titre2'=>$titre2,'titre1'=>$titre1]
        );
    }
}
