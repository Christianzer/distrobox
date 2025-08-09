<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Desistements implements FromView,ShouldAutoSize
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

        $sql = " select * from desistement where statut_desist=2 ";
        $sql .= " and datecrea_desist>='$datdeb' ";

        if ($type === 1):
            $sql .= " and datecrea_desist<='$datfin' ";
            $titre = "RAPPORT DETAILLE DES DESISTEMENTS DES CLIENTS DE $comnom PERIODE DE : $moisaff $anneed ";

        endif;

        if ($comid != 0) {
            $sql .= " and id_desist=$comid ";
        }
        //	$sql .= " and commercial='$comnom' ";
        $sql .= " group by id_desist";
        $sql .= " order by commercial";

        $respas = DB::select($sql);

        return view('export.desistement', [
            'respas' => $respas,
            'titre'=>$titre
        ]);
    }
}
