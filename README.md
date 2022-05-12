# Recomendation Addon
Addon que realiza recomendações de disciplinas na rede social Friendica desenvolvido em PHP.

# Instalação Addon

Navegue até a pasta de addons da sua instalação friendica e utilize o comando:

`git clone https://github.com/RenanZX/RecomendationGOServer.git recomendapp`

## Instalando tabelas do Banco de Dados

Instale o MariaDB, conecte ao banco de dados e utilize o comando:

`source caminho-para-o-addon/config.sql`

## Limpando tabelas de feeds

Caso seja necessário, é possivel limpar todas as tabelas relacionadas aos feedbacks utilizando o comando:

`source caminho-para-o-addon/clear_tables_feed.sql`

## Instalando o Addon no friendica

Após a instalação das tabelas e do repositório, navegue até o friendica como administrador, e selecione o addon para a instalação, será instalado todas as dependências necessárias para a sua utilização.