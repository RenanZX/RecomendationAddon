<?php
    namespace Badge;

    use Friendica\Core\Logger;
    use Friendica\Database\DBA;
    use Friendica\Database\Database;

    define('BRONZE',1);
    define('PRATA', 2);
    define('OURO', 3);

    function compute_reputation_user($id_user){
        $r = get_reputation_user($id_user);
        $M1 = compute_M1($id_user, $r);
        $M2 = compute_M2($id_user, $r);
        $M3 = compute_M3($id_user, $r);
        $M4 = compute_M4($id_user, $r);
        Logger::debug('M1='.$M1.',M2='.$M2.',M3='.$M3.'M4='.$M4);
        $v = ($M1 + $M2 + $M3 + $M4) / 4;
        Logger::debug('Calculo de reputacao id:'.$id_user);
        update_reputation($id_user, $v);
    }

    function update_reputation($id_user, $rep){
      try{
        if(DBA::exists('Badge', ['ID_perfil'=>$id_user])){
          DBA::update('Badge', ['Reputacao'=>$rep], ['ID_perfil'=>$id_user]);
        }else{
          DBA::insert('Badge', ['ID_perfil'=>$id_user, 'Reputacao'=>$rep]);
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
    }

    function get_reputation_user($id_user){
      try{
        $r = DBA::selectFirst('Badge', ['Reputacao'], ['ID_perfil'=>$id_user]);
        if(DBA::isResult($r)){
          $res = $r['Reputacao'];
          $res = $res * 100;
          if($res > 0 && $res < 30){
            return 'LBronze';
          }else if($res >= 30 && $res < 70){
            return 'LPrata';
          }else if($res >= 70){
            return 'LOuro';
          }
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return -1;
    }

    function compute_meta($id_user, $likes_t, $deslikes_t){
      try{
        //Compara o desempenho de likes/deslikes atingido do dia anterior com o dia atual
        $yesterday = date("Y-m-d", strtotime("yesterday"));
        $r = DBA::selectFirst('DesempenhoDiario', ['Likes', 'Deslikes'], ['ID_perfil = ? AND DATE(Data) = ?',$id_user, $yesterday]);
        if(DBA::isResult($r)){
          $likes_y = $r['Likes'];
          $deslikes_y = $r['Deslikes'];

          if(($likes_y > $deslikes_y)&&($deslike > 0)){ //se tiveram mais likes no dia anterior a meta é que ele faça pelo menos 1 deslike no dia atual
            return 0.1;
          }else if(($deslikes_y > $likes_y)&&($like > 0)){ //se tiveram mais deslikes no dia anterior a meta é que ele faça pelo menos 1 like no dia atual
            return 0.1;
          }else if(($deslikes_y == $likes_y)&&($likes_t != $deslikes_t)){ //se iguais os likes e deslikes a meta é que ele de mais likes ou deslikes do dia atual
            return 0.2;
          }else{ //se a meta nao for comprida o nível do aluno regride um pouco
            return -0.1;
          }
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return 0;
    }

    function update_desempenho($uid, $like, $deslike){
      try{
        $today = date('Y-m-d');
        if(DBA::exists('DesempenhoDiario', ['ID_perfil = ? AND DATE(Data) = ?',$uid, $today])){
          DBA::update('DesempenhoDiario', ['Likes'=>$like, 'Deslikes'=>$deslike],['ID_perfil = ? AND DATE(Data) = ?',$uid, $today]);
        }else{
          $itoday = date('Y-m-d H:i:s');
          DBA::insert('DesempenhoDiario', ['ID_perfil'=>$uid,'Likes'=>$like, 'Deslikes'=>$deslike, 'Data'=>$itoday]);
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
    }

    function compute_M1($uid, $rep){ //quantidade de mini badges recebidas pelo aluno
        $peso = 0;
        $cb = count_badges($uid);
        if($cb >= 1){
            $peso += 0.1;
        }
        if($cb > 5){
            $peso += 0.3;
        }
        if($cb > 10){
            $peso += 0.4;
        }
        if($cb > 40){
            $peso = 1;
        }
        return $peso;
    }

    function compute_M2($uid, $rep){ //quantidade de likes e deslikes dados aos comentarios de outros alunos
      $w = 0;
      try{
        $today = date('Y-m-d');
        
        $like = DBA::count('Feedback_Comment_DP', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,1,$today]) + DBA::count('Feedback_Comment_PF', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,1,$today]); //contando likes
        $deslike = DBA::count('Feedback_Comment_DP', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,0,$today]) + DBA::count('Feedback_Comment_PF', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,0,$today]); //contando deslikes
        $total = $like + $deslike;
        if(($like > 0 && $deslike == 0) || ($deslike > 0 && $like == 0)){
          $w-=0.1;
        }
        if($total > 0){
          $p_deslike = $deslike/$total;
          $p_like = $like/$total;
          if($p_deslike == $p_like){
            $w += 0.4;
          }
          if($p_deslike != $p_like){
            if(count_badges($uid) < 20){
              if($w >= 0.2){
                $w-=0.2;
              }
            }
          }
          if($p_like > $p_deslike){
            $w += 0.3;
          }
        }
        update_desempenho($uid,$like,$deslike);
        $w += compute_meta($uid, $like, $deslike); //adiciona no peso caso a meta tenha sido comprida
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return $w;
    }

    function compute_M3($uid, $rep){ //quantidade de comentarios realizados no dia e recebidos
      $w = 0;
      try{
        $today = date('Y-m-d');
        $cr = DBA::count('Comment_PF', ['ID_origem_perfil = ? OR ID_destino = ? AND DATE(Data) = ?',$uid, $uid,$today]);
        if($cr > 2){
          $w += 0.1;
        }
        if($cr > 5){ //contabilizar o fator tempo de quando o usuário comentou
          $w += 0.2;
        }
        if($cr > 10){
          $w += 0.4;
        }
        if($cr > 20){
          $w += 0.2;
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return $w;
    }

    function compute_M4($uid, $rep){ //quantidade de feedbacks dados ao recomendador
      $w = 0; //Checar tempo de registro também
      try{
        $positivo = DBA::count('Feedback_Ranked_Recomendations', ['ID_origem_perfil = ? AND Estrelas > 1',$uid]);
        $negativo = DBA::count('Feedback_Ranked_Recomendations', ['ID_origem_perfil = ? AND Estrelas < 1',$uid]);
        $equilibrado = DBA::count('Feedback_Ranked_Recomendations', ['ID_origem_perfil = ? AND Estrelas = 1',$uid]);
        $total = $positivo + $negativo + $equilibrado;
        
        if($total > 20){
          $w+= 0.2;
        }
        if($total > 40){
          $w += 0.4;
        }
        if($total > 50){
          $w = 1;
        }

        if($negativo > $positivo && $negativo > $equilibrado){
          if($rep == 'LOuro' && $w >= 0.5){
            $w -= 0.5;
          }else if($rep == 'LPrata' && $w >= 0.3){
            $w -= 0.3;
          }else if($rep == 'LBronze' && $w >= 0.2){
            $w -= 0.2;
          }
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return $w;
    }
  
    function compute_badge_coment($likes, $deslikes, $id_coment){
      if($likes > $deslikes){ //Se o comentario estiver com mais likes do que deslikes o comentario recebe uma badge
        $rq = $likes;
        if($deslikes > 0){ //evita divisao por zero
          $rq = $likes/$deslikes; //A badge é calculada em uma razão entre likes e deslikes
        }  
        if($rq > 0 && $rq < 10){ //De acordo com a razão calculada o usuário será premiado ou não;
          update_badge(BRONZE, $id_coment);
          return 'Bronze_Medal';
        }else if($rq >= 10 && $rq < 20){
          update_badge(PRATA, $id_coment);
          return 'Silver_Medal';
        }else if($rq >= 20){
          update_badge(OURO, $id_coment);
          return 'Gold_Medal';
        }
      }else{
        update_badge(0, $id_coment);
        return '';
      }
    }

    function compute_badge_coment_dp($likes, $deslikes, $id_coment){
      if($likes > $deslikes){ //Se o comentario estiver com mais likes do que deslikes o comentario recebe uma badge
        $rq = $likes;
        if($deslikes > 0){ //evita divisao por zero
          $rq = $likes/$deslikes; //A badge é calculada em uma razão entre likes e deslikes
        }  
        if($rq > 0 && $rq < 10){ //De acordo com a razão calculada o usuário será premiado ou não;
          update_badge_dp(BRONZE, $id_coment);
          return 'Bronze_Medal';
        }else if($rq >= 10 && $rq < 20){
          update_badge_dp(PRATA, $id_coment);
          return 'Silver_Medal';
        }else if($rq >= 20){
          update_badge_dp(OURO, $id_coment);
          return 'Gold_Medal';
        }
      }else{
        update_badge_dp(0, $id_coment);
        return '';
      }
    }
  
    function update_badge($badge, $id_coment){
      try {
          $q = DBA::update('Comment_PF', array('Badge'=>$badge), array('ID'=>$id_coment));
      } catch(Exception $e){
          Logger::debug($e->getMessage());
      }
    }

    function update_badge_dp($badge, $id_coment){
      try {
          $q = DBA::update('Comment_DP', array('Badge'=>$badge), array('ID'=>$id_coment));
      } catch(Exception $e){
          Logger::debug($e->getMessage());
      }
    }

    function count_badges($uid){
        try{
            $count = 0;

            $q = DBA::select('Comment_PF', [], array("`Badge` >= ? AND `ID_origem_perfil` = ?",BRONZE, $uid));
            while($r = DBA::fetch($q)){
                $count+=$r['Badge'];
            }
            $q = DBA::select('Comment_DP', [], array("`Badge` >= ? AND `ID_origem_perfil` = ?",BRONZE, $uid));
            while($r = DBA::fetch($q)){
                $count+=$r['Badge'];
            } 
            return $count;
        } catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

?>