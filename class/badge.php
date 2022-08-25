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
        Logger::debug('M1='.$M1.',M2='.$M2.',M3='.$M3);
        $v = ($M1 + $M2 + $M3) / 3;
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
          }else if($res >= 30 && $res < 80){
            return 'LPrata';
          }else if($res >= 80){
            return 'LOuro';
          }
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return -1;
    }

    function compute_M1($uid, $rep){ //quantidade de mini badges recebidas pelo aluno
        $cb = count_badges($uid);
        if($cb >= 50){
          return 1;
        }
        if($cb >= 40){
          return 0.5;
        }
        if($cb >= 30){
          return 0.4;
        }
        if($cb >= 20){
          return 0.3;
        }
        if($cb >= 10){
          return 0.2;
        }
        if($cb >= 1){
          return 0.1;
        }
    }

    function compute_M2($uid, $rep){ //quantidade de likes e deslikes dados aos comentarios de outros alunos
      try{
        $today = date('Y-m-d');
        
        $like = DBA::count('Feedback_Comment_DP', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,1,$today]) + DBA::count('Feedback_Comment_PF', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,1,$today]); //contando likes
        $deslike = DBA::count('Feedback_Comment_DP', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,0,$today]) + DBA::count('Feedback_Comment_PF', ['ID_origem_perfil = ? AND Tipo = ? AND DATE(Data) = ?',$uid,0,$today]); //contando deslikes
        $total = $like + $deslike;
        if($total >= 100){
          return 1;
        }
        if($total >= 10){
          return 0.5;
        }
        if($total >= 1){
          return 0.1;
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return 0;
    }

    function compute_M3($uid, $rep){ //quantidade de comentarios realizados no dia e recebidos
      try{
        $today = date('Y-m-d');
        $cr = DBA::count('Comment_PF', ['ID_origem_perfil = ? OR ID_destino = ? AND DATE(Data) = ?',$uid, $uid,$today]);
        if($cr >= 20){
          return 1;
        }
        if($cr >= 10){
          return 0.4;
        }
        if($cr >= 5){
          return 0.3;
        }
        if($cr >= 2){ //contabilizar o fator tempo de quando o usuário comentou
          return 0.1;
        }
      }catch(Exception $e){
        Logger::debug($e->getMessage());
      }
      return 0;
    }
  
    function compute_badge_coment($likes, $deslikes, $id_coment){
      if($likes > $deslikes){ //Se o comentario estiver com mais likes do que deslikes o comentario recebe uma badge
        $rq = $likes - $deslikes; //A badge é calculada em uma diferença entre likes e deslikes
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
        $rq = $likes - $deslikes; //A badge é calculada em uma diferença entre likes e deslikes
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