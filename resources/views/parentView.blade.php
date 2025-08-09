
<html lang="fr">
<head>
    <style type="text/css">
        #menu { display: none;}
        #barretitre-div{ display: none;}
        div1
        {
            border-left: 2px solid black;
            border-right: 2px solid black;
        }
        body{  line-height:normal; font-family:arial;}

    </style>
</head>
<body>

@foreach($urls as $url)
    @php($typeImpression = $url['typeImpression'])
    @php($valeur = $url['valeur'])
    @php($type = $url['type'])
    <!doctype html>
        <?php
        $CIVILITES = explode(",","Choisissez,Monsieur,Madame,Mademoiselle,Entreprise,Couple,Association,Indivision,Autre");
        $TYPEPIECES = explode(",","Choisissez,Carte d'identité,Attestation d'identité,Passeport,Autre");
        $MOIS = explode(",","Choisissez,Janvier,Février,Mars,Avril,Mai,Juin,Juillet,Août,Septembre,Octobre,Novembre,Décembre");
        $MODEPAIEMENTS = "Chèque,Numéraire,Virement,Autre";
        $GROUPES = explode(",", "A,B,C,Z");
        ?>
    <div style="page-break-before: always">
        <div class="div" id="div"  width="70%"  >
            <div class="div1" id="div1"  style="border-color:#000" width="70%">
                <img src='{{asset('front/default/LOGOCIAT.png')}}' width="18%"  height="9%"  />

                @if($typeImpression)
                    <img src='{{asset('front/default/fichereser1.fw.png')}}' width="81%" />
                @else
                    <img src='{{asset('front/default/fichereser.fw.png')}}' width="81%" />
                @endif


                <span
                    style="color: #2BBDED; ">&nbsp;Am&eacute;nageur  Foncier Agr&eacute;&eacute; N&deg;12 00003/MCAU/DGUF</span><br>
                <span
                    style="color: #2BBDED; ">&nbsp;Promoteur  Immobilier Agr&eacute;&eacute; N&deg;60/MHLS/DGLCV/DL/SDH/KFT</span><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abidjan-Cocody-Mermoz, rue Booker Washington <br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;06 B.P 1044 Abidjan 06 C&ocirc;te d ivoire <br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;T&eacute;l:(225).27.22.40.09.20 / Fax : (225).27.22.44.16.63<br>
                <span style="color: #2BBDED; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site web&nbsp;:&nbsp;www.ciat.ci &nbsp; E-mail&nbsp;:&nbsp;contact@ciat.ci</span><br>

                <div class="Div2" id="Div2"  align="center" style="background:#CCCCCC; border:solid;" width="50%"   > <FONT size="3pt" face="Arial, Helvetica, sans-serif">
                        <b>Identification du r&eacute;servataire</b> </FONT>
                    <div class="Divind" id="Divind"  align="left" style="background:#FFFFFF;"  width="55%">
                        <FONT size="2pt"> <p>&nbsp;Num&eacute;ro Client: &nbsp;<b>{{$valeur[0]}}&nbsp;</b></BR>
                                &nbsp;Nom et pr&eacute;noms: &nbsp;<b>{{$CIVILITES[$valeur[1]]}}&nbsp;{{$valeur[2]}}</b></BR>
                                &nbsp;N&eacute;(e)&nbsp;le : <b>{{$valeur[3]}} </b>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; A : &nbsp;<b>{{$valeur[4]}}</b>   </BR>
                                &nbsp;Nationalit&eacute;:&nbsp;<b>{{$valeur[5]}}</b>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Type Pi&egrave;ce:&nbsp; <b>{{$valeur[6]}}</b> &nbsp;&nbsp; Pi&egrave;ce N&deg; :&nbsp;<b> {{$valeur[7]}} </b>  </BR>
                                &nbsp;Profession :&nbsp; <b>{{$valeur[8]}}</b>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Employeur : &nbsp; <b>{{$valeur[9]}}</b>   </BR>
                                &nbsp;Situation matrimoniale :&nbsp; <b>{{$valeur[10]}}</b></BR>
                                &nbsp;Adresse postale : &nbsp; <b>{{$valeur[11]}}</b> </BR>
                                &nbsp;Contacts : &nbsp;<b>{{$valeur[12]}}</b>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Email({{$valeur[13]}})&nbsp;:&nbsp; <b>{{$valeur[14]}}</b>    </BR>
                            </p>
                        </FONT>
                        </BR>

                    </div>
                </div>
                </BR>
                <div class="Div3" id="Div3"  align="center" style="background:#CCCCCC; border:solid;" width="55%" > <FONT size="3pt" face="Arial, Helvetica, sans-serif"> <b>Identification du terrain</b> </FONT>
                    <div class="Divind" id="Divind"  align="left" style="background:#FFFFFF;" width="55%">
                        <FONT size="2pt"> <p>&nbsp;Projet :&nbsp; <b>{{$valeur[15]}}</b></BR></BR>
                                &nbsp;Ilot :&nbsp;<b>{{$valeur[16]}}</b> &nbsp;Lot :&nbsp;<b>{{$valeur[17]}}</b> &nbsp;&nbsp;&nbsp;Superficie :&nbsp;<b>{{$valeur[18]}}</b>   <b>m&sup2;&nbsp;&nbsp;&nbsp;&nbsp;</b> Prix/   m&sup2; :&nbsp;<b>{{$valeur[19]}} FCFA</b></BR></BR>
                                &nbsp;Prix Total&nbsp;:&nbsp; <b>{{$valeur[20]}}</b>  <b>FCFA</b></BR></BR>
                                &nbsp;Type d'amenagement :&nbsp; <b style="text-transform: uppercase">{{$valeur[21]}}</b></BR></BR>
                                &nbsp;Conseiller Client&egrave;le :&nbsp; <b>{{$valeur[22]}}</b></BR>
                            </p>
                        </FONT>


                    </div>
                </div>
                </BR>
                <div class="Div4" id="Div4"  align="center" style="background:#CCCCCC; border:solid;"  width="55%" ><span
                        style="font-family: Arial, Helvetica, sans-serif; "> <b> Informations de paiement</b></span>
                    <div class="Divind" id="Divind"  align="left" style="background:#FFFFFF;"  width="55%">
                        <FONT size="2pt">
                            <p>&nbsp;Montant vers&eacute; :&nbsp; &nbsp;&nbsp;&nbsp; <b>{{$valeur[23]}} F CFA</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date de paiement:&nbsp; <b>{{$valeur[24]}}</b> </BR>
                                &nbsp;Mode de paiement:&nbsp; <b>{{$valeur[25]}}</b></BR>
                                &nbsp;Acompte de r&eacute;servation :&nbsp; &nbsp;&nbsp;&nbsp; <b>{{$valeur[26]}}%</b>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;Montant&nbsp;: <b>{{$valeur[27]}} F CFA</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </BR>
                                &nbsp;Dur&eacute;e de l'&eacute;ch&eacute;ance :&nbsp; <b>{{$valeur[28]}} &nbsp; MOIS</b></BR>
                                &nbsp;Montant de l'&eacute;ch&eacute;ancier :&nbsp;<b>{{$valeur[29]}} F CFA</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fin Ech&eacute;ancier:&nbsp; <b>{{$valeur[30]}}</b> </BR></p>
                        </FONT>
                        <span style="font-family: Arial, Helvetica, sans-serif; "><I>&nbsp;&nbsp;&nbsp;&nbsp;Le r&eacute;sevataire d&eacute;clare avoir visit&eacute; le terrain ci-dessus
                        et d&eacute;clare accepter les conditions et modalit&eacute;s de paiement</BR>
                    </I></span></BR>
                    </div>
                </div>
            </div>

            <table width="90%"  border="0">
                <tr>
            <span style="font-family: Arial, Helvetica, sans-serif; "> <th width="33%" ><p><b> R&eacute;servataire</b> </BR>
                        <i>(lu et approuv&eacute;)</i></BR>
                        Date et signature</th>
                <th width="33%" ><p><b> Conseiller Client&egrave;le</b> </BR>Date et signature</p></th>
                <th width="3%" ><p>&nbsp;</p></th>
                <th width="31%" ><p><b> Directeur Commercial</b></BR>Date et signature</p></th>
            </span>
                </tr>
            </table>









            <footer style=" position: fixed;
   left: 0;
   bottom: 0;"><FONT size="0.5pt" face="Arial, Helvetica, sans-serif"><I>1. les frais de dossier ne sont pas remboursables</BR>
                        2. l'acompte de r&eacute;servation ({{$valeur[31]}}%) valide la r&eacute;servation</BR>
                        3. Apr&egrave;s deux &eacute;ch&eacute;ances successives non respect&eacute;es, la reservation est annul&eacute;e . Les sommes vers&eacute;es sont restitu&eacute;es
                        au r&eacute;servataire, d&eacute;duction de 20% faite au titre des p&eacute;nalit&eacute;s. Cette disposition concerne toute forme d'annulation ou
                        de r&eacute;siliation de la r&eacute;servation</I></FONT></BR> </footer>

        </div>
    </div>


@endforeach

</body>
</html>

