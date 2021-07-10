<?php
/**
 * Lime2Camunda: Integration Surveus LimeSurvey with Camunda Server 
 *
 * @author Marcio Junior Vieira <marcio@ambientelivre.com.br>
 * @copyright 2021-2021 - Projeto UNB Cade

 * @license GPL v3
 * @version 0.01
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

class Lime2Camunda extends PluginBase {

    protected $storage = 'DbStorage';
    static protected $name = 'Lime2Camunda';
    static protected $description = 'Limesurvey Surveys 2 Camunda BPM';
    
    protected $settings = array(
	'logo' => array(
	          'type' => 'logo',
	          'path' => 'assets/logo.png'
	),
        'urlrestcamunda' => array(
            'type' => 'string',
            'label' => 'REST URL Camunda',
            'help' => 'Informe a URL do Engine-Rest do Camunda Server. Exemplo: http://localhost:8080/engine-rest/',
	    'default' => 'http://localhost:8080/engine-rest/'
        ),
        'usercamunda' => array(
            'type' => 'string',
            'label' => 'Usuário no Camunda',
            'help' => 'Informe e usuario de login no camunda com suporta a instânciar processos. exemplo: demo',
	    'default' => 'demo'
        ),
        'passcamunda' => array(
            'type' => 'password',
            'label' => 'Senha do Usuário Camunda',
	    'help' => 'informe a senha do usuário que ira instânciar processos. exemplo: demo',
	    'default' => 'demo'
        ),
        'debugresponse' => array(
            'type' => 'boolean',
            'label' => 'Debug no final do Questionário?',
            'help' =>  'Se habilitado permitirá o respondente do questionário visualizar todos dados enviados para o camunda inclusive o retorno do Start Process',
	    'default' => TRUE
        ),
    );

    public function init()
    {
        //$this->subscribe('onPluginRegistration');
        $this->subscribe('afterSurveyComplete', 'StartProcessCamunda');
        $this->subscribe('newSurveySettings');
        //$this->subscribe('beforeSurveySettingsSave');
        $this->subscribe('beforeSurveySettings');
    }

    public function StartProcessCamunda()
    {
	$event      = $this->getEvent();
        $surveyId   = $event->get('surveyId');
        $responseId = $event->get('responseId');
        $urlrestcamunda = $this->get('urlrestcamunda');
        $usercamunda = $this->get('usercamunda');
        $passcamunda = $this->get('passcamunda');
        $debugresponse = $this->get('debugresponse');
        $basicAuth64 = "Basic " . base64_encode($usercamunda.":".$passcamunda);

        $bMapQuestionCodes = true;
        $response = $this->pluginManager->getAPI()->getResponse($surveyId,$responseId,$bMapQuestionCodes);

        if ($debugresponse) {
	        $event->getContent($this)
        	      ->addContent('Todas Respostas do Questionário Camunda<br/><pre>' . print_r($response, true) . '</pre>');
        }
        
        foreach ($response as $name => $value)
        {

         if ($name == "DEFINITIONKEY") {
                $definitionkey = $value; 
		$dataVar = array( "variables"=> array(
        	                  "definitionkey"=> array("value"=>$definitionkey,"type"=>"String"))
				);
	        if ($debugresponse) {
		        $event->getContent($this)
		              ->addContent('Definition key encontrada no Questionario <br/><pre>' . print_r($value, true) . '</pre>');
		}                
         }
         elseif ($definitionkey != "" and $name != "lastpage") {
             $dataVar['variables'][$name]  = array("value"=>$value,"type"=>"String"); 
         }
        }

        if ($debugresponse) {
	        $event->getContent($this)
	              ->addContent('Variáveis filtradas que serão enviadas para iniciar processo no Camunda<br/><pre>' . print_r($dataVar, true) . '</pre>');
	}

        // Start Process Camunda 
        if ( $definitionkey != "" ) {
        	$data_string = json_encode($dataVar);	
		$ch = curl_init($urlrestcamunda . 'process-definition/key/' . $definitionkey . '/start');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
        	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		            'Content-Type: application/json',
		            'Content-Length: ' . strlen($data_string),
		            'Authorization:' . $basicAuth64)
	       	 );
 
	       $resultCamunda = curl_exec($ch);
 	       if ($debugresponse) {
		       $event->getContent($this)
 	      		      ->addContent('Resultado do EndPoint da Camunda com numero da instância gerada:<br/><pre>' . print_r($resultCamunda, true) . '</pre>');
		}

              $resultCamundaArray = json_decode($resultCamunda);
	      foreach ($resultCamundaArray as $name => $value) {
                     if ( $name == "links" ) { 
                          foreach ( (array)$value as $name2 => $value2) {
	                          foreach ( (array)$value2 as $name3 => $value3) {
					if ( $name3 == "href") {
                                               $linkcamunda = str_replace("engine-rest/process-instance","camunda/app/cockpit/default/#/process-instance",$value3);
				 	       if ($debugresponse) {
						       $event->getContent($this)
 				      		 	     ->addContent("<a href='" . $linkcamunda . "'> Clique aqui para acessar o Camunda Cookipt e visualizar a Instância gerada</a>");
						}
                                       }
				  }
                          }
                     }
		}
	}

    }

    public function beforeSurveySettings()  {
            $pluginsettings = $this->getPluginSettings(true);

            $event = $this->getEvent();
            $event->set("surveysettings.{$this->id}", array(
                'name' => get_class($this),
                'settings' => array(
                    'definitionkey' => array(
                        'type' => 'string',
                        'label' => 'Info the Definition Key Process:',
                        'help' => 'See this Key in Camunda Cookpit Deployments or in Camunda Modeler, Sample in Project SEI: SEI_100000512',
                        'current' => $this->get('definitionkey', 'Survey', $event->get('survey'))
                    ),
		   'usercamundasurvey' => array(
		        'type' => 'string',
        	        'label' => 'Usuário no Camunda',
          		'help' => 'Informe e usuario de login no camunda com suporta a instânciar processos. Se vazio será usada config Global.',
		        'current' => $this->get('usercamundasurvey', 'Survey', $event->get('survey'))
	            ),
                   'passcamundasurvey' => array(
                        'type' => 'password',
                        'label' => 'Senha do Usuário Camunda',
              	        'help' => 'informe a senha do usuário que ira instânciar processos. Se vazio será usada config Global.',
		        'current' => $this->get('passcamundasurvey', 'Survey', $event->get('survey'))
                   )
                )
            ));
     }

    public function newSurveySettings()
    {
        $event = $this->event;
        foreach ($event->get('settings') as $name => $value)
        {
            // In order use survey setting, if not set, use global, if not set use default 
            $default=$event->get($name,null,null,isset($this->settings[$name]['default'])?$this->settings[$name]['default']:NULL);
            $this->set($name, $value, 'Survey', $event->get('survey'),$default);
        }
    }

/*
    public function beforeSurveySettings()
    {
        $event = $this->event;
        $event->set("surveysettings.{$this->id}", array(
            'name' => get_class($this),
            'settings' => array(
             'sendcamunda'=>array(
                    'type'=>'select',
                    'label'=>'A select setting',
                    'options'=>array(
                        '0'=>'No',
                        '1'=>'Yes',
                    ),
                    'help'=>'A select setting, need an array of option.',
                    'current' => $this->get('sendcamunda', 'Survey', $event->get('survey'),$this->get('select',null,null,$this->settings['select']['default'])),
                ),
            )
         ));
    }
*/

