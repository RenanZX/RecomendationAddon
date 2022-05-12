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
        $v = ($M1 + $M2 + $M3 + $M4) / 4;
        Logger::debug('Chamando a funcao de calculo de reputacao');
        update_reputation($id_user, $v);
    }

    function update_reputation($id_user, $rep){
      try{
        $r = DBA::selectFirst('Badge', [], ['ID_perfil'=>$id_user]);
        if(DBA::isResult($r)){
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
          if($res >= 0 && $res < 0.3){
            return 'LBronze';
          }else if($res >= 0.3 && $res < 0.7){
            return 'LPrata';
          }else{
            return 'LOuro';
          }
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return 'LBronze';
    }

    function compute_M1($uid, $rep){ //quantidade de mini badges recebidas pelo aluno
        $peso = 0;
        $cb = count_badges($uid);
        if($cb > 12){
            $peso += 0.1;
        }
        if($cb > 20){
            $peso += 0.3;
        }
        if($cb > 25){
            $peso += 0.4;
        }
        if($cb > 35){
            $peso = 1;
        }
        return $peso;
    }

    function compute_M2($uid, $rep){ //quantidade de likes e deslikes dados aos comentarios de outros alunos
      $w = 0;
      try{
        $like = DBA::count('Feedback_Comment_PF', ['ID_origem_perfil'=>$uid, 'Tipo'=>1]);
        $deslike = DBA::count('Feedback_Comment_PF', ['ID_origem_perfil'=>$uid, 'Tipo'=>0]);
        $total = $like + $deslike;
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
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return $w;
    }

    function compute_M3($uid, $rep){ //quantidade de comentarios realizados no dia e recebidos
      $w = 0;
      try{
        $today = date('Y-m-d');
        $cr = DBA::count('Comment_PF', ['ID_origem_perfil = ? AND DATE(Data) = ?',$uid,$today]) + DBA::count('Comment_PF', ['ID_destino'=>$uid]);
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
  
    function update_badge($badge, $id_coment){
      try {
          $q = DBA::update('Comment_PF', array('Badge'=>$badge), array('ID'=>$id_coment));
      } catch(Exception $e){
          Logger::debug($e->getMessage());
      }
    }

    function count_badges($uid){
        try{
            $count = 0;

            $q = DBA::select('Comment_PF', [], array("`Badge` = ? OR `Badge` = ? OR `Badge` = ? AND `ID_origem_perfil` = ?",BRONZE, PRATA, OURO, $uid));
            while($r = DBA::fetch($q)){
                $count+=$r['Badge'];
            }
            return $count;
        } catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

?>