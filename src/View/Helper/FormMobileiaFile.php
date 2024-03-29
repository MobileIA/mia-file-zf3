<?php

namespace MIAFile\View\Helper;
/**
 * Description of FormMobileiaFile
 *
 * @author matiascamiletti
 */
class FormMobileiaFile extends \Zend\View\Helper\AbstractHelper
{
    /**
     *
     * @var \Zend\View\Helper\HeadScript
     */
    protected $headScript;
    /**
     *
     * @var \Zend\View\Helper\EscapeHtmlAttr
     */
    protected $escapeHtmlAttr;
    /**
     *
     * @var \Zend\View\Helper\BasePath
     */
    protected $basePath;
    /**
     * Verifica si ya se agrego el script en el layout
     * @var boolean
     */
    protected $hasScript = false;
    /**
     * 
     * @param \Zend\View\Helper\HeadScript $headScript
     * @param \Zend\View\Helper\EscapeHtmlAttr $escapeHtmlAttr
     * @param \Zend\View\Helper\BasePath $basePath
     */
    public function __construct($headScript, $escapeHtmlAttr, $basePath = null)
    {
        $this->headScript = $headScript;
        $this->escapeHtmlAttr = $escapeHtmlAttr;
        $this->basePath = $basePath;
    }
    /**
     * 
     * @param \Backend\Form\Element\MobileiaFile $element
     */
    public function __invoke($element)
    {
        // Verificamos si se agrego el script principal
        if(!$this->hasScript){
            // Insertamos la funcion principal
            $this->insertFunctionScript();
            // Guardamos que ya se incluyo
            $this->hasScript = true;
        }
        // Generamos onChange del elemento
        $this->insertChangeScript($element);
        // Generamos el HTML
        return $this->render($element);
    }
    /**
     * 
     * @param \Backend\Form\Element\MobileiaFile $element
     * @return string
     */
    protected function render($element)
    {
        // Creamos el input visible para seleccionar archivo
        $html = '<input id="'.$element->getName().'_file" type="file" class="form-control">';
        // Creamos input para almacenar los datos
        $html .= '<input id="'.$element->getName().'" name="'.$element->getName().'" type="hidden" value="'.$this->escapeHtmlAttr->__invoke($element->getValue()).'">';
        // Creamos html para los mensajes
        $html .= '<span id="'.$element->getName().'_msg" class="help-block" style="display: none;">Subiendo...</span>';
        // Devolvemos HTML
        return $html;
    }
    
    protected function insertChangeScript($element)
    {
        $this->headScript->appendScript('var '.$element->getName().'Val = ' .$element->getValue());
        $this->headScript->appendScript('if('.$element->getName().'Val != ""){
            $("#'.$element->getName().'_msg").show();
            $("#'.$element->getName().'_msg").html("Cargado exitosamente: " + '.$element->getName().'Val.filename);
}');
        $this->headScript->appendScript('$("#'.$element->getName().'_file").change(function(){ mobileiaFileFunc(4, "'.$element->getName().'"); });');
    }
    
    protected function insertFunctionScript()
    {
        $this->headScript->appendScript('function mobileiaFileFunc(appId, elementId){
    // Adjuntamos datos del archivo seleccionado
    var file = $("#"+elementId+"_file").prop("files")[0];
    // Mostrar mensaje de cargando
    $("#"+elementId+"_msg").show();
    $("#"+elementId+"_msg").html("Cargando archivo...");
    // Generate File name
    var now = new Date();
    var filenameDef = now.getMilliseconds() + "_" + now.getFullYear() + now.getMonth() + now.getDay() + now.getHours() + "_" + file.name.replace(/ /g, "");
    // Llamada al servidor
    $.ajax({
        url: "https://storage.googleapis.com/upload/storage/v1/b/gulch-files-public/o?uploadType=media&name=" + filenameDef,
        type: "POST",
        data:  file,
        contentType: false,
        cache: false,
        processData:false,
        success: function(data){
            // Resetear input  
            $("#"+elementId+"_file").val("");
            // Mostramos mensaje de que se cargo correctamente
            $("#"+elementId+"_msg").html("Cargado exitosamente: " + data.name);
            // Cargamos datos en el input oculto
            $("#"+elementId).val(JSON.stringify({
                id: data.generation,
                url: "https://storage.googleapis.com/gulch-files-public/" + data.name,
            }));
        }	        
   });
}');
    }
}