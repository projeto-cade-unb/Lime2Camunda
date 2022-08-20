# Lime2Camunda
Limesurvey Surveys for Camunda BPM Plataform Integration

[<img src="assets/logo.png">](https://github.com/projeto-cade-unb/Lime2Camunda)

## Translate
[Portuguese] (https://github.com/projeto-cade-unb/Lime2Camunda/blob/main/README.pt-BR.md)

[English] (https://github.com/projeto-cade-unb/Lime2Camunda/REAMDE.md)

## Features
This plugin allows a process to be started in camunda after filling out a Limesurvey and all questions are sent as variables to Camunda.


## Install
The same is a Limesurvey plugin and follow all your recommendations.

* Stable versions:
 download releases in https://github.com/projeto-cade-unb/Lime2Camunda/releases
  copy the release in extract to the Limesurvey plugins folder and access the plugin manager to configure and enable.

* For developer version:
  to install just copy the project to the Limesurvey plugins folder and access the plugin manager to configure and enable.

## Configs plugin

urlrestcamunda = Enter Your Camunda Server EndPoint URL  Sample: http://localhost:8080/engine-rest/

usercamunda = Your user in Camunda Server. Sample: demo

passcamunda = Your password in Camunda Server. Sample: demo

debugresponse = Displays a debug screen at the end of the survey if TRUE.

## ScreenShots

### ScreenShot Config

[<img src="assets/screenshot-config1.png">]()

### ScreenShot Debug

[<img src="assets/screenshot-debug.png">]()


### New features in RoadMap 0.02
   - Refactoring in code - OK
   - Add user and password in survey
   - Add send to camunda in question parameters.
   - remove question DEFINITIONKEY and add parameter in Survey

### New features in RoadMap 0.03
  - Start process in GRPC (Camunda Plataform 8)

## Configs Surveys

 * Add One question in first position your survey with code DEFINITIONKEY and add your CAMUNDA DEFINITION KEY for Default value this Question.
 * Hidden this question.

# Credits
* [Marcio Junior Vieira](https://www.linkedin.com/in/mvieira1/) - Dev - Projeto CADE UNB.
* [Vinícius Eloy](https://www.linkedin.com/in/vinicius-eloy-a28b203/) - Mentor - Projeto CADE UNB.
* [Denise Mitie Taketomi](http://lattes.cnpq.br/4150411752944887) - Coordenadora - Projeto CADE UNB.

Universidade de Brasília - Latitude - Laboratório de Tecnologias da Tomada de Decisão
read more about Latitude Laboratory(https://www.latitude.unb.br/)
[<img src="assets/latitude.png">]()

# License
  [License GPL v3.0](https://github.com/projeto-cade-unb/LICENSE.GPLv3)
