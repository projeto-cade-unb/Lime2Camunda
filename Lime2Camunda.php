<?php
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
            'help' => 'Informe a URL do Engine-Rest do Camunda Server. Exemplo: http://localhost:8080/engine-rest/'
        ),
        'usercamunda' => array(
            'type' => 'string',
            'label' => 'Usuário no Camunda',
            'help' => 'Informe e usuario de login no camunda com suporta a instânciar processos. exemplo: demo'
        ),
        'passcamunda' => array(
            'type' => 'string',
            'label' => 'Senha do Usuário Camunda',
	    'help' => 'informe a senha do usuário que ira inst6anciar processos. exemplo: demo'
        ),
        'debugresponse' => array(
            'type' => 'boolean',
            'label' => 'Debug no final do Questionário?',
            'help' =>  'Se habilitado permitirá o respondente do questionário visualizar todos dados enviados para o camunda inclusive o retorno do Start Process'
        ),
    );
    
    public function init()
    {
        $this->subscribe('afterSurveyComplete', 'StartProcessCamunda');
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

		$dataVar = array(
        	       "variables"=> array(
        	       "definitionkey"=> array("value"=>$definitionkey,"type"=>"String")
		     )
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
	      foreach ($resultCamundaArray as $name => $value)
       		 {
                     if ( $name == "links" ) { 
                          foreach ( (array)$value as $name2 => $value2) {
	                          foreach ( (array)$value2 as $name3 => $value3) {
					if ( $name3 == "href") {
                                               $linkcamunda = str_replace("engine-rest/process-instance","camunda/app/cockpit/default/#/process-instance",$value3);
					       $event->getContent($this)
 				      		      ->addContent("<a href='" . $linkcamunda . "'> Clique aqui para Camunda acessar o Cookipt da Instância gerada</a>");
                                       }
				  }
                         }
                    }
		 }
	

	}

    }

}
