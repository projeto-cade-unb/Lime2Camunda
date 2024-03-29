[<img src="assets/logo.png">](https://github.com/projeto-cade-unb/Lime2Camunda)
# Lime2Camunda
Integração de formulários Limesurvey com Camunda BPM Plataform.

## Tradução
[Português] (https://github.com/projeto-cade-unb/Lime2Camunda/blob/main/README.pt-BR.md)

[Inglês] (https://github.com/projeto-cade-unb/Lime2Camunda/REAMDE.md)

## Funcionalidades
Este plugin permite que um processo seja iniciado na camunda após o preenchimento de um Limesurvey e todas as perguntas são enviadas como variáveis para a Camunda.

## Instalação
O mesmo é um plugin Limesurvey e siga todas as suas recomendações.

* Versões estáveis:
 Baixe as releases em https://github.com/projeto-cade-unb/Lime2Camunda/releases copie a versão descompactada para a pasta de plugins do Limesurvey e acesse o gerenciador de plugins para configurar e habilitar.

* Versão do desenvolvedor:
Para instalar basta copiar o projeto para a pasta de plugins do Limesurvey e acessar o gerenciador de plugins para configurar e habilitar.

## Configuração do Plugin

urlrestcamunda = Digite o exemplo de URL do seu Camunda Server EndPoint: http://localhost:8080/engine-rest/

usercamunda = Seu usuário no Camunda Server. Exemplo: demo

passcamunda = Sua senha no Servidor Camunda. Exemplo demo

debugresponse = Exibe uma tela de depuração no final da pesquisa se TRUE.

## Screenshots

### Screenshot Config

[<img src="assets/screenshot-config1.png">]()

### Screenshot Debug

[<img src="assets/screenshot-debug.png">]()

### Novas funcionalidades - RoadMap 0.02
    - Refatoração de código - OK
    - Adicionar usuário e senha na pesquisa.
    - Adicionar enviar para camunda nos parâmetros da questão.
    - Remova a pergunta DEFINITIONKEY e adicione o parâmetro na pesquisa.

## Configurações de formulário

* Adicione uma pergunta na primeira posição da sua pesquisa com o código DEFINITIONKEY e adicione sua CAMUNDA DEFINITION KEY para o valor padrão desta pergunta.

* Escondido esta pergunta.

# Créditos
* [Marcio Junior Vieira](https://www.linkedin.com/in/mvieira1/) - Dev - Projeto CADE UNB.
* [Vinícius Eloy](https://www.linkedin.com/in/vinicius-eloy-a28b203/) - Mentor - Projeto CADE UNB.
* [Denise Mitie Taketomi](http://lattes.cnpq.br/4150411752944887) - Coordenadora - Projeto CADE UNB.

Universidade de Brasília - Latitude - Laboratório de Tecnologias da Tomada de Decisão
read more about Latitude Laboratory(https://www.latitude.unb.br/)
[<img src="assets/latitude.png">]()

# Licença
  [License GPL v3.0](https://github.com/projeto-cade-unb/LICENSE.GPLv3)
