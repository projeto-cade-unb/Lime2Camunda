[<img src="assets/logo.png">](https://github.com/projeto-cade-unb/Lime2Camunda)
# Lime2Camunda
Limesurvey Surveys for Camunda BPM Integration

## Features
This plugin allows a process to be started in camunda after filling out a Limesurvey and all questions are sent as variables to camunda.


## Install
The same is a Limesurvey plugin and follow all your recommendations so to install just copy the project to the Limesurvey plugins folder and access the plugin manager to configure and enable. 

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
   - Add user and password in survey
   - Add send to camunda in question parameters.
   - remove question DEFINITIONKEY and add parameter in Survey


## Configs Surveys

 * Add One question in first position your survey with code DEFINITIONKEY and add your CAMUNDA DEFINITION KEY for Default value this Question.
 * Hidden this question.

# Credits 
  Marcio Junior Vieira - Projeto CADE UNB.

# License 
  GLP V3
