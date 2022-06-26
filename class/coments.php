<?php

namespace Comentarios;

require_once('badge.php');
require_once('disciplina.php');
require_once('recomendations.php');
use Friendica\DI;

use Friendica\Core\L10n;
use Friendica\Core\Logger;
use Friendica\Database\DBA;

use Disciplina;
use Badge;
use Recomendador;

use Friendica\Model\Profile;
use Friendica\Core\Renderer;

function get_likes($id){
    try{
      $count = DBA::count('Feedback_Comment_PF', ['ID_coment'=>$id, 'Tipo'=>1]);
      return $count;
    }catch(Exception $e){
      return 0;
    }
  }

  function get_likes_dp($id){
    try{
      $count = DBA::count('Feedback_Comment_DP', ['ID_coment'=>$id, 'Tipo'=>1]);
      return $count;
    }catch(Exception $e){
      return 0;
    }
  }

  function get_deslikes($id){
    try{
      $count = DBA::count('Feedback_Comment_PF', ['ID_coment'=>$id, 'Tipo'=>0]);
      return $count;
    }catch(Exception $e){
      return 0;
    }
  }

  function get_deslikes_dp($id){
    try{
      $count = DBA::count('Feedback_Comment_DP', ['ID_coment'=>$id, 'Tipo'=>0]);
      return $count;
    }catch(Exception $e){
      return 0;
    }
  }

  function compute_association_coment($id){
    $dps = [];
    try{
      $q = DBA::p("SELECT t1.Nome FROM Disciplinas as t1, Bolha_recomendados as t2, Link_Tag as t3 WHERE t2.ID_origem_perfil = ? AND t2.ID_Tag = t3.ID_Tag AND t3.ID_disciplina = t1.ID  ORDER BY RAND() LIMIT 10", $id);
      
      while($r = DBA::fetch($q)){
        //Logger::debug('QUERY RESULT?:'.json_encode($r));
        if(!in_array($r['Nome'], $dps)){
          array_push($dps,$r['Nome']);
        }
      }

      if(empty($dps)){
        $q = DBA::p('SELECT * FROM Disciplinas ORDER BY RAND()');
        while($r = DBA::fetch($q)){
          if(!in_array($r['Nome'], $dps)){
            array_push($dps, $r['Nome']);
          }
        }
      }else{
        $q = DBA::p("SELECT t1.Nome FROM Disciplinas as t1, Bolha_recomendados as t2, Link_Tag as t3 WHERE t2.ID_origem_perfil = ? AND t2.ID_Tag = t3.ID_Tag AND t3.ID_disciplina != t1.ID  ORDER BY RAND()", $id);
      
        while($r = DBA::fetch($q)){
          //Logger::debug('QUERY RESULT?:'.json_encode($r));
          if(!in_array($r['Nome'], $dps)){
            array_push($dps,$r['Nome']);
          }
        } 
      }
      return $dps;
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
    return $dps;
  }

  function delete_coment($id_coment){
    try{
      DBA::delete('Comment_PF', ['ID'=>$id_coment]);
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
  }

  function get_comentarios($id){
    try {
      $q = DBA::select('Comment_PF', [], array('ID_destino'=>$id),[]);
      $perfil_coments = [];
      
      while($r = DBA::fetch($q)){
        $profile = Profile::getByUID($r['ID_origem_perfil']);
        $likes = get_likes($r['ID']);
        $deslikes = get_deslikes($r['ID']);
        $badge = Badge\compute_badge_coment($likes,$deslikes, $r['ID']);
        if($badge != ''){
          $badge = DI::baseUrl()->get().'/addon/recomendapp/assets/'.$badge.'.png';
        }
        
        $perfil_coments[] = [
          'id' => $r['ID'],
          'id_perfil' => $r['ID_origem_perfil'],
          'photo' => $profile['photo'],
          'name' => $profile['name'],
          'comment' => $r['Comentario'],
          'likes' => $likes,
          'deslikes' => $deslikes,
          'badge' => $badge,
        ];
      }

      return $perfil_coments;
    } catch(Exception $e){
      Logger::debug($e->getMessage());
    }
    return [];
  }

  function get_comentarios_dp($id){
    try{
      $q = DBA::select('Comment_DP', [], array('ID_disciplina'=>$id),[]);
      $perfil_coments = [];
      
      while($r = DBA::fetch($q)){
        $profile = Profile::getByUID($r['ID_origem_perfil']);
        $likes = get_likes_dp($r['ID']);
        $deslikes = get_deslikes_dp($r['ID']);
        $badge = Badge\compute_badge_coment_dp($likes,$deslikes, $r['ID']);
        if($badge != ''){
          $badge = DI::baseUrl()->get().'/addon/recomendapp/assets/'.$badge.'.png';
        }
        
        $perfil_coments[] = [
          'id' => $r['ID'],
          'photo' => $profile['photo'],
          'name' => $profile['name'],
          'comment' => $r['Comentario'],
          'stars' => $r['Estrelas'],
          'likes' => $likes,
          'deslikes' => $deslikes,
          'badge' => $badge
        ];
      }

      return $perfil_coments;
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
    return [];
  }

  function update_like($id_coment, $id_user, $type){
    try{
      $today = date('Y-m-d H:i:s');
      $consulta = ($type == '1'? 'Feedback_Comment_PF' : 'Feedback_Comment_DP');
      $q = DBA::select($consulta, [], ['ID_origem_perfil'=>$id_user, 'ID_coment'=>$id_coment], []);
      if($r = DBA::fetch($q)){
        DBA::delete($consulta, ['ID_origem_perfil'=>$id_user,'ID_coment'=>$id_coment, 'Tipo'=>1]);
      }else{
        DBA::insert($consulta, ['ID_origem_perfil'=>$id_user, 'ID_coment'=>$id_coment, 'Tipo'=>1, 'Data'=>$today]);
      }
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
  }

  function update_deslike($id_coment, $id_user, $type){
    try{
      $today = date('Y-m-d H:i:s');
      $consulta = ($type == '1'? 'Feedback_Comment_PF' : 'Feedback_Comment_DP');
      
      $q = DBA::select($consulta, [], ['ID_origem_perfil'=>$id_user, 'ID_coment'=>$id_coment], []);
      
      if($r = DBA::fetch($q)){
        DBA::delete($consulta, ['ID_origem_perfil'=>$id_user,'ID_coment'=>$id_coment, 'Tipo'=>0]);
      }else{
        DBA::insert($consulta, ['ID_origem_perfil'=>$id_user, 'ID_coment'=>$id_coment, 'Tipo'=>0, 'Data'=>$today]);
      }
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
  }

  function is_dp_comentada($uid, $dp_id){
    try{
      return DBA::exists('Comment_DP', ['ID_origem_perfil'=>$uid, 'ID_disciplina'=>$dp_id]);
    }catch(Exception $e){
      Logger::debug($e->getMessage());
    }
    return false;
  }

  function post_coment($id_origem, $id_destino, $coment, $dp){
    try{
        //Pegar a categoria
        //Insere o ID da categoria no comentario
        $today = date('Y-m-d H:i:s');

        $id = Disciplina\get_categoria_id_fromDP($dp);
        Recomendador\marcar_disciplina_indef($id_destino, $dp); //aluno fez a disciplina logo ele nao fara ela de novo
        DBA::insert('Comment_PF', ['ID_origem_perfil'=>$id_origem, 'ID_destino'=> $id_destino, 'Comentario'=>$coment, "Tag_ID" => $id, 'Data'=>$today]);
        //DBA::insert()
    }catch(Exception $e){
        Logger::debug($e->getMessage());
    }
  }

  function post_coment_dp($id_origem, $id_dp, $star, $coment){
    try {
      $today = date('Y-m-d H:i:s');
      Recomendador\marcar_disciplina_indef_id($id_origem, $id_dp);
      
      if(DBA::exists('Comment_DP', ['ID_origem_perfil'=>$id_origem, 'ID_disciplina'=>$id_dp])){
        DBA::update('Comment_DP', ['Estrelas'=>$star, 'Comentario'=>$coment, 'Data'=>$today], ['ID_origem_perfil'=>$id_origem, 'ID_disciplina'=>$id_dp]);
      }else{
        DBA::insert('Comment_DP', ['ID_origem_perfil'=>$id_origem, 'ID_disciplina'=>$id_dp, 'Estrelas'=>$star, 'Comentario'=>$coment, 'Data'=>$today]);
      }

      Disciplina\update_evaluation($id_dp);
    } catch(Exception $e){
      Logger::debug($e->getMessage());
    }
  }
?>