/*
 public function onPluginRegistration(){
    $pluginName = get_class();
    tracevar($pluginName); // This don't see to happen
    $thisMenu = Surveymenu::model()->find("title = :pluginName",array(":pluginName"=>$pluginName));
    if(!$thisMenu){
      //Check if the menu/menu entries are already created use yii database methods for that
      
      $menuArray = array(
          "parent_id" => 1, //1 -> main surveymenu, 2-> quickemenu, NULL -> new base menu in sidebar
          "title" => $pluginName,
          "position" => "side", // possible positions are "side" and "collapsed" state 3.0.0.beta-2
          "description" => "[your plugins menu description]"
      );
      $newMenuId = Surveymenu::staticAddMenu($menuArray);

      // repeat this as often as you need it
      $menuEntryArray = array(
          "name" => "[name of the action]",
          "title" => "[title for the action]",
          "menu_title" => "[title for the action]", 
          "menu_description" => "[description for the action]",
          "menu_icon" => "[icon for action]", //it is either the fontawesome classname withot the fa- prefix, or the iconclass classname, or a src link
          "menu_icon_type" => "fontawesome", // either 'fontawesome', 'iconclass' or 'image'
          "menu_link" => "[admin/]controller/sa/action", //the link will be parsed through yii's createURL method
          "addSurveyId" => true, //add the surveyid parameter to the url
          "addQuestionGroupId" => true, //add gid parameter to url
          "addQuestionId" => true, //add qid parameter to url
          "linkExternal" => false, //open link in a new tab/window
          "hideOnSurveyState" => null, //possible values are "active", "inactive" and null
          "manualParams" => "" //please read up on this setting as it may render the link useless
      );
      SurveymenuEntries::staticAddMenuEntry($newMenuId, $menuEntryArray);
    }

  }

*/






}
