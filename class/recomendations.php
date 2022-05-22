<?php

    namespace Recomendador;

    use Friendica\Core\L10n;
    use Friendica\Core\Logger;
    use Friendica\Database\DBA;
    use Friendica\Database\Database;

    function insert_feedback($id_perfil, $stars){
        try{
            DBA::insert('Feedback_Ranked_Recomendations', ['ID_origem_perfil'=>$id_perfil, 'Estrelas'=>$stars]);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function get_balance($id_perfil){
        try{
            $q = DBA::p("SELECT Estrelas FROM Feedback_Ranked_Recomendations WHERE ID_origem_perfil = ? ORDER BY ID DESC LIMIT 1", $id_perfil);
            if($r = DBA::fetch($q)){
                $smiles = $r['Estrelas'];
                //echo $smiles;
                if($smiles == 3){
                    return [0.3, 0.7];
                }else if($smiles == 2){
                    return [0.5, 0.5];
                }else if($smiles == 1){
                    return [0.7, 0.3];
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return [0.5, 0.5];
    }

    function get_total_dps(){
        try{
            return DBA::count('Disciplinas', []);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return 10;
    }

    function check_buble($id_perfil){ //checa se existe uma bolha para um usuario
        try{
            return DBA::exists('Bolha_recomendados', ['ID_origem_perfil'=>$id_perfil]);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return false;
    }

    function recomendados($id_perfil, $balance){ //Tipo origem = 1
        $dps = [];
        try{
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID IN (SELECT ID_disciplina FROM Link_Tag WHERE ID_Tag IN (SELECT ID_Tag FROM Bolha_recomendados WHERE ID_origem_perfil = ?) AND ID_disciplina NOT IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil = ?)) ORDER BY RAND() LIMIT ".$balance,$id_perfil, $id_perfil);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }

        if(!empty($dps)){
            marcar_disciplinas($id_perfil, $dps, 1);
        }
        //Logger::debug("values dps:".json_encode($dps));
        return $dps;
    }

    function recomendados_comunidade($id_perfil, $balance){
        $dps = [];
        try{
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID NOT IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil = ?) AND Avaliacao > 3.5 ORDER BY RAND() LIMIT ".$balance,$id_perfil);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }

        if(!empty($dps)){
            marcar_disciplinas($id_perfil, $dps, 3);
        }
        //Logger::debug("values dps:".json_encode($dps));
        return $dps;
    }
    
    function n_recomendados($id_perfil, $balance){ //Tipo origem = 2
        $dps = [];
        try{
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID IN (SELECT ID_disciplina FROM Link_Tag WHERE ID_Tag NOT IN (SELECT ID_Tag FROM Bolha_recomendados WHERE ID_origem_perfil = ?) AND ID NOT IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil = ?)) ORDER BY RAND() LIMIT ".$balance,$id_perfil, $id_perfil);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
            
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        if(!empty($dps)){
            marcar_disciplinas($id_perfil, $dps, 2);
        }
        
        Logger::debug("values dps:".json_encode($dps));
        return $dps;
    }

    function consulta_recomendados($id_user){
        $dps = [];
        try{
            $today = date('Y-m-d');
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil =? AND Tipo = ? AND DATE(Data) = ?) ORDER BY RAND()",$id_user,1,$today);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return $dps;
    }

    function consulta_comunidade_recomendados($id_user){
        $dps = [];
        try{
            $today = date('Y-m-d');
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil =? AND Tipo = ? AND DATE(Data) = ?) ORDER BY RAND()",$id_user,3,$today);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return $dps;
    }

    function consulta_n_recomendados($id_user){
        $dps = [];
        try{
            $today = date('Y-m-d');
            $q = DBA::p("SELECT * FROM Disciplinas WHERE Tipo = 1 AND ID IN (SELECT ID_disciplina FROM Recomendados WHERE ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?) ORDER BY RAND()",$id_user,2, $today);
            while($r = DBA::fetch($q)){
                array_push($dps, [
                    'ID' => $r['ID'],
                    'Nome' => $r['Nome']
                ]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return $dps;
    }
    
    function count_cicles($id_perfil){
        try{
            $q = DBA::select('Recomendados', [], ['ID_origem_perfil'=>$id_perfil]);
            while($r = DBA::fetch($q)){
                $ciclos = $r['Ciclos'];
                $id_dp = $r['ID_disciplina'];

                if($ciclos > 0 && $r['Tipo'] != 0){
                    $ciclos--;
                    DBA::update('Recomendados',['Ciclos'=>$ciclos],['ID_origem_perfil'=>$id_perfil]);
                }else{
                    if($r['Tipo'] != 0){
                        DBA::delete('Recomendados',['ID_disciplina'=>$id_dp,'ID_origem_perfil'=>$id_perfil]);
                    }
                    $r = DBA::selectFirst('Disciplinas', ['ID_Tag'], ['ID'=>$id_dp]);
                    if(DBA::isResult($r)){
                        DBA::delete('Bolha_recomendados', ['ID_origem_perfil'=>$id_perfil, 'ID_Tag'=>$r['ID_Tag']]);
                    }
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }
    
    function marcar_disciplinas($id_perfil, $dps, $tipo){
        $m = getCycle($id_perfil);
        $today = date('Y-m-d');

        try{
            foreach($dps as $dp){
                if(!DBA::exists('Recomendados',['ID_disciplina'=>$dp['ID'], 'ID_origem_perfil'=>$id_perfil])){
                    DBA::insert('Recomendados',['ID_disciplina'=>$dp['ID'],'ID_origem_perfil'=>$id_perfil,'Ciclos'=>$m, 'Tipo'=>$tipo, 'Data'=>$today]);
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function desmarcar_disciplinas($id_perfil, $dps){
        try{
            foreach($dps as $dp){
                DBA::delete('Recomendados',['ID_disciplina'=>$dp['ID'],'ID_origem_perfil'=>$id_perfil]);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function marcar_disciplina_indef($id_perfil, $dp_name){
        try{
            $r = DBA::selectFirst('Disciplinas', ['ID'], ['Nome'=>$dp_name]);
            if(DBA::isResult($r)){
                DBA::insert('Recomendados',['ID_disciplina'=>$r['ID'],'ID_origem_perfil'=>$id_perfil,'Ciclos'=>(-1), 'Tipo'=>0, 'Data'=>'0000-00-00'],Database::INSERT_IGNORE);
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function marcar_disciplina_indef_id($id_perfil, $id_dp){
        try{
            DBA::insert('Recomendados',['ID_disciplina'=>$id_dp,'ID_origem_perfil'=>$id_perfil,'Ciclos'=>(-1), 'Tipo'=>0, 'Data'=>'0000-00-00'],Database::INSERT_IGNORE);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function getCycle($id_perfil){
        try{
            $r = DBA::selectFirst('Badge',['Reputacao'],['ID_perfil'=>$id_perfil]);
            if(DBA::isResult($r)){
                $rep = $r['Reputacao'];
                $cycle = [];
                if($rep >= 0 && $rep < 0.4){
                    $cycle = [2, 7, 17];
                }else if($rep >= 0.4 && $rep < 0.8){
                    $cycle = [3, 11, 19];
                }else if($rep >= 0.8){
                    $cycle = [5, 13, 23];
                }
                $n = random_int(0,2);
                return $cycle[$n];
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return -1;
    }

    function today_recomended($id_perfil){
        try{
            $today = date('Y-m-d');
            return DBA::exists('Recomendados', ['Data'=>$today, 'ID_origem_perfil'=>$id_perfil]);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return false;
    }

    function get_recommendations($id_perfil){
        $recomended = [];
        $n_recomended = [];
        $comunidade = false;
        //$total_dps = get_total_dps();
        $total_dps = 10;

        if(today_recomended($id_perfil)){
            $recomended = consulta_recomendados($id_perfil);
            $n_recomended = consulta_n_recomendados($id_perfil);
        }else{
            count_cicles($id_perfil);
            $balance = get_balance($id_perfil);
            
            $recomended = recomendados($id_perfil, $balance[0]*$total_dps);
            $n_recomended = n_recomendados($id_perfil, $balance[1]*$total_dps);
        }
        $table = [
            'comunidade' => $comunidade,
            'Recomendados' => $recomended,
            'NRecomendados' => $n_recomended
        ];

        return $table;
    }

?>