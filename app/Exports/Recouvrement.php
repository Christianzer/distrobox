<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Recouvrement implements FromView,ShouldAutoSize
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


        $anneed = intval($this->annee);
        $moisd = intval($this->mois);


        $anneed = intval($anneed);
        $moisd = intval($moisd);

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

        $titre = " RAPPORT DETAILLE DES COMMISIONS DE RECOUVREMENT DES COMMERCIAUX PERIODE DE : $moisaff $anneed";
        $titre1 = "RECAPITULATIF DES COMMISSIONS ET PRIMES PERIODE DE : $moisaff $anneed  ";

        $sql = "select res_id, res_cession, res_comid , res_dat2,pai_date,pai_montant,pai_total,lot_new,lot_id, pai_date , pai_resid,pai_id";
        $sql .= " com_nom,cli_nom,cli_employeur,com_id,lot_designation, ilo_designation, ope_designation,ope_grand, lot_prixm2,lot_superficie,ope_acompte,ope_id";
        $sql .= " from reservation, commercial, lot, ilot, operation,client, paiement ";
        $sql .= " where res_del='A'  and pai_del ='A' and pai_date >='$datdeb'  and pai_id != '40268' and pai_id != '40436'";

        $sql .= " and res_dat2 < '2022-01-01' ";
        $sql .= " and pai_date<='$datfin' ";

        if ($comid != 0) :
            $titre = "RAPPORT DETAILLE DES COMMISIONS DE RECOUVREMENT DE $comnom PERIODE DE : $moisaff $anneed ";

        endif;

        $sql .= " and res_lotid=lot_id and res_comid=com_id and res_cliid=cli_id";
        $sql .= " and lot_iloid=ilo_id and ilo_opeid=ope_id ";
        $sql .= " and res_id = pai_resid" ;
        //$sql .= " and pai_date between '2015/03/01' and '2015/03/31' ";
        if ($comid != 0) {


            $com_aboa = "(11,20,22,23,24,25,26)";

            $com_fatou = "(2,16,1,21)";


            $com_papouet = "(3,10,19,27)";

            //ABOA YAN
            if ($comid == 22){

                $sql .= " and com_id in $com_aboa ";

                //PAPOUET
            }elseif ($comid == 19){

                $sql .= " and com_id in $com_papouet ";
                //FATOU
            }elseif ($comid == 16){
                $sql .= " and com_id in $com_fatou ";
            }else{
                $sql .= " and com_id=$comid ";
            }



            //PAPOUET PASCAL

            //FATOU


            //$sql .= " and com_id=$comid ";
        }
        //$sql .= " group by  pai_date";
        $sql .= " order by com_nom";

        //dd($sql);

        $respas = DB::select($sql);

        return view('export.recouvrement',
            ['titre'=>$titre,'respas'=>$respas,'titre1'=>$titre1]
        );
    }
}
