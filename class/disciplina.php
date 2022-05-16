<?php
    namespace Disciplina;

    use Friendica\DI;

    use Friendica\Core\L10n;
    use Friendica\Core\Logger;
    use Friendica\Database\DBA;
    use Friendica\Database\Database;

    function get_categoria_id_fromDP($dp){
        try{
            $rq = get_disciplina_by_name($dp);
            if($rq){
                $q = DBA::p("SELECT ID_Tag FROM Link_Tag WHERE ID_disciplina = ? ORDER BY RAND() LIMIT 1", $rq['ID']);
                if($r = DBA::fetch($q)){
                    return $r['ID_Tag'];
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return -1;
    }

    function enter_in_buble($id_user){
        $arr_ids = [];
        try{
            //checar as categorias mais comentas no seu perfil e inserir na bolha
            $q = DBA::select('Comment_PF', ['Tag_ID'], ['ID_destino'=>$id_user]);
            while($r = DBA::fetch($q)){
                array_push($arr_ids, $r['Tag_ID']);
            }
            //print_r($arr_ids);
            if(count($arr_ids) >= 1){
                $arr_values = array_count_values($arr_ids);
                $v_m = max($arr_values);
                foreach($arr_values as $chave => $valor){
                    if($v_m >= $valor && $valor != -1){
                        DBA::insert('Bolha_recomendados', ['ID_Tag'=>$chave, 'ID_origem_perfil'=>$id_user], Database::INSERT_IGNORE);
                    }
                }

                //Pegar as disciplinas mais bem avaliadas pelo usuario
                $q = DBA::select('Feedback_Comment_DP', ['Estrelas'], ['ID_origem_perfil'=>$id_user]);
                $arr_stars = [];
                while($r = DBA::fetch($q)){
                    array_push($arr_stars, $r['Estrelas']);
                }
                if(!empty($arr_stars)){
                    $max_star = max($arr_stars);
                    $q = DBA::p('SELECT t2.ID_Tag FROM Feedback_Comment_DP as t1, Link_Tag as t2 WHERE t1.ID_disciplina = t2.ID_disciplina AND t1.ID_origem_perfil = ? AND t1.Estrelas >= ?',$id_user, $max_star);
                    while($r = DBA::fetch($q)){
                        DBA::insert('Bolha_recomendados',['ID_Tag'=>$r['ID_Tag'],'ID_origem_perfil'=>$id_user], Database::INSERT_IGNORE);
                    }
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function install_dps(){
        try{
            //Disciplinas Dpto CIC
            insert_dp(['Nome'=>'Algoritmos e Programação de Computadores', 'Descricao'=>'Disciplina APC'], ['CiC']);
            insert_dp(['Nome'=>'Programação Concorrente', 'Descricao'=>'Disciplina Programação Concorrente'] ,['CiC']);
            insert_dp(['Nome'=>'Sistemas Operacionais', 'Descricao'=>'Disciplina SO'] ,['CiC']);
            insert_dp(['Nome'=>'Introdução a Inteligência Articial', 'Descricao'=>'Disciplina sobre IA'], ['CiC']);
            insert_dp(['Nome'=>'Fundamentos Teóricos da Computação', 'Descricao'=>'Disciplina de Fundamentos Teóricos da Computação'], ['CiC']);
            insert_dp(['Nome'=>'Topicos avançados em Nuvem Computacional', 'Descricao'=>'Disciplina de Nuvem Computacional'], ['CiC']);
            insert_dp(['Nome'=>'Estrutura de Dados', 'Descricao'=>'Disciplina de ED'], ['CiC']);
            insert_dp(['Nome'=>'Sistemas de Informação', 'Descricao'=>'Disciplina de SI'], ['CiC']);
            insert_dp(['Nome'=>'Introdução a Desenvolvimento de Jogos', 'Descricao'=>'Disciplina de Introdução a Jogos'], ['CiC']);
            insert_dp(['Nome'=>'Linguagens de Programação', 'Descricao'=>'Disciplina de LP'], ['CiC']);
            
            //Disciplinas Dpto Matematica
            insert_dp(['Nome'=>'Calculo 1', 'Descricao'=>'Disciplina Calculo'], ['Matematica']);
            insert_dp(['Nome'=>'Calculo 2', 'Descricao'=>'Disciplina Calculo'], ['Matematica']);
            insert_dp(['Nome'=>'Calculo 3', 'Descricao'=>'Disciplina Calculo'], ['Matematica']);
            insert_dp(['Nome'=>'Introdução a Algebra Linear', 'Descricao'=>'Disciplina IAL'], ['Matematica']);
            insert_dp(['Nome'=>'Algebra 1', 'Descricao'=>'Disciplina Algebra 1'], ['Matematica']);
            insert_dp(['Nome'=>'Teoria dos Numeros 1', 'Descricao'=>'Disciplina Teoria dos Numeros'], ['Matematica']);
            insert_dp(['Nome'=>'Algebra Linear', 'Descricao'=>'Disciplina Algebra Linear'], ['Matematica']);
            insert_dp(['Nome'=>'Calculo Numérico', 'Descricao'=>'Disciplina Calculo Numérico'], ['Matematica']);
            insert_dp(['Nome'=>'Introdução a Teoria dos Grafos', 'Descricao'=>'Disciplina Grafos'], ['Matematica']);
            insert_dp(['Nome'=>'Geometria Analítica Para Matemática', 'Descricao'=>'Disciplina Geometria'], ['Matematica']);

            //Disciplinas Dpto Economia
            insert_dp(['Nome'=>'Introdução a Economia', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            insert_dp(['Nome'=>'Formação Econômica do Brasil', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            insert_dp(['Nome'=>'Econometria', 'Descricao'=>'Disciplina de Econometria'], ['Economia']);
            insert_dp(['Nome'=>'Economia Industrial', 'Descricao'=>'Disciplina de Economia Industrial'], ['Economia']);
            insert_dp(['Nome'=>'Economia do setor público', 'Descricao'=>'Disciplina de Economia Setor Publico'], ['Economia']);
            insert_dp(['Nome'=>'Economia Monetária', 'Descricao'=>'Disciplina de Economia Monetaria'], ['Economia']);
            insert_dp(['Nome'=>'Teoria do Desenvolvimento Econômico', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            insert_dp(['Nome'=>'Microeconomia Ambiental', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            insert_dp(['Nome'=>'Economia Quantitativa 1', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            insert_dp(['Nome'=>'Contabilidade Nacional', 'Descricao'=>'Disciplina de Economia'], ['Economia']);
            //Disciplinas Dpto Linguas

            insert_dp(['Nome'=>'Pesquisa em Tradução', 'Descricao'=>'Disciplina de Pesquisa em Traducao'], ['Linguas']);
            insert_dp(['Nome'=>'Ensino de Espanhol como lingua estrangeira', 'Descricao'=>'Disciplina de Espanhol'], ['Linguas']);
            insert_dp(['Nome'=>'Prática Italiano Oral e Escrito', 'Descricao'=>'Disciplina de Italiano'], ['Linguas']);
            insert_dp(['Nome'=>'Tradução de Textos Literários', 'Descricao'=>'Disciplina de Literatura'], ['Linguas']);
            insert_dp(['Nome'=>'Versão de Textos Literários', 'Descricao'=>'Versão de Textos Literários'], ['Linguas']);
            insert_dp(['Nome'=>'Lingua Neerlandesa(Holandês) 1', 'Descricao'=>'Disciplina de Linguas'], ['Linguas']);
            insert_dp(['Nome'=>'Língua Neerlandesa(Holandês) 2', 'Descricao'=>'Disciplina de Linguas'], ['Linguas']);
            insert_dp(['Nome'=>'Laboratório de Texto 1', 'Descricao'=>'Disciplina de Texto'], ['Linguas']);
            insert_dp(['Nome'=>'Teoria da Tradução 1', 'Descricao'=>'Disciplina de Traducao'], ['Linguas']);
            insert_dp(['Nome'=>'Japonês 1', 'Descricao'=>'Disciplina de Japones'], ['Linguas']);
            //Disciplinas Dpto Musica

            insert_dp(['Nome'=>'Instrumento Principal Violino 1', 'Descricao'=>'Disciplina de Violino'], ['Musica']);
            insert_dp(['Nome'=>'Harmonia na Música Popular 1', 'Descricao'=>'Disciplina de Harmonia'], ['Musica']);
            insert_dp(['Nome'=>'Instrumento Principal Piano 1', 'Descricao'=>'Disciplina de Piano'], ['Musica']);
            insert_dp(['Nome'=>'Música de Tradição Oral Performática', 'Descricao'=>'Disciplina de Música'], ['Musica']);
            insert_dp(['Nome'=>'Instrumento Principal Clarineta 1', 'Descricao'=>'Disciplina de Clarineta'], ['Musica']);
            insert_dp(['Nome'=>'Instrumento Principal Canto 1', 'Descricao'=>'Disciplina de Canto'], ['Musica']);
            insert_dp(['Nome'=>'Instrumento Principal Trompete 1', 'Descricao'=>'Disciplina de Trompete'], ['Musica']);
            insert_dp(['Nome'=>'Instrumento Principal Guitarra 1', 'Descricao'=>'Disciplina de Guitarra'], ['Musica']);
            insert_dp(['Nome'=>'Instrumentação e Orquestração 1', 'Descricao'=>'Disciplina de Orquestra'], ['Musica']);
            insert_dp(['Nome'=>'Canto Coral 1', 'Descricao'=>'Disciplina de Canto'], ['Musica']);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function insert_dp($dp, $categories){
        try{
            DBA::insert('Disciplinas', ['Nome'=>$dp['Nome'], 'Descricao'=>$dp['Descricao'],'Tipo'=>1, 'Avaliacao'=>0]);
            $r2 = DBA::selectFirst('Disciplinas',[], ['Nome'=>$dp['Nome']]);
    
            foreach($categories as $category){
                $r = DBA::selectFirst('Categorias',[], ['Tag'=>$category]);
                Logger::debug('Interando loop de categorias');
                if(DBA::isResult($r) && DBA::isResult($r2)){
                    Logger::debug('Entrando no link_tag');
                    DBA::insert('Link_Tag', ['ID_disciplina'=>$r2['ID'], 'ID_Tag'=>$r['ID']]);
                }
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function uninstall_dps(){
        try{
            DBA::e('TRUNCATE TABLE Disciplinas');
            DBA::e('TRUNCATE TABLE Feedback_Comment_DP');
            DBA::e('TRUNCATE TABLE Comment_PF');
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function install_categories(){
        try{
            DBA::insert('Categorias', ['Tag'=>'CiC']);
            DBA::insert('Categorias', ['Tag'=>'Matematica']);
            DBA::insert('Categorias', ['Tag'=>'Economia']);
            DBA::insert('Categorias', ['Tag'=>'Musica']);
            DBA::insert('Categorias', ['Tag'=>'Linguas']);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function uninstall_categories(){
        try{
            DBA::e("TRUNCATE TABLE Categorias");
            DBA::e("TRUNCATE TABLE Link_Tag");
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function get_disciplina_by_id($id){
        $value = [];
        try{
            $r = DBA::selectFirst('Disciplinas', [], ['ID'=>$id]);
            if(DBA::isResult($r)){
                return $r;
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return $value;
    }

    function get_disciplina_by_name($name){
        $value = [];
        try{
            $r = DBA::selectFirst('Disciplinas', [], ['Nome'=>$name]);
            if(DBA::isResult($r)){
                return $r;
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
        return $value;
    }

    function get_tag_id($dp_id){
        try{
            $r = DBA::selectFirst('Link_Tag',['ID_Tag'], ['ID_disciplina'=>$dp_id]);
            if(DBA::isResult($r)){
                return $r['ID_Tag'];
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function update_evaluation($id_dp){
        try{
            $q = DBA::select('Feedback_Comment_DP',['Estrelas'], ['ID_disciplina'=>$id_dp]);
            $count = 0;
            $stars = 0;
            while($r = DBA::fetch($q)){
                $count++;
                $stars += $r['Estrelas'];
            }
            if($count == 0){
                $stars = 0;
            } else {
                $stars = $stars/$count;
            }
            DBA::update('Disciplinas', ['Avaliacao'=>$stars], ['ID'=>$id_dp]);
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }
    }

    function get_list(){
        $dps = [];
        try {
            $q = DBA::select('Disciplinas', []);
            while($r = DBA::fetch($q)){

                $dps[] = [
                    'ID'=>$r['ID'],
                    'Nome'=>$r['Nome'],
                    'Descricao'=>$r['Descricao'],
                    'ID_Tag'=> get_tag_id($r['ID'])
                ];
            }
        }catch(Exception $e){
            Logger::debug($e->getMessage());
        }

        return $dps;
    }
?>